@extends('layouts.app')

@section('title', $book->title)

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
                        <a href="{{ route('books.index') }}" class="text-gray-500 hover:text-gray-700 ml-1 md:ml-2">Books</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="text-gray-400 ml-1 md:ml-2 line-clamp-1">{{ $book->title }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <!-- Book Details -->
        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
            <!-- Book Image -->
            <div class="lg:col-span-1">
                <div class="aspect-w-2 aspect-h-3 overflow-hidden rounded-lg border border-gray-200">
                    <img src="{{ $book->image_url ?: 'https://placehold.co/600x900/e2e8f0/1e293b?text=No+Cover' }}" alt="{{ $book->title }}" class="object-cover w-full h-full">
                </div>
                
                @auth
                <div class="mt-4 flex flex-col space-y-2">
                    <button id="favorite-button" 
                        class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium {{ $book->is_favorited ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        data-id="{{ $book->id }}"
                        data-type="book">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="{{ $book->is_favorited ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        {{ $book->is_favorited ? 'Remove from Favorites' : 'Add to Favorites' }}
                    </button>
                    
                    @if($book->amazon_url)
                        <a href="{{ $book->amazon_url }}" target="_blank" class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Buy on Amazon
                        </a>
                    @endif
                </div>
                @endauth
            </div>
            
            <!-- Book Info -->
            <div class="mt-6 lg:mt-0 lg:col-span-2">
                <h1 class="text-2xl font-bold text-gray-900">{{ $book->title }}</h1>
                <p class="mt-2 text-lg text-gray-600">by {{ $book->author }}</p>
                
                @if($book->weeks_on_list)
                    <div class="mt-3 inline-flex items-center px-3 py-1 bg-blue-100 text-sm text-blue-800 rounded-full">
                        {{ $book->weeks_on_list }} {{ Str::plural('week', $book->weeks_on_list) }} on list
                    </div>
                @endif
                
                @if($book->list_name)
                    <div class="mt-2 inline-flex items-center px-3 py-1 bg-green-100 text-sm text-green-800 rounded-full">
                        {{ $book->list_name }}
                    </div>
                @endif
                
                <!-- Book Description -->
                <div class="mt-6">
                    <h2 class="text-lg font-medium text-gray-900">Description</h2>
                    <div class="mt-2 prose prose-blue max-w-none text-gray-600">
                        <p>{{ $book->description ?: 'No description available.' }}</p>
                    </div>
                </div>
                
                <!-- Book Details -->
                <div class="mt-6">
                    <h2 class="text-lg font-medium text-gray-900">Details</h2>
                    <dl class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                        @if($book->publisher)
                            <div class="text-sm">
                                <dt class="font-medium text-gray-500">Publisher</dt>
                                <dd class="mt-1 text-gray-900">{{ $book->publisher }}</dd>
                            </div>
                        @endif
                        
                        @if($book->published_date)
                            <div class="text-sm">
                                <dt class="font-medium text-gray-500">Published Date</dt>
                                <dd class="mt-1 text-gray-900">{{ $book->published_date->format('F j, Y') }}</dd>
                            </div>
                        @endif
                        
                        @if($book->isbn13)
                            <div class="text-sm">
                                <dt class="font-medium text-gray-500">ISBN-13</dt>
                                <dd class="mt-1 text-gray-900">{{ $book->isbn13 }}</dd>
                            </div>
                        @endif
                        
                        @if($book->isbn10)
                            <div class="text-sm">
                                <dt class="font-medium text-gray-500">ISBN-10</dt>
                                <dd class="mt-1 text-gray-900">{{ $book->isbn10 }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
                
                <!-- User Reviews Section -->
                <div class="mt-10 pt-6 border-t border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Reader Reviews</h2>
                    
                    @if($book->reviews->count() > 0)
                        <div class="mt-4 space-y-6">
                            @foreach($book->reviews as $review)
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
                                <input type="hidden" name="reviewable_id" value="{{ $book->id }}">
                                <input type="hidden" name="reviewable_type" value="book">
                                
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
        
        <!-- Related Books (same author) -->
        @if($relatedBooks->count() > 0)
            <div class="mt-16 pt-8 border-t border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">More Books by {{ $book->author }}</h2>
                <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    @foreach($relatedBooks as $relatedBook)
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                            <a href="{{ route('books.show', $relatedBook) }}" class="block h-full">
                                <div class="aspect-w-2 aspect-h-3 w-full">
                                    <img class="object-cover w-full h-full" src="{{ $relatedBook->image_url ?: 'https://placehold.co/600x900/e2e8f0/1e293b?text=No+Cover' }}" alt="{{ $relatedBook->title }}">
                                </div>
                                <div class="p-4">
                                    <h3 class="font-medium text-gray-900 line-clamp-2">{{ $relatedBook->title }}</h3>
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