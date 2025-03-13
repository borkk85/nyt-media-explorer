<?php 

namespace App\Repositories;

use App\Models\Book;
use App\Services\NYT\BooksService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class BookRepository 
{

    protected $booksService;
    protected $cacheTime = 1440;

    public function __construct(BooksService $booksService)
    {
        $this->booksService = $booksService;
    }

    /**
     * Get all bestseller list categories
     */
    public function getAllLists()
    {
        return Cache::remember('nyt_bestseller_lists', $this->cacheTime, function () {
            $response = $this->booksService->getLists();
            return $response['results'] ?? [];
        });
    }

    /**
     * Get bestsellers for a specific list
     */
    public function getBestsellers($listName, $date = 'current')
    {
        $cacheKey = "nyt_bestsellers_{$listName}_{$date}";
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($listName, $date) {
            $response = $this->booksService->getBestsellersByList($listName, $date);
            if (!isset($response['results']['books'])) {
                return [];
            }
            
            // Process and save books to database
            $books = [];
            foreach ($response['results']['books'] as $bookData) {
                $books[] = $this->saveBookFromApi($bookData);
            }
            
            return $books;
        });
    }

    /**
     * Get book details by ID
     */
    public function getById($id)
    {
        return Book::findOrFail($id);
    }

    /**
     * Get book reviews
     */
    public function getBookReviews($isbn)
    {
        $cacheKey = "nyt_book_reviews_{$isbn}";
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($isbn) {
            $response = $this->booksService->getReviewsByIsbn($isbn);
            return $response['results'] ?? [];
        });
    }

    /**
     * Save book data from API to database
     */
    protected function saveBookFromApi($bookData)
    {
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
            'list_name' => $bookData['list_name'] ?? null,
            'last_fetched_at' => now(),
        ]);
        
        $book->save();
        
        return $book;
    }
}