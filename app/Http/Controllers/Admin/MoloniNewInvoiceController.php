<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Moloni;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\MoloniInvoice;

class MoloniNewInvoiceController extends Controller
{
    use Moloni;

    public function index()
    {
        abort_if(Gate::denies('moloni_new_invoice_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

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
                    'ocr' => '[OCR] Ficheiro não encontrado no caminho: ' . $filePath
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
                if (isset($parsed['ParsedText']) && is_string($parsed['ParsedText'])) {
                    $ocrText .= $parsed['ParsedText'] . "\n";
                } elseif (isset($parsed['ParsedText']) && is_array($parsed['ParsedText'])) {
                    $ocrText .= implode("\n", $parsed['ParsedText']) . "\n";
                }
            }

            $moloniInvoice->ocr = (string) $ocrText;
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
    
}
