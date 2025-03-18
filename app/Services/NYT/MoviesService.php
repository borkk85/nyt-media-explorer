<?php

namespace App\Services\NYT;

class MoviesService extends BaseService {

    protected $baseUrl = 'https://api.nytimes.com/svc/';


    /**
     * Get movie reviews using the Article Search API 
     * (as the Movie Reviews API is deprecated according to the YAML)
     */

     public function getMovieReviews($page = 0)
     {
         $result = $this->get('search/v2/articlesearch.json', [
             'q' => 'movie', // Add a query term
             'fq' => 'section_name:"Movies" AND type_of_material:"Review"',
             'sort' => 'newest',
             'page' => $page
         ]);
         
         \Illuminate\Support\Facades\Log::info('NYT API Response', [
             'endpoint' => 'articlesearch',
             'page' => $page,
             'response' => $result
         ]);
         
         return $result;
     }
 
     public function searchMovieReviews($query, $page = 0) 
     {
         return $this->get('search/v2/articlesearch.json', [
             'fq' => 'section_name:"Movies" AND type_of_material:"Review"',
             'q' => $query,
             'sort' => 'newest',
             'page' => $page
         ]);
     }

}