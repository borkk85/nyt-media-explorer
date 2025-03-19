<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Repositories\MovieRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    protected $movieRepository;
    
    public function __construct(MovieRepository $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }
    
    /**
     * Display a listing of movie reviews.
     */
    public function index(Request $request)
    {
        // Get movies query
        $query = Movie::query();
        
        // Search if query provided
        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('display_title', 'like', "%{$searchTerm}%")
                    ->orWhere('headline', 'like', "%{$searchTerm}%")
                    ->orWhere('summary', 'like', "%{$searchTerm}%")
                    ->orWhere('byline', 'like', "%{$searchTerm}%");
            });
        }
        
        // Apply sorting
        $sort = $request->sort ?? 'newest';
        switch ($sort) {
            case 'oldest':
                $query->orderBy('publication_date', 'asc');
                break;
            case 'title':
                $query->orderBy('display_title');
                break;
            case 'newest':
            default:
                $query->orderBy('publication_date', 'desc');
                break;
        }
        
        // Add favorited status for authenticated user
        if (Auth::check()) {
            $userId = Auth::id();
            $query->addSelect(['is_favorited' => function ($query) use ($userId) {
                $query->selectRaw('COUNT(*)')
                    ->from('user_favorites')
                    ->whereColumn('user_favorites.favoritable_id', 'movies.id')
                    ->where('user_favorites.favoritable_type', 'App\\Models\\Movie')
                    ->where('user_favorites.user_id', $userId);
            }]);
        }
        
        // Get paginated movies
        $movies = $query->paginate(12)->withQueryString();
        
        return view('movies.index', compact('movies'));
    }
    
    /**
     * Display the specified movie.
     */
    public function show(Movie $movie)
    {
        // Add favorited status for authenticated user
        if (Auth::check()) {
            $userId = Auth::id();
            $movie->is_favorited = $movie->userFavorites()
                ->where('user_id', $userId)
                ->exists();
        }
        
        // Load reviews with their users
        $movie->load(['reviews' => function ($query) {
            $query->with('user')->latest();
        }]);
        
        // Get related movies (recent reviews)
        $relatedMovies = Movie::where('id', '!=', $movie->id)
            ->orderBy('publication_date', 'desc')
            ->take(3)
            ->get();
        
        return view('movies.show', compact('movie', 'relatedMovies'));
    }
}