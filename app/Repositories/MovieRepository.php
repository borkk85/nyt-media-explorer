<?php

namespace App\Repositories;

use App\Models\Movie;
use App\Services\NYT\MoviesService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MovieRepository
{
    protected $moviesService;
    protected $cacheTime = 1440; 

    public function __construct(MoviesService $moviesService)
    {
        $this->moviesService = $moviesService;
    }

    /**
     * Get recent movie reviews
     */
    public function getRecentReviews($page = 0)
    {
        $cacheKey = "nyt_movie_reviews_page_{$page}";
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($page) {
            $response = $this->moviesService->getMovieReviews($page);
            if (!isset($response['response']['docs'])) {
                return [];
            }
            
            // Process and save movies to database
            $movies = [];
            foreach ($response['response']['docs'] as $movieData) {
                $movie = $this->saveMovieFromApi($movieData);
                if ($movie) {
                    $movies[] = $movie;
                }
            }
            
            return $movies;
        });
    }

    /**
     * Search for movie reviews
     */
    public function searchReviews($query, $page = 0)
    {
        $cacheKey = "nyt_movie_search_{$query}_page_{$page}";

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($query, $page) {
            $response = $this->moviesService->searchMovieReviews($query, $page);
            if (!isset($response['response']['docs'])) {
                return [];
            }

            $movies = [];
            foreach ($response['response']['docs'] as $movieData) {
                $movies[] = $this->saveMovieFromApi($movieData);
            }

            return $movies;
        });
    }

    /**
     * Get movie details by ID
     */
    public function getById($id)
    {
        return Movie::findOrFail($id);
    }

    /**
     * Save movie data from API to database
     */
    protected function saveMovieFromApi($movieData)
    {
        Log::info('Processing movie data:', [
            'headline' => $movieData['headline']['main'] ?? 'No headline',
            'byline' => $movieData['byline']['original'] ?? 'No byline',
            'pub_date' => $movieData['pub_date'] ?? 'No date'
        ]);

        $headline = $movieData['headline']['main'] ?? '';

        $title = $headline;
        if (strpos($headline, ':') !== false) {
            list($title) = explode(':', $headline, 2);
        }

        try {
            $movie = Movie::where('display_title', $title)->first();

            if (!$movie) {
                $movie = new Movie();
                Log::info('Creating new movie record', ['title' => $title]);
            } else {
                Log::info('Updating existing movie record', ['id' => $movie->id, 'title' => $title]);
            }

            $imageUrl = null;
            if (isset($movieData['multimedia']) && !empty($movieData['multimedia'])) {
                foreach ($movieData['multimedia'] as $media) {
                    if (isset($media['url'])) {
                        $imageUrl = 'https://www.nytimes.com/' . $media['url'];
                        break;
                    }
                }
            }

            $movie->fill([
                'display_title' => $title,
                'headline' => $headline,
                'byline' => $movieData['byline']['original'] ?? '',
                'summary' => $movieData['abstract'] ?? '',
                'publication_date' => $movieData['pub_date'] ? Carbon::parse($movieData['pub_date']) : null,
                'image_url' => $imageUrl,
                'nyt_url' => $movieData['web_url'] ?? null,
                'last_fetched_at' => now(),
            ]);

            $movie->save();
            Log::info('Movie saved successfully', ['id' => $movie->id, 'title' => $movie->display_title]);

            return $movie;
        } catch (\Exception $e) {
            Log::error('Error saving movie: ' . $e->getMessage(), [
                'title' => $title,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function getMoviesService()
    {
        return $this->moviesService;
    }
}
