<?php

namespace App\Services\NYT;

class MoviesService extends BaseService {

    protected $baseUtl = 'https://api.nytimes.com/svc/';


    /**
     * Get movie reviews using the Article Search API 
     * (as the Movie Reviews API is deprecated according to the YAML)
     */

    public function getMovieReviews($page = 0)
    {
        return $this->get('search/v2/articlesearch.json', [
            'fq' => 'section_name:"Movies AND type_of_material:"Review"',
            'sort' => 'newest',
            'page' => $page
        ]);
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