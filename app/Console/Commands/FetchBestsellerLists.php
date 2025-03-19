<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Repositories\BookRepository;
use App\Services\NYT\BooksService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FetchBestsellerLists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-bestsellers 
                           {list? : The specific list to fetch} 
                           {--all : Fetch all available lists}
                           {--date=current : The date to fetch bestsellers for (YYYY-MM-DD or "current")}
                           {--historical=0 : Number of weeks of historical data to fetch}
                           {--force : Force refresh all data, ignoring cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch NYT bestseller lists data';
    
    protected $booksService;
    protected $bookRepository;
    
    public function __construct(BooksService $booksService, BookRepository $bookRepository)
    {
        parent::__construct();
        $this->booksService = $booksService;
        $this->bookRepository = $bookRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fetch NYT bestseller data...');
        
        try {
            $forceFresh = $this->option('force');
            $date = $this->option('date');
            $historicalWeeks = (int)$this->option('historical');
            
            // Clear cache if force option is used
            if ($forceFresh) {
                $this->info('Clearing cache for bestseller data...');
                Cache::forget('nyt_bestseller_lists');
            }
            
            // Get all available lists
            $listsResponse = $this->booksService->getLists();
            
            if (!$listsResponse || !isset($listsResponse['results'])) {
                $this->error('Failed to fetch bestseller lists from NYT API.');
                return 1;
            }
            
            $lists = $listsResponse['results'];
            $this->info('Found ' . count($lists) . ' bestseller lists');
            
            // If a specific list is provided, filter to just that list
            $listName = $this->argument('list');
            if ($listName) {
                $lists = array_filter($lists, function($list) use ($listName) {
                    return $list['list_name_encoded'] === $listName;
                });
                
                if (empty($lists)) {
                    $this->error("List '{$listName}' not found. Available lists:");
                    foreach ($listsResponse['results'] as $availableList) {
                        $this->info("- {$availableList['list_name_encoded']} ({$availableList['display_name']})");
                    }
                    return 1;
                }
            } elseif (!$this->option('all')) {
                // If not requesting all lists and no specific list provided, 
                // default to the top 10 most popular lists
                $this->info('No specific list requested. Defaulting to the first 10 lists');
                
                // Assuming more popular lists are first
                $lists = array_slice($lists, 0, 10);
            }
            
            $totalBooks = 0;
            
            // Process each list
            foreach ($lists as $listInfo) {
                $encodedListName = $listInfo['list_name_encoded'];
                $displayName = $listInfo['display_name'];
                
                $this->info("Processing list: {$displayName} ({$encodedListName})");
                
                // Fetch current bestsellers
                $this->fetchAndSaveBestsellers($encodedListName, $date);
                
                // Fetch historical data if requested
                if ($historicalWeeks > 0) {
                    $this->fetchHistoricalBestsellers($encodedListName, $historicalWeeks);
                }
                
                // Add a small delay to respect rate limits
                if (next($lists) !== false) {
                    $this->info("Pausing briefly to respect API rate limits...");
                    sleep(1);
                }
            }
            
            // Get total count of books in database
            $bookCount = Book::count();
            $this->info("Finished fetching NYT bestseller data. Total books in database: {$bookCount}");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error fetching bestseller data: " . $e->getMessage());
            Log::error("NYT Bestseller API error: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            
            return 1;
        }
    }
    
    /**
     * Fetch and save bestsellers for a specific list
     */
    protected function fetchAndSaveBestsellers($listName, $date = 'current')
    {
        $this->info("Fetching bestsellers for list '{$listName}' on {$date}");
        
        $response = $this->booksService->getBestsellersByList($listName, $date);
        
        if (!$response || !isset($response['results']['books'])) {
            $this->warn("No bestsellers found for list '{$listName}' on {$date}");
            return 0;
        }
        
        $books = $response['results']['books'];
        $this->info("Found " . count($books) . " bestsellers for list '{$listName}'");
        
        $savedCount = 0;
        foreach ($books as $bookData) {
            $book = $this->saveBookFromApi($bookData, $listName);
            if ($book) {
                $savedCount++;
            }
        }
        
        $this->info("Saved {$savedCount} books from list '{$listName}'");
        return $savedCount;
    }
    
    /**
     * Fetch historical bestsellers for a specific list
     */
    protected function fetchHistoricalBestsellers($listName, $weeksBack)
    {
        $this->info("Fetching {$weeksBack} weeks of historical data for '{$listName}'");
        
        $totalSaved = 0;
        
        for ($i = 1; $i <= $weeksBack; $i++) {
            $date = Carbon::now()->subWeeks($i)->format('Y-m-d');
            $this->info("Fetching bestsellers for list '{$listName}' on {$date} (week -{$i})");
            
            $savedCount = $this->fetchAndSaveBestsellers($listName, $date);
            $totalSaved += $savedCount;
            
            // Add a small delay to respect rate limits
            if ($i < $weeksBack) {
                sleep(1);
            }
        }
        
        $this->info("Saved {$totalSaved} books from historical data for list '{$listName}'");
        
        return $totalSaved;
    }
    
    /**
     * Save book data from API to database
     */
    protected function saveBookFromApi($bookData, $listName)
    {
        try {
            // Try to find existing book by ISBN
            $isbn13 = $bookData['primary_isbn13'] ?? null;
            $isbn10 = $bookData['primary_isbn10'] ?? null;
            
            $book = null;
            if ($isbn13) {
                $book = Book::where('isbn13', $isbn13)->first();
            } elseif ($isbn10) {
                $book = Book::where('isbn10', $isbn10)->first();
            }
            
            // If book doesn't exist, create it
            if (!$book) {
                $book = new Book();
                $this->info("Creating new book: {$bookData['title']} by {$bookData['author']}");
            } else {
                $this->info("Updating existing book: {$bookData['title']} by {$bookData['author']}");
            }
            
            // Update book data
            $book->fill([
                'title' => $bookData['title'] ?? '',
                'author' => $bookData['author'] ?? '',
                'description' => $bookData['description'] ?? '',
                'isbn13' => $isbn13,
                'isbn10' => $isbn10,
                'publisher' => $bookData['publisher'] ?? '',
                'image_url' => $bookData['book_image'] ?? null,
                'amazon_url' => $bookData['amazon_product_url'] ?? null,
                'weeks_on_list' => $bookData['weeks_on_list'] ?? null,
                'list_name' => $listName,
                'last_fetched_at' => now(),
            ]);
            
            $book->save();
            
            return $book;
            
        } catch (\Exception $e) {
            $this->error("Error saving book: " . $e->getMessage());
            Log::error("Error saving book from API: " . $e->getMessage(), [
                'data' => $bookData,
                'trace' => $e->getTraceAsString(),
            ]);
            
            return null;
        }
    }

}