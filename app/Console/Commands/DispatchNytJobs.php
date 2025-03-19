<?php

namespace App\Console\Commands;

use App\Jobs\FetchNytData;
use App\Services\NYT\BooksService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DispatchNytJobs extends Command
{
    protected $signature = 'app:dispatch-nyt-jobs
                           {type : Type of data to fetch (movies, books, or all)}
                           {--batch-size=10 : Number of pages to process per job}
                           {--concurrent=5 : Number of concurrent jobs to dispatch}
                           {--max-pages=100 : Maximum number of pages to process for movies}';

    protected $description = 'Dispatch background jobs to fetch NYT data';

    protected $booksService;

    public function __construct(BooksService $booksService)
    {
        parent::__construct();
        $this->booksService = $booksService;
    }

    public function handle()
    {
        $type = $this->argument('type');
        $batchSize = (int) $this->option('batch-size');
        $concurrent = (int) $this->option('concurrent');
        $maxPages = (int) $this->option('max-pages');
        
        $this->info("Dispatching NYT data fetch jobs for: {$type}");
        
        if ($type === 'movies' || $type === 'all') {
            $this->dispatchMovieJobs($batchSize, $concurrent, $maxPages);
        }
        
        if ($type === 'books' || $type === 'all') {
            $this->dispatchBookJobs($batchSize, $concurrent);
        }
        
        $this->info("All jobs have been dispatched");
        
        return 0;
    }
    
    protected function dispatchMovieJobs($batchSize, $concurrent, $maxPages)
    {
        $this->info("Dispatching movie review fetch jobs");
        $this->info("Batch size: {$batchSize}, Concurrent jobs: {$concurrent}, Max pages: {$maxPages}");
        
        // Calculate how many batches to dispatch
        $batches = ceil($maxPages / $batchSize);
        $batches = min($batches, $concurrent);
        
        for ($i = 0; $i < $batches; $i++) {
            $startPage = $i * $batchSize;
            
            $this->info("Dispatching movie fetch job for pages {$startPage} to " . ($startPage + $batchSize - 1));
            
            FetchNytData::dispatch('movies', [
                'page' => $startPage,
                'batch_size' => $batchSize
            ])->delay(now()->addSeconds($i * 5)); // Stagger job starts
        }
        
        $this->info("Dispatched {$batches} movie fetch jobs");
    }
    
    protected function dispatchBookJobs($batchSize, $concurrent)
    {
        $this->info("Dispatching bestseller list fetch jobs");
        
        // Get all available lists
        $listsResponse = $this->booksService->getLists();
        
        if (!$listsResponse || !isset($listsResponse['results'])) {
            $this->error("Failed to fetch bestseller lists");
            return;
        }
        
        $lists = $listsResponse['results'];
        $totalLists = count($lists);
        
        $this->info("Found {$totalLists} bestseller lists");
        
        // Determine how many lists to process in parallel
        $batchCount = min($concurrent, $totalLists);
        
        for ($i = 0; $i < $batchCount; $i++) {
            $this->info("Dispatching bestseller fetch job for list index {$i}");
            
            FetchNytData::dispatch('books', [
                'list_index' => $i,
                'date' => 'current'
            ])->delay(now()->addSeconds($i * 5)); // Stagger job starts
        }
        
        $this->info("Dispatched {$batchCount} bestseller fetch jobs");
    }
}