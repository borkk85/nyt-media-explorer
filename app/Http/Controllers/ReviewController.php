<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Movie;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except(['show']);
    }
    
    /**
     * Display the specified review with comments.
     */
    public function show(Review $review)
    {
        // Load comments with their users
        $review->load(['comments' => function ($query) {
            $query->with('user')->latest();
        }]);
        
        // Load the related item (book or movie)
        $review->load('reviewable', 'user');
        
        return view('reviews.show', compact('review'));
    }
    
    /**
     * Store a newly created review.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reviewable_id' => 'required|integer',
            'reviewable_type' => 'required|in:book,movie',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $user = Auth::user();
        $type = $request->reviewable_type;
        $id = $request->reviewable_id;
        
        // Determine model class based on type
        $modelClass = $type === 'book' ? Book::class : Movie::class;
        $model = $modelClass::find($id);
        
        if (!$model) {
            return redirect()->back()
                ->with('error', 'Item not found');
        }
        
        // Check if user already reviewed this item
        $existingReview = Review::where('user_id', $user->id)
            ->where('reviewable_id', $id)
            ->where('reviewable_type', get_class($model))
            ->first();
        
        if ($existingReview) {
            return redirect()->back()
                ->with('error', 'You have already reviewed this item');
        }
        
        // Create new review
        $review = Review::create([
            'user_id' => $user->id,
            'reviewable_id' => $id,
            'reviewable_type' => get_class($model),
            'rating' => $request->rating,
            'title' => $request->title,
            'content' => $request->content,
        ]);
        
        return redirect()->back()
            ->with('success', 'Review submitted successfully');
    }
    
    /**
     * Show the form for editing the specified review.
     */
    public function edit(Review $review)
    {
        // Check if user is authorized to edit
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }
        
        // Load the related item (book or movie)
        $review->load('reviewable');
        
        return view('reviews.edit', compact('review'));
    }
    
    /**
     * Update the specified review.
     */
    public function update(Request $request, Review $review)
    {
        // Check if user is authorized to update
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }
        
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Update review
        $review->update([
            'rating' => $request->rating,
            'title' => $request->title,
            'content' => $request->content,
        ]);
        
        // Redirect to the appropriate page
        if ($review->reviewable_type === 'App\\Models\\Book') {
            return redirect()->route('books.show', $review->reviewable_id)
                ->with('success', 'Review updated successfully');
        } else {
            return redirect()->route('movies.show', $review->reviewable_id)
                ->with('success', 'Review updated successfully');
        }
    }
    
    /**
     * Remove the specified review.
     */
    public function destroy(Review $review)
    {
        // Check if user is authorized to delete
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }
        
        // Delete review
        $review->delete();
        
        // If request is AJAX, return JSON response
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        // Otherwise redirect to the appropriate page
        return redirect()->back()
            ->with('success', 'Review deleted successfully');
    }
}