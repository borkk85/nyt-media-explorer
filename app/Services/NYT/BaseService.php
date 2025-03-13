<?php

namespace App\Services\NYT;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BaseService {

    protected $apiKey;
    protected $baseUrl = 'https://api.nytimes.com/svc/';

    public function __construct()
    {
        $this->apiKey = config('services.nyt.api_key');
    }

    protected function get($endpoint, $params = []) 
    {
        $params['api-key'] = $this->apiKey;

        try {
            $response = Http::get($this->baseUrl . $endpoint, $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('NYT API Error: ' . $response->status(), [
                'endpoint' => $endpoint,
                'params' => $params,
                'response' => $response->body(),
            ]);

        } catch (\Exception $e) {
            Log::error('NYT API Exception: ' . $e->getMessage(), [
                'endpoint' => $endpoint,
                'params' => $params,
            ]);
            
            return null;
        }
    }

}