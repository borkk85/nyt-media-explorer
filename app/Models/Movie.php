<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'display_title',
        'byline',
        'headline',
        'summary',
        'publication_date',
        'mpaa_rating',
        'critics_pick',
        'image_url',
        'nyt_url',
        'last_fetched_at'
    ];

    protected $casts = [
        'critics_pick' => 'boolean',
        'publication_date' => 'date',
        'last_fetched_at' => 'datetime',
    ];

    public function userFavorites()
    {
        return $this->morphMany(UserFavorite::class, 'favoritable');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
