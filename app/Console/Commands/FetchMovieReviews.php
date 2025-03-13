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
    protected $signature = 'app:fetch-movie-reviews {pages=3 : Number of pages to fetch}';

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
            $pages = (int) $this->argument('pages');
            $totalMovies = 0;
            
            for ($page = 0; $page < $pages; $page++) {
                $this->info("Fetching movie reviews page {$page}...");
                $movies = $this->movieRepository->getRecentReviews($page);
                $totalMovies += count($movies);
                $this->info("Fetched " . count($movies) . " movie reviews");
                
                // Sleep to avoid hitting the rate limit
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
