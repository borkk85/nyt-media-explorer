<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    
    /**
     * Store a newly created comment.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'review_id' => 'required|exists:reviews,id',
            'content' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $user = Auth::user();
        $reviewId = $request->review_id;
        
        // Create new comment
        $comment = Comment::create([
            'user_id' => $user->id,
            'review_id' => $reviewId,
            'content' => $request->content,
        ]);
        
        return redirect()->back()
            ->with('success', 'Comment added successfully');
    }
    
    /**
     * Show the form for editing the specified comment.
     */
    public function edit(Comment $comment)
    {
        // Check if user is authorized to edit
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }
        
        // Load the related review and its reviewable
        $comment->load(['review', 'review.reviewable']);
        
        return view('comments.edit', compact('comment'));
    }
    
    /**
     * Update the specified comment.
     */
    public function update(Request $request, Comment $comment)
    {
        // Check if user is authorized to update
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }
        
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Update comment
        $comment->update([
            'content' => $request->content,
        ]);
        
        return redirect()->route('reviews.show', $comment->review_id)
            ->with('success', 'Comment updated successfully');
    }
    
    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment)
    {
        // Check if user is authorized to delete
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }
        
        // Delete comment
        $comment->delete();
        
        // If request is AJAX, return JSON response
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        // Otherwise redirect to the review page
        return redirect()->back()
            ->with('success', 'Comment deleted successfully');
    }
}