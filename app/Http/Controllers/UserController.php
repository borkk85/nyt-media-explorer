<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Comment;
use App\Models\Movie;
use App\Models\Review;
use App\Models\UserFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    
    /**
     * Display user's favorite items.
     */
    public function favorites(Request $request)
    {
        $user = Auth::user();
        $query = $user->favorites()->with('favoritable');
        
        // Filter by type if specified
        if ($request->has('type')) {
            if ($request->type === 'books') {
                $query->where('favoritable_type', 'App\\Models\\Book');
            } elseif ($request->type === 'movies') {
                $query->where('favoritable_type', 'App\\Models\\Movie');
            }
        }
        
        // Get paginated favorites
        $favorites = $query->latest()->paginate(12)->withQueryString();
        
        return view('user.favorites', compact('favorites'));
    }
    
    /**
     * Display user's reviews.
     */
    public function reviews(Request $request)
    {
        $user = Auth::user();
        $query = $user->reviews()->with('reviewable');
        
        // Add comments count to each review
        $query->withCount('comments');
        
        // Filter by type if specified
        if ($request->has('type')) {
            if ($request->type === 'books') {
                $query->where('reviewable_type', 'App\\Models\\Book');
            } elseif ($request->type === 'movies') {
                $query->where('reviewable_type', 'App\\Models\\Movie');
            }
        }
        
        // Get paginated reviews
        $reviews = $query->latest()->paginate(10)->withQueryString();
        
        return view('user.reviews', compact('reviews'));
    }
    
    /**
     * Display user's comments.
     */
    public function comments(Request $request)
    {
        $user = Auth::user();
        $query = $user->comments()->with(['review', 'review.user', 'review.reviewable']);
        
        // Filter by type if specified
        if ($request->has('type')) {
            $query->whereHas('review', function ($q) use ($request) {
                if ($request->type === 'books') {
                    $q->where('reviewable_type', 'App\\Models\\Book');
                } elseif ($request->type === 'movies') {
                    $q->where('reviewable_type', 'App\\Models\\Movie');
                }
            });
        }
        
        // Get paginated comments
        $comments = $query->latest()->paginate(15)->withQueryString();
        
        return view('user.comments', compact('comments'));
    }
}