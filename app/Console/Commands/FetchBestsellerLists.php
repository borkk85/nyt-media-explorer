<?php

namespace App\Console\Commands;

use App\Repositories\BookRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchBestsellerLists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    
    protected $signature = 'app:fetch-bestsellers {list? : The specific list to fetch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch NYT bestseller lists data';
    protected $bookRepository;
    
    public function __construct(BookRepository $bookRepository)
    {
        parent::__construct();
        $this->bookRepository = $bookRepository;
    }


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fetch NYT bestseller data...');
        
        try {
            // If a specific list is provided, fetch only that list
            $listName = $this->argument('list');
            
            if ($listName) {
                $this->info("Fetching bestsellers for list: {$listName}");
                $books = $this->bookRepository->getBestsellers($listName);
                $this->info("Fetched " . count($books) . " books for list {$listName}");
            } else {
                // Otherwise, fetch all lists
                $lists = $this->bookRepository->getAllLists();
                $this->info("Found " . count($lists) . " bestseller lists");
                
                // Get the first 5 lists to avoid rate limiting
                $chunks = array_chunk($lists, 10);
                
                foreach ($chunks as $chunk) {
                    foreach ($chunk as $list) {
                        $encodedListName = $list['list_name_encoded'] ?? '';
                        if (!$encodedListName) continue;
                        
                        $this->info("Fetching bestsellers for list: {$encodedListName}");
                        $books = $this->bookRepository->getBestsellers($encodedListName);
                        $this->info("Fetched " . count($books) . " books");
                    }
                    
                    if (next($chunks) !== false) {  
                        $this->info("Sleeping to avoid rate limits...");
                        sleep(10);  
                    }
                }
            }
            
            $this->info('Finished fetching NYT bestseller data');
            return 0;
        } catch (\Exception $e) {
            $this->error("Error fetching bestseller data: " . $e->getMessage());
            Log::error("NYT Bestseller API error: " . $e->getMessage());
            return 1;
        }
    }

}
