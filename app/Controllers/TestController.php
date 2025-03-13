<?php

namespace App\Http\Controllers;

use App\Repositories\BookRepository;
use App\Repositories\MovieRepository;
use Illuminate\Http\Request;

class TestController extends Controller
{
    protected $bookRepository;
    protected $movieRepository;

    public function __construct(BookRepository $bookRepository, MovieRepository $movieRepository)
    {
        $this->bookRepository = $bookRepository;
        $this->movieRepository = $movieRepository;
    }

    public function testBooks()
    {
        $lists = $this->bookRepository->getAllLists();
        
        if (!empty($lists)) {
            $firstList = $lists[0]['list_name_encoded'] ?? 'hardcover-fiction';
            $books = $this->bookRepository->getBestsellers($firstList);
            
            return view('test.books', [
                'lists' => $lists,
                'books' => $books,
                'currentList' => $firstList
            ]);
        }
        
        return 'No lists found';
    }

    public function testMovies()
    {
        $movies = $this->movieRepository->getRecentReviews();
        
        return view('test.movies', [
            'movies' => $movies
        ]);
    }
}