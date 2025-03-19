<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Movie;
use App\Models\UserFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    
    /**
     * Toggle favorite status for a book or movie.
     */
    public function toggle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'type' => 'required|in:book,movie',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid input'], 422);
        }
        
        $user = Auth::user();
        $type = $request->type;
        $id = $request->id;
        
        // Determine model class based on type
        $modelClass = $type === 'book' ? Book::class : Movie::class;
        $model = $modelClass::find($id);
        
        if (!$model) {
            return response()->json(['error' => 'Item not found'], 404);
        }
        
        // Check if already favorited
        $favorite = UserFavorite::where('user_id', $user->id)
            ->where('favoritable_id', $id)
            ->where('favoritable_type', get_class($model))
            ->first();
        
        // Toggle favorite status
        if ($favorite) {
            $favorite->delete();
            $favorited = false;
        } else {
            UserFavorite::create([
                'user_id' => $user->id,
                'favoritable_id' => $id,
                'favoritable_type' => get_class($model),
            ]);
            $favorited = true;
        }
        
        return response()->json([
            'success' => true,
            'favorited' => $favorited,
        ]);
    }
}