<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Repositories\MovieRepository;
use App\Services\NYT\MoviesService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchMovieReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-movie-reviews 
                            {--pages=20 : Maximum number of pages to fetch (default 20, max 100)}
                            {--force : Force refresh all data, ignoring cache}
                            {--from=0 : Start from this page number}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch NYT movie reviews data';
    
    protected $moviesService;
    protected $movieRepository;

    public function __construct(MoviesService $moviesService, MovieRepository $movieRepository)
    {
        parent::__construct();
        $this->moviesService = $moviesService;
        $this->movieRepository = $movieRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fetch NYT movie reviews...');
        
        try {
            $maxPages = min(100, (int) $this->option('pages')); // NYT API limit is 100 pages
            $fromPage = max(0, (int) $this->option('from'));
            $force = $this->option('force');
            
            $this->info("Fetching up to {$maxPages} pages of NYT movie reviews, starting from page {$fromPage}");
            
            $totalProcessed = 0;
            $totalSaved = 0;
            $hasMore = true;
            
            for ($page = $fromPage; $page < ($fromPage + $maxPages) && $hasMore; $page++) {
                $this->info("Fetching movie reviews page {$page}...");
                
                // Get reviews for this page
                $response = $this->moviesService->getMovieReviews($page);
                
                if (!$response || !isset($response['response']['docs'])) {
                    $this->error("Failed to fetch page {$page}. Stopping.");
                    break;
                }
                
                $reviews = $response['response']['docs'];
                $totalHits = $response['response']['meta']['hits'] ?? 0;
                
                $this->info("Page {$page}: Found " . count($reviews) . " reviews (Total available: {$totalHits})");
                
                if (empty($reviews)) {
                    $this->info("No more reviews found. Stopping.");
                    $hasMore = false;
                    break;
                }
                
                // Process and save each review
                $savedCount = 0;
                foreach ($reviews as $reviewData) {
                    $totalProcessed++;
                    
                    // Use the repository to save the movie
                    $movie = $this->saveMovieFromApi($reviewData);
                    
                    if ($movie) {
                        $savedCount++;
                        $totalSaved++;
                    }
                }
                
                $this->info("Saved {$savedCount} movies from page {$page}");
                
                // Check if we've reached the API limit
                if (count($reviews) < 10) {
                    $this->info("Received fewer than 10 results. Likely reached the end of available data.");
                    $hasMore = false;
                }
                
                // Add a small delay to respect rate limits
                if ($hasMore && $page < ($fromPage + $maxPages - 1)) {
                    $this->info("Pausing briefly to respect API rate limits...");
                    sleep(1);
                }
            }
            
            $this->info("Finished fetching NYT movie reviews.");
            $this->info("Processed {$totalProcessed} reviews, saved {$totalSaved} to database.");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error fetching movie reviews: " . $e->getMessage());
            Log::error("NYT Movie API error: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }
    
    /**
     * Save movie data from API to database
     */
    protected function saveMovieFromApi($reviewData)
    {
        try {
            $headline = $reviewData['headline']['main'] ?? '';

            $title = $headline;
            if (strpos($headline, ':') !== false) {
                list($title) = explode(':', $headline, 2);
            }
            
            // Try to find existing movie by title
            $movie = Movie::where('display_title', $title)->first();

            if (!$movie) {
                $movie = new Movie();
            }

            $imageUrl = null;
            if (isset($reviewData['multimedia']) && !empty($reviewData['multimedia'])) {
                foreach ($reviewData['multimedia'] as $media) {
                    if (isset($media['url'])) {
                        $imageUrl = 'https://www.nytimes.com/' . $media['url'];
                        break;
                    }
                }
            }

            $movie->fill([
                'display_title' => $title,
                'headline' => $headline,
                'byline' => $reviewData['byline']['original'] ?? '',
                'summary' => $reviewData['abstract'] ?? '',
                'publication_date' => isset($reviewData['pub_date']) ? 
                    Carbon::parse($reviewData['pub_date']) : null,
                'mpaa_rating' => null, // Not available in the article search API
                'critics_pick' => false, // Not available in the article search API
                'image_url' => $imageUrl,
                'nyt_url' => $reviewData['web_url'] ?? null,
                'last_fetched_at' => now(),
            ]);

            $movie->save();
            
            return $movie;
            
        } catch (\Exception $e) {
            $this->error("Error saving movie: " . $e->getMessage());
            Log::error("Error saving movie from API: " . $e->getMessage(), [
                'data' => $reviewData,
                'trace' => $e->getTraceAsString(),
            ]);
            
            return null;
        }
    }
}
