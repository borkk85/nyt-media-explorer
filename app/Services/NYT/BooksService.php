<?php

namespace App\Services\NYT;

use Illuminate\Support\Facades\Log;

class BooksService extends BaseService
{
    protected $baseUrl = 'https://api.nytimes.com/svc/books/v3/';

    /**
     * Get all lists (categories) of bestsellers
     */
    public function getLists()
    {
        return $this->get('lists/names.json');
    }
    
    /**
     * Get bestsellers from a specific list (category)
     * 
     * @param string $listName The encoded list name
     * @param string $date Date in YYYY-MM-DD format or 'current' for the latest
     * @return array Bestseller data
     */
    public function getBestsellersByList($listName, $date = 'current')
    {
        return $this->get("lists/$date/$listName.json");
    }
    
    /**
     * Get all bestsellers from all available lists
     * 
     * @param array|null $listNames Optional array of specific list names to fetch
     * @param string $date Date in YYYY-MM-DD format or 'current' for the latest
     * @param bool $showProgress Whether to log progress
     * @return array Associative array of list name => bestsellers
     */
    public function getAllBestsellers($listNames = null, $date = 'current', $showProgress = true)
    {
        $results = [];
        
        // If no list names provided, get all available lists
        if ($listNames === null) {
            $listsResponse = $this->getLists();
            
            if (!$listsResponse || !isset($listsResponse['results'])) {
                Log::error("Failed to fetch NYT bestseller lists");
                return $results;
            }
            
            $listNames = array_map(function($list) {
                return $list['list_name_encoded'];
            }, $listsResponse['results']);
        }
        
        $totalLists = count($listNames);
        
        if ($showProgress) {
            Log::info("Fetching bestsellers for {$totalLists} NYT lists");
        }
        
        foreach ($listNames as $index => $listName) {
            if ($showProgress) {
                Log::info("Fetching bestsellers for list '{$listName}' ({$index}/{$totalLists})");
            }
            
            $response = $this->getBestsellersByList($listName, $date);
            
            if (!$response || !isset($response['results'])) {
                Log::warning("Failed to fetch bestsellers for list '{$listName}'");
                continue;
            }
            
            $results[$listName] = $response['results'];
            
            // Respect rate limits with delay between list requests
            if ($index < $totalLists - 1) {
                sleep(1);
            }
        }
        
        if ($showProgress) {
            Log::info("Completed fetching NYT bestsellers for all lists");
        }
        
        return $results;
    }
    
    /**
     * Get historical bestseller data for a specific list
     * 
     * @param string $listName The encoded list name 
     * @param string $startDate Start date in YYYY-MM-DD format
     * @param string $endDate End date in YYYY-MM-DD format
     * @return array Historical bestseller data
     */
    public function getHistoricalBestsellers($listName, $startDate, $endDate)
    {
        $results = [];
        $currentDate = $startDate;
        
        while (strtotime($currentDate) <= strtotime($endDate)) {
            Log::info("Fetching bestsellers for list '{$listName}' on {$currentDate}");
            
            $response = $this->getBestsellersByList($listName, $currentDate);
            
            if ($response && isset($response['results'])) {
                $results[$currentDate] = $response['results'];
            }
            
            // Move to next week (bestseller lists are weekly)
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +7 days'));
            
            // Respect rate limits
            sleep(1);
        }
        
        return $results;
    }
    
    /**
     * Get overview of all bestseller lists
     */
    public function getOverview($date = null)
    {
        $endpoint = 'lists/overview.json';
        $params = [];
        
        if ($date) {
            $params['published_date'] = $date;
        }
        
        return $this->get($endpoint, $params);
    }
    
    /**
     * Get book reviews by ISBN
     */
    public function getReviewsByIsbn($isbn)
    {
        return $this->get('reviews.json', ['isbn' => $isbn]);
    }
    
    /**
     * Get book reviews by title
     */
    public function getReviewsByTitle($title)
    {
        return $this->get('reviews.json', ['title' => $title]);
    }
    
    /**
     * Get book reviews by author
     */
    public function getReviewsByAuthor($author)
    {
        return $this->get('reviews.json', ['author' => $author]);
    }
}