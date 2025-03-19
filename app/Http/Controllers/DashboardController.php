<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Review;
use App\Models\UserFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user statistics
        $stats = [
            'favorites' => $user->favorites()->count(),
            'reviews' => $user->reviews()->count(),
            'comments' => $user->comments()->count(),
        ];
        
        // Get recent activity (favorites, reviews, comments)
        $recentActivity = [];
        
        // Add recent favorites
        $favorites = $user->favorites()
            ->with('favoritable')
            ->latest()
            ->take(5)
            ->get();
            
        foreach ($favorites as $favorite) {
            $content = '';
            $url = '';
            
            if ($favorite->favoritable_type === 'App\\Models\\Book') {
                $content = $favorite->favoritable->title;
                $url = route('books.show', $favorite->favoritable);
            } elseif ($favorite->favoritable_type === 'App\\Models\\Movie') {
                $content = $favorite->favoritable->display_title;
                $url = route('movies.show', $favorite->favoritable);
            }
            
            $recentActivity[] = [
                'type' => 'Favorited',
                'content' => $content,
                'date' => $favorite->created_at->format('M d, Y'),
                'url' => $url,
            ];
        }
        
        // Add recent reviews
        $reviews = $user->reviews()
            ->with('reviewable')
            ->latest()
            ->take(5)
            ->get();
            
        foreach ($reviews as $review) {
            $content = '';
            $url = '';
            
            if ($review->reviewable_type === 'App\\Models\\Book') {
                $content = $review->reviewable->title;
                $url = route('books.show', $review->reviewable);
            } elseif ($review->reviewable_type === 'App\\Models\\Movie') {
                $content = $review->reviewable->display_title;
                $url = route('movies.show', $review->reviewable);
            }
            
            $recentActivity[] = [
                'type' => 'Reviewed',
                'content' => $content,
                'date' => $review->created_at->format('M d, Y'),
                'url' => $url,
            ];
        }
        
        // Add recent comments
        $comments = $user->comments()
            ->with(['review', 'review.reviewable'])
            ->latest()
            ->take(5)
            ->get();
            
        foreach ($comments as $comment) {
            $content = '';
            $url = '';
            
            if ($comment->review->reviewable_type === 'App\\Models\\Book') {
                $content = $comment->review->reviewable->title;
                $url = route('books.show', $comment->review->reviewable);
            } elseif ($comment->review->reviewable_type === 'App\\Models\\Movie') {
                $content = $comment->review->reviewable->display_title;
                $url = route('movies.show', $comment->review->reviewable);
            }
            
            $recentActivity[] = [
                'type' => 'Commented',
                'content' => $content,
                'date' => $comment->created_at->format('M d, Y'),
                'url' => $url,
            ];
        }
        
        // Sort activity by date (newest first)
        usort($recentActivity, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        // Limit to 10 items
        $recentActivity = array_slice($recentActivity, 0, 10);
        
        return view('dashboard', compact('stats', 'recentActivity'));
    }
}