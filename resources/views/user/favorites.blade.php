@extends('layouts.app')

@section('title', 'My Favorites')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-semibold mb-6">My Favorites</h1>
                
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
                
                @if(count($favorites) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($favorites as $favorite)
                            @if($favorite->favoritable_type == 'App\\Models\\Book')
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                                    <a href="{{ route('books.show', $favorite->favoritable->id) }}" class="block h-full">
                                        <div class="aspect-w-2 aspect-h-3 w-full">
                                            <img class="object-cover w-full h-full" src="{{ $favorite->favoritable->image_url ?: 'https://placehold.co/600x900/e2e8f0/1e293b?text=No+Cover' }}" alt="{{ $favorite->favoritable->title }}">
                                        </div>
                                        <div class="p-4">
                                            <div class="inline-flex items-center px-2 py-1 mb-2 bg-blue-100 text-xs text-blue-800 rounded-full">
                                                Book
                                            </div>
                                            <h3 class="font-medium text-gray-900 line-clamp-2">{{ $favorite->favoritable->title }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">{{ $favorite->favoritable->author }}</p>
                                            
                                            <div class="mt-3 flex items-center justify-between">
                                                <span class="text-xs text-gray-500">Added {{ $favorite->created_at->diffForHumans() }}</span>
                                                <button 
                                                    class="favorite-button text-yellow-500 hover:text-gray-400 transition-colors"
                                                    data-id="{{ $favorite->favoritable->id }}"
                                                    data-type="book"
                                                    title="Remove from favorites">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @elseif($favorite->favoritable_type == 'App\\Models\\Movie')
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                                    <a href="{{ route('movies.show', $favorite->favoritable->id) }}" class="block h-full">
                                        <div class="aspect-w-16 aspect-h-9 w-full">
                                            <img class="object-cover w-full h-full" src="{{ $favorite->favoritable->image_url ?: 'https://placehold.co/800x450/e2e8f0/1e293b?text=No+Image' }}" alt="{{ $favorite->favoritable->display_title }}">
                                        </div>
                                        <div class="p-4">
                                            <div class="inline-flex items-center px-2 py-1 mb-2 bg-red-100 text-xs text-red-800 rounded-full">
                                                Movie
                                            </div>
                                            <h3 class="font-medium text-gray-900 line-clamp-2">{{ $favorite->favoritable->display_title }}</h3>
                                            @if($favorite->favoritable->publication_date)
                                                <p class="text-sm text-gray-500 mt-1">Reviewed {{ $favorite->favoritable->publication_date->format('M d, Y') }}</p>
                                            @endif
                                            
                                            <div class="mt-3 flex items-center justify-between">
                                                <span class="text-xs text-gray-500">Added {{ $favorite->created_at->diffForHumans() }}</span>
                                                <button 
                                                    class="favorite-button text-yellow-500 hover:text-gray-400 transition-colors"
                                                    data-id="{{ $favorite->favoritable->id }}"
                                                    data-type="movie"
                                                    title="Remove from favorites">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $favorites->links() }}
                    </div>
                @else
                    <div class="bg-gray-50 p-8 rounded-lg text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        <p class="mt-4 text-lg text-gray-700">No favorites yet</p>
                        <p class="mt-2 text-gray-500">Start exploring books and movies to add to your favorites</p>
                        <div class="mt-6 flex flex-col sm:flex-row justify-center gap-4">
                            <a href="{{ route('books.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Browse Books
                            </a>
                            <a href="{{ route('movies.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Explore Movies
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
        
        // Handle favorite buttons
        document.querySelectorAll('.favorite-button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const id = this.dataset.id;
                const type = this.dataset.type;
                const card = this.closest('.bg-white');
                
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
                    // Remove the card with a fade out effect
                    if (!data.favorited) {
                        card.style.transition = 'opacity 0.3s ease';
                        card.style.opacity = '0';
                        setTimeout(() => {
                            card.remove();
                            
                            // Check if there are no more favorites
                            if (document.querySelectorAll('.favorite-button').length === 0) {
                                location.reload(); // Reload to show empty state
                            }
                        }, 300);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    });
</script>
@endpush
@endsection