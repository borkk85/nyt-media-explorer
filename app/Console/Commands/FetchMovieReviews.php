<?php

namespace App\Console\Commands;

use App\Repositories\MovieRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchMovieReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-movie-reviews {--pages=0 : Number of pages to fetch (0 for all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch NYT movie reviews data';
    protected $movieRepository;

    public function __construct(MovieRepository $movieRepository)
    {
        parent::__construct();
        $this->movieRepository = $movieRepository;
    }


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fetch NYT movie reviews...');
        
        try {
            $pages = $this->option('pages') ? (int)$this->option('pages') : 5; 
            $totalMovies = 0;
            
            for ($page = 0; $page < $pages; $page++) {
                $this->info("Fetching movie reviews page {$page}...");
                
                $apiResponse = $this->movieRepository->getMoviesService()->getMovieReviews($page);
                $this->info("API Response hits: " . ($apiResponse['response']['meta']['hits'] ?? 'unknown'));
                $this->info("API Response docs count: " . count($apiResponse['response']['docs'] ?? []));
                
                if (!empty($apiResponse['response']['docs'])) {
                    $this->info("First doc structure: " . json_encode(array_keys($apiResponse['response']['docs'][0])));
                }
                
                $movies = $this->movieRepository->getRecentReviews($page);
                $totalMovies += count($movies);
                $this->info("Processed and saved: " . count($movies) . " movie reviews");
                
                sleep(1); 
            }
            
            $this->info("Finished fetching NYT movie reviews. Total: {$totalMovies}");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error fetching movie reviews: " . $e->getMessage());
            Log::error("NYT Movie API error: " . $e->getMessage());
            return 1;
        }
    }
}
