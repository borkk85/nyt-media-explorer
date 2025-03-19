<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Handle the search request.
     */
    public function index(Request $request)
    {
        // Validate search query
        $query = $request->q;
        
        if (empty($query)) {
            return redirect()->route('home');
        }
        
        // Determine what to search for based on type
        $type = $request->type;
        
        // Get books matching the search query
        $books = collect();
        $booksTotal = 0;
        
        if (!$type || $type === 'books') {
            $booksQuery = Book::where('title', 'like', "%{$query}%")
                ->orWhere('author', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");
                
            // Add favorited status for authenticated user
            if (Auth::check()) {
                $userId = Auth::id();
                $booksQuery->addSelect(['is_favorited' => function ($q) use ($userId) {
                    $q->selectRaw('COUNT(*)')
                        ->from('user_favorites')
                        ->whereColumn('user_favorites.favoritable_id', 'books.id')
                        ->where('user_favorites.favoritable_type', 'App\\Models\\Book')
                        ->where('user_favorites.user_id', $userId);
                }]);
            }
            
            // Paginate results if specifically requesting books, otherwise limit to a few
            if ($type === 'books') {
                $books = $booksQuery->paginate(20)->withQueryString();
            } else {
                $books = $booksQuery->take(8)->get();
                $booksTotal = $booksQuery->count();
            }
        } else {
            // Set up empty pagination for compatibility
            $books = Book::where('id', 0)->paginate(1)->withQueryString();
        }
        
        // Get movies matching the search query
        $movies = collect();
        $moviesTotal = 0;
        
        if (!$type || $type === 'movies') {
            $moviesQuery = Movie::where('display_title', 'like', "%{$query}%")
                ->orWhere('headline', 'like', "%{$query}%")
                ->orWhere('summary', 'like', "%{$query}%")
                ->orWhere('byline', 'like', "%{$query}%");
                
            // Add favorited status for authenticated user
            if (Auth::check()) {
                $userId = Auth::id();
                $moviesQuery->addSelect(['is_favorited' => function ($q) use ($userId) {
                    $q->selectRaw('COUNT(*)')
                        ->from('user_favorites')
                        ->whereColumn('user_favorites.favoritable_id', 'movies.id')
                        ->where('user_favorites.favoritable_type', 'App\\Models\\Movie')
                        ->where('user_favorites.user_id', $userId);
                }]);
            }
            
            // Paginate results if specifically requesting movies, otherwise limit to a few
            if ($type === 'movies') {
                $movies = $moviesQuery->paginate(12)->withQueryString();
            } else {
                $movies = $moviesQuery->take(6)->get();
                $moviesTotal = $moviesQuery->count();
            }
        } else {
            // Set up empty pagination for compatibility
            $movies = Movie::where('id', 0)->paginate(1)->withQueryString();
        }
        
        // Calculate total results
        $totalResults = ($type === 'books' ? $books->total() : $booksTotal) + 
                       ($type === 'movies' ? $movies->total() : $moviesTotal);
        
        return view('search.index', compact('books', 'movies', 'totalResults'));
    }
}