<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Movie;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the application dashboard.
     */
    public function index()
    {
        // Get featured books (newest bestsellers)
        $featuredBooks = Book::orderBy('created_at', 'desc')
            ->take(4)
            ->get();
            
        // Get featured movies (newest reviews)
        $featuredMovies = Movie::orderBy('created_at', 'desc')
            ->take(3)
            ->get();
            
        // Get application stats
        $stats = [
            'books' => Book::count(),
            'movies' => Movie::count(),
            'reviews' => Review::count(),
            'users' => User::count(),
        ];
        
        return view('home', compact('featuredBooks', 'featuredMovies', 'stats'));
    }
}