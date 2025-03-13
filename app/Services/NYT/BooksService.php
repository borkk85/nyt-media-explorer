<?php

namespace App\Services\NYT;

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
     */
    public function getBestsellersByList($listName, $date = 'current')
    {
        return $this->get("lists/$date/$listName.json");
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