<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MoloniInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Traits\Moloni;

class MoloniNewInvoiceController extends Controller
{
    use Moloni;

    public function index()
    {
        return view('admin.moloniNewInvoices.index');
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
        $resultados = [];

        foreach ($request->ids as $id) {

            $invoice = MoloniInvoice::with('moloni_items')->findOrFail($id);

            $access_token = $this->login()['access_token'];

            $supplier = $this->findSuplier($access_token, $invoice->supplier);

            if (count($supplier) > 0) {
                $suplier_id = $supplier[0]['supplier_id'];
                foreach ($invoice->moloni_items as $moloni_item) {
                    $product = $this->searchByReference($access_token, $moloni_item->reference);
                    if($product) {
                        $product = $product[0];
                        return $product;
                    } else {
                        //CRIAR PRODUTO
                        
                    }
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => '[Moloni] Fornecedor não encontrado.'
                ]);
            }
        }

        return response()->json($resultados);
    }
}
