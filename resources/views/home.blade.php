@extends('layouts.app')

@section('title', 'Home')

@section('content')

<div class="bg-gradient-to-b from-blue-600 to-blue-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
        <div class="text-center">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold">
                Discover the Best in Books and Movies
            </h1>
            <p class="mt-4 text-lg md:text-xl max-w-3xl mx-auto">
                Explore New York Times bestsellers and movie reviews all in one place.
                Track your favorites, share your thoughts, and discover your next read or watch.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('books.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50">
                    Browse Bestsellers
                </a>
                <a href="{{ route('movies.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-900 hover:bg-blue-950">
                    Explore Movie Reviews
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Featured Content Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Featured Content</h2>
        <p class="mt-2 text-gray-600">Explore what's trending this week</p>
    </div>

    <!-- Featured Books -->
    <div class="mb-16">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">Bestselling Books</h3>
            <a href="{{ route('books.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                View all
            </a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($featuredBooks as $book)
                <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <a href="{{ route('books.show', $book) }}">
                        <div class="aspect-w-2 aspect-h-3 w-full">
                            <img class="object-cover w-full h-full" src="{{ $book->image_url ?: 'https://placehold.co/600x900/e2e8f0/1e293b?text=No+Cover' }}" alt="{{ $book->title }}">
                        </div>
                        <div class="p-4">
                            <h4 class="font-medium text-gray-900 line-clamp-1">{{ $book->title }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $book->author }}</p>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-500">Featured books will appear here once data is loaded.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Featured Movies -->
    <div>
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">Recent Movie Reviews</h3>
            <a href="{{ route('movies.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                View all
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($featuredMovies as $movie)
                <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <a href="{{ route('movies.show', $movie) }}">
                        <div class="aspect-w-16 aspect-h-9 w-full">
                            <img class="object-cover w-full h-full" src="{{ $movie->image_url ?: 'https://placehold.co/800x450/e2e8f0/1e293b?text=No+Image' }}" alt="{{ $movie->display_title }}">
                        </div>
                        <div class="p-4">
                            <h4 class="font-medium text-gray-900">{{ $movie->display_title }}</h4>
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $movie->summary }}</p>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-500">Featured movie reviews will appear here once data is loaded.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Media Explorer Stats</h2>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p class="text-3xl font-bold text-blue-600">{{ $stats['books'] ?? 0 }}</p>
                <p class="text-gray-600 mt-1">Books</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p class="text-3xl font-bold text-blue-600">{{ $stats['movies'] ?? 0 }}</p>
                <p class="text-gray-600 mt-1">Movies</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p class="text-3xl font-bold text-blue-600">{{ $stats['reviews'] ?? 0 }}</p>
                <p class="text-gray-600 mt-1">User Reviews</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p class="text-3xl font-bold text-blue-600">{{ $stats['users'] ?? 0 }}</p>
                <p class="text-gray-600 mt-1">Community Members</p>
            </div>
        </div>
    </div>
</div>
@endsection