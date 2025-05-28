<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MoloniInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\MoloniItem;
use App\Services\MoloniService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MoloniNewInvoiceController extends Controller
{

    protected $moloni;

    public function __construct(MoloniService $moloni)
    {
        $this->moloni = $moloni;
    }

    public function index()
    {
        return view('admin.moloniNewInvoices.index');
    }

    public function redirectToMoloni()
    {
        $url = 'https://api.moloni.pt/v1/authorize/?client_id=' .
            env('MOLONI_CLIENT_ID') .
            '&redirect_uri=' . urlencode(env('MOLONI_CALLBACK_URL'));

        return redirect()->away($url);
    }

    public function moloniCallback(Request $request)
    {
        $code = $request->get('code');

        $response = Http::asForm()->post('https://api.moloni.pt/v1/grant/', [
            'grant_type' => 'authorization_code',
            'client_id' => env('MOLONI_CLIENT_ID'),
            'client_secret' => env('MOLONI_CLIENT_SECRET'),
            'code' => $code,
            'redirect_uri' => env('MOLONI_CALLBACK_URL'),
        ]);

        $data = $response->json();

        if (isset($data['access_token'])) {
            session(['moloni_access_token' => $data['access_token']]);
            return redirect()->route('admin.dashboard')->with('success', 'Autenticado com sucesso na Moloni!');
        }

        return redirect()->route('admin.dashboard')->with('error', 'Erro ao autenticar com a Moloni');
    }

    public function processOcr(MoloniInvoice $moloniInvoice)
    {
        try {
            $file = $moloniInvoice->file;

            if (!$file || !method_exists($file, 'getPath')) {
                return response()->json([
                    'success' => false,
                    'ocr' => '[OCR] Ficheiro não está disponível ou método getPath() não existe.'
                ]);
            }

            $filePath = $file->getPath();

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'ocr' => '[OCR] Ficheiro não encontrado: ' . $filePath
                ]);
            }

            $response = Http::attach(
                'file',
                file_get_contents($filePath),
                basename($filePath)
            )->post('https://api.ocr.space/parse/image', [
                'apikey' => env('OCR_API', 'CHAVE_INVALIDA'),
                'language' => 'por',
                'isOverlayRequired' => 'false',
                'OCREngine' => '2',
                'isTable' => 'true',
            ]);

            $data = $response->json();

            if (!isset($data['ParsedResults']) || !is_array($data['ParsedResults'])) {
                return response()->json([
                    'success' => false,
                    'ocr' => '[OCR] Resposta inesperada da API: ' . json_encode($data),
                ]);
            }

            $ocrText = '';
            foreach ($data['ParsedResults'] as $parsed) {
                if (isset($parsed['ParsedText'])) {
                    $ocrText .= $parsed['ParsedText'] . "\n";
                }
            }

            $moloniInvoice->ocr = $ocrText;
            $moloniInvoice->save();

            return response()->json([
                'success' => true,
                'ocr' => $ocrText,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'ocr' => '[OCR] Exceção capturada: ' . $e->getMessage(),
            ]);
        }
    }

    public function generateReferences(MoloniInvoice $moloniInvoice)
    {
        try {
            if (empty($moloniInvoice->ocr)) {
                return response()->json([
                    'success' => false,
                    'message' => '[IA] O conteúdo OCR está vazio. Processa o OCR primeiro.'
                ]);
            }

            $prompt = <<<EOT
Tens abaixo o conteúdo extraído por OCR de uma fatura.  
Gera uma lista em JSON com os seguintes campos por linha:
- fornecedor (usa exatamente "{$moloniInvoice->supplier}")
- fatura (usa exatamente "{$moloniInvoice->invoice}")
- referencia (por ex: "9Y0907253C", "PAA698151", etc.)
- nome (por ex: "1 conj. past. de travão", "Bateria", etc.)
- quantidade (valor numérico)
- preco (valor numérico ou null se não estiver indicado)

Se a referência não existir, usa null (não coloques "undefined").
Tenta manter a ordem dos itens conforme aparecem no texto.

Texto OCR:
"""{$moloniInvoice->ocr}"""
EOT;

            $response = OpenAI::chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => 'És um assistente de faturação que extrai tabelas de faturas em português.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.2,
            ]);

            $content = $response->choices[0]->message->content ?? '';

            \Log::debug('[IA] Conteúdo devolvido pela IA:', ['raw' => $content]);

            $cleanContent = trim($content);
            $cleanContent = preg_replace('/^```(?:json)?|```$/', '', $cleanContent);

            $json = json_decode($cleanContent, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($json)) {
                return response()->json([
                    'success' => false,
                    'message' => '[IA] JSON inválido',
                    'raw' => $cleanContent
                ]);
            }

            return response()->json([
                'success' => true,
                'referencias' => $json
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '[IA] Erro ao gerar referências: ' . $e->getMessage()
            ]);
        }
    }

    public function sync(Request $request)
    {
        $ids = $request->input('ids', []);

        $items = MoloniItem::with('moloni_invoice')->whereIn('id', $ids)->get()->groupBy('moloni_invoice_id');

        foreach ($items as $invoiceId => $group) {
            $invoice = $group->first()->moloni_invoice;

            try {
                $supplierId = $this->moloni->getOrCreateSupplier($invoice->supplier);
                $documentId = $this->moloni->createDocument($supplierId, $invoice->invoice);

                foreach ($group as $item) {
                    $productId = $this->moloni->getOrCreateProduct($item, env('MOLONI_PRODUCT_CATEGORY_ID'));
                    $this->moloni->addProductToDocument($documentId, $productId, $item);
                    $item->update(['synced' => true]);
                }

                $this->moloni->uploadDocumentFile($documentId, $invoice);
                $invoice->update(['handled' => true]);
            } catch (\Exception $e) {
                Log::error("Erro ao sincronizar fatura {$invoice->id}", [
                    'erro' => $e->getMessage(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => "Erro ao sincronizar fatura ID {$invoice->id}: {$e->getMessage()}"
                ], 500);
            }
        }

        return response()->json(['success' => true, 'message' => 'Faturas sincronizadas com sucesso!']);
    }
}
