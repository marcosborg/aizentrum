<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\Http;

trait Moloni
{

    protected $baseUrl = 'https://api.moloni.pt/v1/';

    /**
     * Faz autenticação na API da Moloni e retorna o token de acesso.
     *
     * @return array|null
     */
    public function authenticateMoloni()
    {
        $this->clientId = env('MOLONI_CLIENT_ID');
        $this->clientSecret = env('MOLONI_CLIENT_SECRET');
        $this->username = env('MOLONI_USERNAME');
        $this->password = env('MOLONI_PASSWORD');

        try {
            $query = http_build_query([
                'grant_type'    => 'password',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'username'      => $this->username,
                'password'      => $this->password,
            ]);

            $url = $this->baseUrl . 'grant/?' . $query;

            // Simular cURL puro: headers mínimos
            $response = Http::withHeaders([
                'Accept' => '*/*', // Forçar o comportamento mais simples possível
            ])->get($url);

            return $response->json();

            \Log::error('Moloni Auth Failed', ['response' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            \Log::error('Moloni Auth Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function refreshMoloniToken($refreshToken)
    {
        $this->clientId = env('MOLONI_CLIENT_ID');
        $this->clientSecret = env('MOLONI_CLIENT_SECRET');

        try {
            $query = http_build_query([
                'grant_type'    => 'refresh_token',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $refreshToken,
            ]);

            $url = $this->baseUrl . 'grant/?' . $query;

            $response = Http::withHeaders([
                'Accept' => '*/*',
            ])->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            \Log::error('Moloni Token Refresh Failed', ['response' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            \Log::error('Moloni Token Refresh Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
