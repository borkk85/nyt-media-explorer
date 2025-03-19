<?php

namespace App\Services\NYT;

use Illuminate\Support\Facades\Log;

class MoviesService extends BaseService {

    protected $baseUrl = 'https://api.nytimes.com/svc/';
    
    /**
     * Get movie reviews using the Article Search API with optional pagination
     * 
     * @param int $page Page number (0-based)
     * @param int|null $limit Maximum number of results to return across all pages
     * @return array Array of movie reviews
     */
    public function getMovieReviews($page = 0, $limit = null)
    {
        return $this->get('search/v2/articlesearch.json', [
            'q' => 'movie',
            'fq' => 'section_name:"Movies" AND type_of_material:"Review"',
            'sort' => 'newest',
            'page' => $page
        ]);
    }
    
    /**
     * Get all available movie reviews (up to NYT API limits)
     * Will automatically handle pagination and rate limits
     * 
     * @param int $maxPages Maximum number of pages to fetch (NYT limits to 100 pages)
     * @param bool $showProgress Whether to log progress
     * @return array Array of all movie reviews
     */
    public function getAllMovieReviews($maxPages = 100, $showProgress = true)
    {
        $allResults = [];
        $currentPage = 0;
        $totalHits = null;
        $hasMore = true;
        
        while ($hasMore && $currentPage < $maxPages) {
            if ($showProgress) {
                Log::info("Fetching NYT movie reviews page {$currentPage}");
            }
            
            $response = $this->getMovieReviews($currentPage);
            
            if (!$response || !isset($response['response']['docs'])) {
                Log::error("Failed to fetch NYT movie reviews page {$currentPage}");
                break;
            }
            
            $results = $response['response']['docs'];
            $allResults = array_merge($allResults, $results);
            
            // Get total hits if not already set
            if ($totalHits === null && isset($response['response']['meta']['hits'])) {
                $totalHits = $response['response']['meta']['hits'];
                
                if ($showProgress) {
                    Log::info("NYT API reports {$totalHits} total movie reviews available");
                }
            }
            
            // Check if we have more results to fetch
            if (empty($results) || count($results) < 10) {
                $hasMore = false;
            }
            
            // Move to next page
            $currentPage++;
            
            // Respect rate limits with a small delay between requests
            if ($hasMore && $currentPage < $maxPages) {
                sleep(1); // 1 second delay between requests is conservative
            }
        }
        
        if ($showProgress) {
            Log::info("Completed fetching NYT movie reviews. Retrieved " . count($allResults) . " reviews");
        }
        
        return $allResults;
    }
    
    /**
     * Search movie reviews with pagination handling
     */
    public function searchMovieReviews($query, $page = 0) 
    {
        return $this->get('search/v2/articlesearch.json', [
            'fq' => 'section_name:"Movies" AND type_of_material:"Review"',
            'q' => $query,
            'sort' => 'newest',
            'page' => $page
        ]);
    }
    
    /**
     * Get all search results for movie reviews (handles pagination)
     */
    public function getAllSearchResults($query, $maxPages = 100)
    {
        $allResults = [];
        $currentPage = 0;
        $hasMore = true;
        
        while ($hasMore && $currentPage < $maxPages) {
            Log::info("Searching NYT movie reviews for '{$query}' - page {$currentPage}");
            
            $response = $this->searchMovieReviews($query, $currentPage);
            
            if (!$response || !isset($response['response']['docs'])) {
                break;
            }
            
            $results = $response['response']['docs'];
            $allResults = array_merge($allResults, $results);
            
            // Check if we have more results to fetch
            if (empty($results) || count($results) < 10) {
                $hasMore = false;
            }
            
            // Move to next page
            $currentPage++;
            
            // Respect rate limits with a small delay between requests
            if ($hasMore && $currentPage < $maxPages) {
                sleep(1);
            }
        }
        
        return $allResults;
    }

}