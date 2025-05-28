<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MoloniInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;

class MoloniNewInvoiceController extends Controller
{
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
- referencia (ex: "9Y0907253C", "PAA698151", etc.)
- nome (ex: "1 conj. past. de travão", "Bateria", etc.)
- quantidade (valor numérico)

Se a referência não existir, usa null. Não uses "undefined".
Mantém a ordem dos itens conforme aparecem no texto.

Texto OCR:
\"\"\"{$moloniInvoice->ocr}\"\"\"
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

        // Log de resposta bruta
        \Log::debug('[IA] Conteúdo devolvido pela IA:', ['raw' => $content]);

        // Limpar blocos de código ```json
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
            'referencias' => $json // <- nome correto esperado pelo JS
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => '[IA] Erro ao gerar referências: ' . $e->getMessage()
        ]);
    }
}

}
