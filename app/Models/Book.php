<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'nyt_id',
        'title',
        'author',
        'description',
        'isbn13',
        'isbn10',
        'publisher',
        'image_url',
        'published_date',
        'amazon_url',
        'rating',
        'weeks_on_list',
        'list_name',
        'last_fetched_at'
    ];

    protected $casts = [
        'published_date' => 'date',
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
