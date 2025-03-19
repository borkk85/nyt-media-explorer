<?php

namespace App\Services\NYT;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BaseService {

    protected $apiKey;
    protected $baseUrl = 'https://api.nytimes.com/svc/';
    protected $rateLimitRemaining = 1000; // Conservative default
    protected $rateLimitReset = 0;
    protected $defaultCacheTime = 1440; // 24 hours in minutes

    public function __construct()
    {
        $this->apiKey = config('services.nyt.api_key');
    }

    /**
     * Get request with retry and rate limit handling
     */
    protected function get($endpoint, $params = [], $cacheTime = null) 
    {
        $params['api-key'] = $this->apiKey;
        $cacheKey = 'nyt_api_' . md5($endpoint . serialize($params));
        
        // Check cache first
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        try {
            // Configure the HTTP client with retry logic
            $response = $this->configureRequest()
                ->get($this->baseUrl . $endpoint, $params);
                
            $this->updateRateLimits($response);
            
            if ($response->successful()) {
                $result = $response->json();
                
                // Cache the result
                $cacheTime = $cacheTime ?? $this->defaultCacheTime;
                Cache::put($cacheKey, $result, now()->addMinutes($cacheTime));
                
                return $result;
            }

            Log::error('NYT API Error: ' . $response->status(), [
                'endpoint' => $endpoint,
                'params' => $params,
                'response' => $response->body(),
            ]);
            
            return null;

        } catch (\Exception $e) {
            Log::error('NYT API Exception: ' . $e->getMessage(), [
                'endpoint' => $endpoint,
                'params' => $params,
            ]);
            
            return null;
        }
    }
    
    /**
     * Configure HTTP client with retry and backoff strategy
     */
    protected function configureRequest(): PendingRequest
    {
        return Http::retry(3, function ($exception, $request) {
            // Only retry rate limiting errors (429) or server errors (5xx)
            $response = $exception->response ?? null;
            $status = $response?->status() ?? 0;
            
            $shouldRetry = $status === 429 || ($status >= 500 && $status < 600);
            
            if ($shouldRetry) {
                // If rate limited, wait until reset time if provided
                if ($status === 429) {
                    $resetTime = $response->header('X-RateLimit-Reset');
                    if ($resetTime) {
                        $sleepTime = max(1, strtotime($resetTime) - time());
                        Log::warning("Rate limited by NYT API. Sleeping for {$sleepTime} seconds.");
                        sleep($sleepTime);
                    } else {
                        // Default backoff: 15 seconds
                        Log::warning("Rate limited by NYT API. Using default backoff of 15 seconds.");
                        sleep(15);
                    }
                }
            }
            
            return $shouldRetry;
        }, function ($exception) {
            // Calculate backoff time - exponential backoff strategy
            $attempt = $exception->retryCount ?? 0;
            $backoff = min(30, pow(2, $attempt)); // 2, 4, 8, 16, 30 seconds
            
            Log::info("Retrying NYT API request. Attempt {$attempt}. Waiting {$backoff} seconds.");
            
            return $backoff * 1000; // Convert to milliseconds
        })
        ->withHeaders([
            'Accept' => 'application/json',
        ])
        ->timeout(30); // 30 second timeout
    }
    
    /**
     * Update rate limit tracking from response headers
     */
    protected function updateRateLimits($response)
    {
        // Update rate limit information from response headers
        if ($response->header('X-RateLimit-Remaining')) {
            $this->rateLimitRemaining = (int) $response->header('X-RateLimit-Remaining');
        }
        
        if ($response->header('X-RateLimit-Reset')) {
            $this->rateLimitReset = $response->header('X-RateLimit-Reset');
        }
        
        if ($this->rateLimitRemaining < 50) {
            Log::warning("NYT API rate limit getting low: {$this->rateLimitRemaining} requests remaining");
        }
    }

}