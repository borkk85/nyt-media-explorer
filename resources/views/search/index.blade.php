@extends('layouts.app')

@section('title', 'My Reviews')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-semibold mb-6">My Reviews</h1>
                
                <!-- Tabs -->
                <div class="border-b border-gray-200 mb-6">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                        <li class="mr-2">
                            <a href="#" class="all-tab inline-block p-4 rounded-t-lg border-b-2 {{ !request('type') ? 'active border-blue-600 text-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}" data-type="all">
                                All
                            </a>
                        </li>
                        <li class="mr-2">
                            <a href="#" class="books-tab inline-block p-4 rounded-t-lg border-b-2 {{ request('type') == 'books' ? 'active border-blue-600 text-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}" data-type="books">
                                Books
                            </a>
                        </li>
                        <li class="mr-2">
                            <a href="#" class="movies-tab inline-block p-4 rounded-t-lg border-b-2 {{ request('type') == 'movies' ? 'active border-blue-600 text-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}" data-type="movies">
                                Movies
                            </a>
                        </li>
                    </ul>
                </div>
                
                @if(count($reviews) > 0)
                    <div class="space-y-6">
                        @foreach($reviews as $review)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center">
                                            @if($review->reviewable_type == 'App\\Models\\Book')
                                                <div class="inline-flex items-center px-2 py-1 mr-2 bg-blue-100 text-xs text-blue-800 rounded-full">
                                                    Book
                                                </div>
                                            @elseif($review->reviewable_type == 'App\\Models\\Movie')
                                                <div class="inline-flex items-center px-2 py-1 mr-2 bg-red-100 text-xs text-red-800 rounded-full">
                                                    Movie
                                                </div>
                                            @endif
                                            
                                            <div class="flex items-center ml-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="h-5 w-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                        <span class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</span>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 mr-4">
                                            @if($review->reviewable_type == 'App\\Models\\Book')
                                                <img class="w-20 h-32 object-cover rounded" src="{{ $review->reviewable->image_url ?: 'https://placehold.co/600x900/e2e8f0/1e293b?text=No+Cover' }}" alt="{{ $review->reviewable->title }}">
                                            @elseif($review->reviewable_type == 'App\\Models\\Movie')
                                                <img class="w-20 h-32 object-cover rounded" src="{{ $review->reviewable->image_url ?: 'https://placehold.co/800x450/e2e8f0/1e293b?text=No+Image' }}" alt="{{ $review->reviewable->display_title }}">
                                            @endif
                                        </div>
                                        
                                        <div class="flex-1">
                                            @if($review->reviewable_type == 'App\\Models\\Book')
                                                <h3 class="text-lg font-medium text-gray-900">
                                                    <a href="{{ route('books.show', $review->reviewable->id) }}" class="hover:underline">
                                                        {{ $review->reviewable->title }}
                                                    </a>
                                                </h3>
                                                <p class="text-sm text-gray-600">by {{ $review->reviewable->author }}</p>
                                            @elseif($review->reviewable_type == 'App\\Models\\Movie')
                                                <h3 class="text-lg font-medium text-gray-900">
                                                    <a href="{{ route('movies.show', $review->reviewable->id) }}" class="hover:underline">
                                                        {{ $review->reviewable->display_title }}
                                                    </a>
                                                </h3>
                                                @if($review->reviewable->byline)
                                                    <p class="text-sm text-gray-600">Review by {{ $review->reviewable->byline }}</p>
                                                @endif
                                            @endif
                                            
                                            <h4 class="mt-3 text-base font-medium text-gray-900">{{ $review->title }}</h4>
                                            <p class="mt-2 text-sm text-gray-600">{{ $review->content }}</p>
                                            
                                            <!-- Comments count -->
                                            @if($review->comments_count > 0)
                                                <div class="mt-3 text-sm text-gray-500">
                                                    <a href="{{ route('reviews.show', $review) }}" class="flex items-center hover:text-blue-600">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                                        </svg>
                                                        {{ $review->comments_count }} {{ Str::plural('comment', $review->comments_count) }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="flex-shrink-0 ml-4">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('reviews.edit', $review) }}" class="text-blue-600 hover:text-blue-800">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <button class="delete-review text-red-600 hover:text-red-800" data-id="{{ $review->id }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $reviews->links() }}
                    </div>
                @else
                    <div class="bg-gray-50 p-8 rounded-lg text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <p class="mt-4 text-lg text-gray-700">No reviews yet</p>
                        <p class="mt-2 text-gray-500">Share your thoughts on books and movies you've explored</p>
                        <div class="mt-6 flex flex-col sm:flex-row justify-center gap-4">
                            <a href="{{ route('books.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Review Books
                            </a>
                            <a href="{{ route('movies.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Review Movies
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle tab switching
        const tabs = document.querySelectorAll('.all-tab, .books-tab, .movies-tab');
        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                const type = this.dataset.type;
                const url = new URL(window.location.href);
                
                if (type === 'all') {
                    url.searchParams.delete('type');
                } else {
                    url.searchParams.set('type', type);
                }
                
                window.location.href = url.toString();
            });
        });
        
        // Handle delete review buttons
        document.querySelectorAll('.delete-review').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this review?')) {
                    const reviewId = this.dataset.id;
                    const card = this.closest('.bg-white');
                    
                    // Send delete request
                    fetch(`/reviews/${reviewId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            // Remove the card with a fade out effect
                            card.style.transition = 'opacity 0.3s ease';
                            card.style.opacity = '0';
                            setTimeout(() => {
                                card.remove();
                                
                                // Check if there are no more reviews
                                if (document.querySelectorAll('.delete-review').length === 0) {
                                    location.reload(); // Reload to show empty state
                                }
                            }, 300);
                        } else {
                            alert('Failed to delete review');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the review');
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection