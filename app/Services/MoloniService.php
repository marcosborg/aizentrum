<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\MoloniItem;
use App\Models\MoloniInvoice;
use Exception;

class MoloniService
{
    protected $clientId;
    protected $clientSecret;
    protected $username;
    protected $password;
    protected $companyId;
    protected $documentSetId;
    protected $accessToken;

    public function __construct()
    {
        $this->clientId       = env('MOLONI_CLIENT_ID');
        $this->clientSecret   = env('MOLONI_CLIENT_SECRET');
        $this->username       = env('MOLONI_USERNAME');
        $this->password       = env('MOLONI_PASSWORD');
        $this->companyId      = env('MOLONI_COMPANY_ID');
        $this->documentSetId  = env('MOLONI_DOCUMENT_SET_ID');

        $this->ensureAuthenticated();
    }

    public function ensureAuthenticated()
    {
        if (session()->has('moloni_access_token') && now()->lessThan(session('moloni_token_expiry'))) {
            $this->accessToken = session('moloni_access_token');
            Log::debug('[MOLONI] Access token recuperado da sessão.');
            return;
        }

        if (session()->has('moloni_refresh_token')) {
            Log::debug('[MOLONI] Tentativa de refresh token...');
            $refreshed = $this->refreshToken(session('moloni_refresh_token'));
            if ($refreshed) {
                return;
            }
        }

        Log::debug('[MOLONI] Autenticação com username/password.');
        $this->authenticateWithPassword();
    }

    protected function request($endpoint, $params = [])
    {
        $this->ensureAuthenticated();

        if (!$this->accessToken) {
            throw new Exception('Access token inválido ou não definido.');
        }

        $params['access_token'] = $this->accessToken;

        $response = Http::post("https://api.moloni.pt/v1/{$endpoint}/", $params);

        if (!$response->ok()) {
            throw new Exception("Erro na chamada ao endpoint {$endpoint}: {$response->body()}");
        }

        return $response->json();
    }

    public function getOrCreateSupplier($name)
    {
        $result = $this->request('suppliers/getAll', [
            'company_id' => $this->companyId,
            'filters' => ['name' => $name]
        ]);

        $supplier = collect($result)->firstWhere('name', $name);

        if ($supplier) {
            return $supplier['supplier_id'];
        }

        $new = $this->request('suppliers/insert', [
            'company_id' => $this->companyId,
            'name' => $name
        ]);

        return $new['supplier_id'];
    }

    public function getOrCreateProduct($item, $categoryId)
    {
        $products = $this->request('products/getAll', [
            'company_id' => $this->companyId,
            'filters' => ['reference' => $item->reference]
        ]);

        $product = collect($products)->firstWhere('reference', $item->reference);

        if ($product) {
            $this->request('products/updateStock', [
                'company_id' => $this->companyId,
                'product_id' => $product['product_id'],
                'stock' => $product['stock'] + $item->qty,
            ]);
            return $product['product_id'];
        }

        $new = $this->request('products/insert', [
            'company_id' => $this->companyId,
            'category_id' => $categoryId,
            'name' => $item->name ?? 'Produto sem nome',
            'reference' => $item->reference,
            'price' => $item->price ?? 0,
            'has_stock' => true,
            'stock' => $item->qty,
            'unit' => 'Un',
            'vat_id' => 23,
        ]);

        return $new['product_id'];
    }

    public function createDocument($supplierId, $invoiceNumber)
    {
        $document = $this->request('purchaseOrder/insert', [
            'company_id' => $this->companyId,
            'supplier_id' => $supplierId,
            'document_set_id' => $this->documentSetId,
            'date' => now()->toDateString(),
            'expiration_date' => now()->addDays(30)->toDateString(),
            'document_number' => $invoiceNumber,
        ]);

        return $document['purchase_order_id'];
    }

    public function addProductToDocument($documentId, $productId, $item)
    {
        $this->request('purchaseOrder/addProduct', [
            'company_id' => $this->companyId,
            'purchase_order_id' => $documentId,
            'product_id' => $productId,
            'qty' => $item->qty,
            'price' => $item->price ?? 0,
        ]);
    }

    public function uploadDocumentFile($documentId, MoloniInvoice $invoice)
    {
        $file = $invoice->file;

        if (!$file || !method_exists($file, 'getPath')) {
            return;
        }

        $filePath = $file->getPath();

        if (!file_exists($filePath)) {
            return;
        }

        Http::attach(
            'file',
            file_get_contents($filePath),
            basename($filePath)
        )->post('https://api.moloni.pt/v1/purchaseOrder/uploadFile/', [
            'access_token' => $this->accessToken,
            'company_id' => $this->companyId,
            'purchase_order_id' => $documentId,
        ]);
    }

    public function getCompanyDetails()
    {
        return $this->request('companies/getOne', [
            'company_id' => $this->companyId
        ]);
    }

    protected function refreshToken($refreshToken)
    {
        $response = Http::asForm()->post('https://api.moloni.pt/v1/grant/', [
            'grant_type'    => 'refresh_token',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $refreshToken,
        ]);

        if (!$response->successful()) {
            Log::warning('Falha ao refrescar o token da Moloni.', [
                'refresh_token' => $refreshToken,
                'response' => $response->body()
            ]);
            return false;
        }

        $data = $response->json();

        $this->accessToken = $data['access_token'] ?? null;

        if (!$this->accessToken) {
            throw new Exception('Access token não recebido ao autenticar/refrescar.');
        }

        session([
            'moloni_access_token' => $this->accessToken,
            'moloni_token_expiry' => now()->addSeconds($data['expires_in']),
            'moloni_refresh_token' => $data['refresh_token'] ?? null,
        ]);

        return true;
    }

    protected function authenticateWithPassword()
    {
        $data = http_build_query([
            'grant_type'    => 'password',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'username'      => $this->username,
            'password'      => $this->password,
        ]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->withBody($data, 'application/x-www-form-urlencoded')
          ->post('https://api.moloni.pt/v1/grant/');

        if (!$response->successful()) {
            Log::error('Erro ao autenticar com username/password Moloni.', [
                'response' => $response->body()
            ]);
            throw new Exception('Erro ao autenticar com a Moloni');
        }

        $data = $response->json();

        $this->accessToken = $data['access_token'] ?? null;

        if (!$this->accessToken) {
            throw new Exception('Access token não recebido.');
        }

        session([
            'moloni_access_token' => $this->accessToken,
            'moloni_token_expiry' => now()->addSeconds($data['expires_in']),
            'moloni_refresh_token' => $data['refresh_token'] ?? null,
        ]);
    }
}
