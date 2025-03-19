<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Repositories\BookRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    protected $bookRepository;
    
    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }
    
    /**
     * Display a listing of bestseller books.
     */
    public function index(Request $request)
    {
        // Get all bestseller lists
        $lists = $this->bookRepository->getAllLists();
        
        // Get current list if specified
        $currentList = null;
        if ($request->has('list')) {
            $listName = $request->list;
            foreach ($lists as $list) {
                if ($list['list_name_encoded'] === $listName) {
                    $currentList = $list;
                    break;
                }
            }
        }
        
        // Get books query
        $query = Book::query();
        
        // Filter by list if specified
        if ($currentList) {
            $query->where('list_name', $currentList['list_name_encoded']);
        }
        
        // Apply sorting
        $sort = $request->sort ?? 'weeks';
        switch ($sort) {
            case 'title':
                $query->orderBy('title');
                break;
            case 'author':
                $query->orderBy('author');
                break;
            case 'weeks':
            default:
                $query->orderByRaw('weeks_on_list DESC NULLS LAST');
                break;
        }
        
        // Add favorited status for authenticated user
        if (Auth::check()) {
            $userId = Auth::id();
            $book->is_favorited = $book->userFavorites()
                ->where('user_id', $userId)
                ->exists();
        }
        
        // Load reviews with their users
        $book->load(['reviews' => function ($query) {
            $query->with('user')->latest();
        }]);
        
        // Get related books by the same author
        $relatedBooks = Book::where('author', $book->author)
            ->where('id', '!=', $book->id)
            ->take(6)
            ->get();
        
        return view('books.show', compact('book', 'relatedBooks'));
    }
}userId = Auth::id();
            $query->addSelect(['is_favorited' => function ($query) use ($userId) {
                $query->selectRaw('COUNT(*)')
                    ->from('user_favorites')
                    ->whereColumn('user_favorites.favoritable_id', 'books.id')
                    ->where('user_favorites.favoritable_type', 'App\\Models\\Book')
                    ->where('user_favorites.user_id', $userId);
            }]);
        }
        
        // Get paginated books
        $books = $query->paginate(20)->withQueryString();
        
        return view('books.index', compact('books', 'lists', 'currentList'));
    }
    
    /**
     * Display the specified book.
     */
    public function show(Book $book)
    {
        // Add favorited status for authenticated user
        if (Auth::check()) {
            $