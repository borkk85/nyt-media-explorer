@extends('layouts.app')

@section('title', $movie->display_title)

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumbs -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700">Home</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="{{ route('movies.index') }}" class="text-gray-500 hover:text-gray-700 ml-1 md:ml-2">Movies</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="text-gray-400 ml-1 md:ml-2 line-clamp-1">{{ $movie->display_title }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <!-- Movie Details -->
        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
            <!-- Movie Image -->
            <div class="lg:col-span-1">
                <div class="aspect-w-16 aspect-h-9 overflow-hidden rounded-lg border border-gray-200">
                    <img src="{{ $movie->image_url ?: 'https://placehold.co/800x450/e2e8f0/1e293b?text=No+Image' }}" alt="{{ $movie->display_title }}" class="object-cover w-full h-full">
                </div>
                
                @auth
                <div class="mt-4 flex flex-col space-y-2">
                    <button id="favorite-button" 
                        class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium {{ $movie->is_favorited ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        data-id="{{ $movie->id }}"
                        data-type="movie">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="{{ $movie->is_favorited ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        {{ $movie->is_favorited ? 'Remove from Favorites' : 'Add to Favorites' }}
                    </button>
                    
                    @if($movie->nyt_url)
                        <a href="{{ $movie->nyt_url }}" target="_blank" class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Read Full Review
                        </a>
                    @endif
                </div>
                @endauth
            </div>
            
            <!-- Movie Info -->
            <div class="mt-6 lg:mt-0 lg:col-span-2">
                <h1 class="text-2xl font-bold text-gray-900">{{ $movie->display_title }}</h1>
                
                @if($movie->byline)
                    <p class="mt-2 text-sm text-gray-600">Review by <span class="font-medium">{{ $movie->byline }}</span></p>
                @endif
                
                @if($movie->publication_date)
                    <p class="mt-1 text-sm text-gray-500">Reviewed {{ $movie->publication_date->format('F j, Y') }}</p>
                @endif
                
                @if($movie->mpaa_rating)
                    <div class="mt-3 inline-flex items-center px-3 py-1 bg-gray-100 text-sm text-gray-800 rounded-full">
                        {{ $movie->mpaa_rating }}
                    </div>
                @endif
                
                @if($movie->critics_pick)
                    <div class="mt-3 inline-flex items-center px-3 py-1 bg-yellow-100 text-sm text-yellow-800 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        Critics' Pick
                    </div>
                @endif
                
                <!-- Movie Summary -->
                <div class="mt-6">
                    <h2 class="text-lg font-medium text-gray-900">Summary</h2>
                    <div class="mt-2 prose prose-blue max-w-none text-gray-600">
                        <p>{{ $movie->summary ?: 'No summary available.' }}</p>
                    </div>
                </div>
                
                <!-- Movie Headline -->
                @if($movie->headline)
                    <div class="mt-6">
                        <h2 class="text-lg font-medium text-gray-900">Review Headline</h2>
                        <p class="mt-2 text-gray-600 italic">{{ $movie->headline }}</p>
                    </div>
                @endif
                
                <!-- User Reviews Section -->
                <div class="mt-10 pt-6 border-t border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">User Reviews</h2>
                    
                    @if($movie->reviews->count() > 0)
                        <div class="mt-4 space-y-6">
                            @foreach($movie->reviews as $review)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">{{ $review->user->name }}</div>
                                            <div class="ml-4 flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="h-5 w-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</div>
                                    </div>
                                    <h3 class="mt-2 text-base font-medium text-gray-900">{{ $review->title }}</h3>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <p>{{ $review->content }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-4 text-gray-500">No reviews yet. Be the first to share your thoughts!</p>
                    @endif
                    
                    @auth
                        <!-- Add Review Form -->
                        <div class="mt-8">
                            <h3 class="text-base font-medium text-gray-900">Write a Review</h3>
                            <form action="{{ route('reviews.store') }}" method="POST" class="mt-4">
                                @csrf
                                <input type="hidden" name="reviewable_id" value="{{ $movie->id }}">
                                <input type="hidden" name="reviewable_type" value="movie">
                                
                                <div class="mb-4">
                                    <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                    <div class="flex items-center">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <input type="radio" id="rating-{{ $i }}" name="rating" value="{{ $i }}" class="hidden peer" required>
                                                <label for="rating-{{ $i }}" class="cursor-pointer p-1">
                                                    <svg class="w-6 h-6 text-gray-300 peer-checked:text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </label>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Review Title</label>
                                    <input type="text" name="title" id="title" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Review Content</label>
                                    <textarea name="content" id="content" rows="4" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                                </div>
                                
                                <div>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Submit Review
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="mt-8 bg-gray-50 p-4 rounded-lg text-center">
                            <p class="text-gray-700">Please <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">log in</a> to write a review.</p>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
        
        <!-- Related Movies (recent reviews) -->
        @if($relatedMovies->count() > 0)
            <div class="mt-16 pt-8 border-t border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">More Recent Reviews</h2>
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($relatedMovies as $relatedMovie)
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                            <a href="{{ route('movies.show', $relatedMovie) }}" class="block h-full">
                                <div class="aspect-w-16 aspect-h-9 w-full">
                                    <img class="object-cover w-full h-full" src="{{ $relatedMovie->image_url ?: 'https://placehold.co/800x450/e2e8f0/1e293b?text=No+Image' }}" alt="{{ $relatedMovie->display_title }}">
                                </div>
                                <div class="p-4">
                                    <h3 class="font-medium text-gray-900">{{ $relatedMovie->display_title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">Reviewed {{ $relatedMovie->publication_date->format('M d, Y') }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle favorite button
        const favoriteButton = document.getElementById('favorite-button');
        if (favoriteButton) {
            favoriteButton.addEventListener('click', function() {
                const id = this.dataset.id;
                const type = this.dataset.type;
                
                // Make ajax request to toggle favorite status
                fetch(`/favorites/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id: id,
                        type: type
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Update the button appearance based on the new state
                    const svg = this.querySelector('svg');
                    if (data.favorited) {
                        svg.setAttribute('fill', 'currentColor');
                        this.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        this.classList.add('bg-yellow-100', 'text-yellow-700', 'hover:bg-yellow-200');
                        this.innerHTML = this.innerHTML.replace('Add to Favorites', 'Remove from Favorites');
                    } else {
                        svg.setAttribute('fill', 'none');
                        this.classList.remove('bg-yellow-100', 'text-yellow-700', 'hover:bg-yellow-200');
                        this.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        this.innerHTML = this.innerHTML.replace('Remove from Favorites', 'Add to Favorites');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        }
        
        // Handle rating stars in review form
        const ratingInputs = document.querySelectorAll('input[name="rating"]');
        ratingInputs.forEach(input => {
            input.addEventListener('change', function() {
                const value = this.value;
                ratingInputs.forEach((input, index) => {
                    const star = input.nextElementSibling.querySelector('svg');
                    if (index < value) {
                        star.classList.remove('text-gray-300');
                        star.classList.add('text-yellow-400');
                    } else {
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-300');
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection