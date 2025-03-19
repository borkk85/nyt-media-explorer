<?php

namespace App\Jobs;

use App\Services\NYT\MoviesService;
use App\Services\NYT\BooksService;
use App\Models\Book;
use App\Models\Movie;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchNytData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;
    protected $options;
    protected $maxTries = 3;
    protected $timeout = 600; // 10 minutes
    
    /**
     * Create a new job instance.
     *
     * @param string $type Type of data to fetch ('movies' or 'books')
     * @param array $options Options for the fetch operation
     */
    public function __construct($type, array $options = [])
    {
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle(MoviesService $moviesService, BooksService $booksService)
    {
        Log::info("Starting NYT data fetch job", [
            'type' => $this->type,
            'options' => $this->options
        ]);
        
        try {
            if ($this->type === 'movies') {
                $this->fetchMovies($moviesService);
            } elseif ($this->type === 'books') {
                $this->fetchBooks($booksService);
            } else {
                Log::error("Unknown NYT data type: {$this->type}");
            }
            
            Log::info("Completed NYT data fetch job", [
                'type' => $this->type
            ]);
        } catch (\Exception $e) {
            Log::error("Error in NYT data fetch job: " . $e->getMessage(), [
                'type' => $this->type,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // Re-throw to trigger job failure
        }
    }
    
    /**
     * Fetch movie reviews
     */
    protected function fetchMovies(MoviesService $moviesService)
    {
        $page = $this->options['page'] ?? 0;
        $batchSize = $this->options['batch_size'] ?? 10;
        $endPage = $page + $batchSize;
        
        Log::info("Fetching movie reviews", [
            'start_page' => $page,
            'end_page' => $endPage
        ]);
        
        $totalSaved = 0;
        $hasMore = true;
        
        for ($currentPage = $page; $currentPage < $endPage && $hasMore; $currentPage++) {
            Log::info("Fetching movie reviews page {$currentPage}");
            
            $response = $moviesService->getMovieReviews($currentPage);
            
            if (!$response || !isset($response['response']['docs'])) {
                Log::warning("No results for page {$currentPage}");
                $hasMore = false;
                break;
            }
            
            $reviews = $response['response']['docs'];
            $totalHits = $response['response']['meta']['hits'] ?? 0;
            
            if (empty($reviews)) {
                $hasMore = false;
                break;
            }
            
            foreach ($reviews as $reviewData) {
                $movie = $this->saveMovie($reviewData);
                if ($movie) {
                    $totalSaved++;
                }
            }
            
            // Check if we've reached the end of available data
            if (count($reviews) < 10) {
                $hasMore = false;
            }
            
            // Respect rate limits
            if ($hasMore && $currentPage < $endPage - 1) {
                sleep(1);
            }
        }
        
        Log::info("Saved {$totalSaved} movies", [
            'start_page' => $page,
            'end_page' => $currentPage
        ]);
        
        // If there's more data, queue the next batch
        if ($hasMore) {
            self::dispatch('movies', [
                'page' => $endPage,
                'batch_size' => $batchSize
            ])->delay(now()->addSeconds(5));
            
            Log::info("Queued next batch of movie reviews", [
                'next_page' => $endPage
            ]);
        }
    }
    
    /**
     * Fetch bestseller books
     */
    protected function fetchBooks(BooksService $booksService)
    {
        $listName = $this->options['list'] ?? null;
        $date = $this->options['date'] ?? 'current';
        $listIndex = $this->options['list_index'] ?? 0;
        
        // If no specific list, get all lists
        if (!$listName) {
            $listsResponse = $booksService->getLists();
            
            if (!$listsResponse || !isset($listsResponse['results'])) {
                Log::error("Failed to fetch bestseller lists");
                return;
            }
            
            $lists = $listsResponse['results'];
            
            // If we have a list index, process just that list
            if (isset($lists[$listIndex])) {
                $listName = $lists[$listIndex]['list_name_encoded'];
                Log::info("Processing list at index {$listIndex}: {$listName}");
                
                $this->processBestsellerList($booksService, $listName, $date);
                
                // Queue the next list if available
                if (isset($lists[$listIndex + 1])) {
                    self::dispatch('books', [
                        'list_index' => $listIndex + 1,
                        'date' => $date
                    ])->delay(now()->addSeconds(5));
                    
                    Log::info("Queued next bestseller list", [
                        'next_index' => $listIndex + 1,
                        'next_list' => $lists[$listIndex + 1]['list_name_encoded']
                    ]);
                }
            } else {
                Log::error("Invalid list index: {$listIndex}");
            }
        } else {
            // Process the specified list
            $this->processBestsellerList($booksService, $listName, $date);
        }
    }
    
    /**
     * Process a single bestseller list
     */
    protected function processBestsellerList(BooksService $booksService, $listName, $date)
    {
        Log::info("Fetching bestsellers for list '{$listName}' on {$date}");
        
        $response = $booksService->getBestsellersByList($listName, $date);
        
        if (!$response || !isset($response['results']['books'])) {
            Log::warning("No bestsellers found for list '{$listName}' on {$date}");
            return;
        }
        
        $books = $response['results']['books'];
        $totalSaved = 0;
        
        foreach ($books as $bookData) {
            $book = $this->saveBook($bookData, $listName);
            if ($book) {
                $totalSaved++;
            }
        }
        
        Log::info("Saved {$totalSaved} books from list '{$listName}'");
    }
    
    /**
     * Save movie data from API to database
     */
    protected function saveMovie($reviewData)
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
                'image_url' => $imageUrl,
                'nyt_url' => $reviewData['web_url'] ?? null,
                'last_fetched_at' => now(),
            ]);

            $movie->save();
            
            return $movie;
            
        } catch (\Exception $e) {
            Log::error("Error saving movie: " . $e->getMessage(), [
                'data' => $reviewData,
                'trace' => $e->getTraceAsString(),
            ]);
            
            return null;
        }
    }
    
    /**
     * Save book data from API to database
     */
    protected function saveBook($bookData, $listName)
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
            Log::error("Error saving book: " . $e->getMessage(), [
                'data' => $bookData,
                'trace' => $e->getTraceAsString(),
            ]);
            
            return null;
        }
    }
}