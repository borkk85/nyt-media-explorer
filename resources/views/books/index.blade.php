@extends('layouts.app')

@section('title', 'Bestselling Books')

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">NYT Bestselling Books</h1>
                <p class="mt-1 text-sm text-gray-600">Browse the latest bestsellers from The New York Times</p>
            </div>
            
            <div class="mt-4 md:mt-0">
                <form method="GET" action="{{ route('books.index') }}" class="flex flex-wrap gap-2">
                    <select name="list" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">All Categories</option>
                        @foreach($lists as $list)
                            <option value="{{ $list['list_name_encoded'] }}" {{ request('list') == $list['list_name_encoded'] ? 'selected' : '' }}>
                                {{ $list['display_name'] }}
                            </option>
                        @endforeach
                    </select>
                    
                    <select name="sort" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="weeks" {{ request('sort') == 'weeks' ? 'selected' : '' }}>Weeks on List</option>
                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title</option>
                        <option value="author" {{ request('sort') == 'author' ? 'selected' : '' }}>Author</option>
                    </select>
                </form>
            </div>
        </div>
        
        <!-- Current List Information -->
        @if($currentList)
            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-8">
                <h2 class="font-medium text-blue-800">{{ $currentList['display_name'] }}</h2>
                <p class="mt-1 text-sm text-blue-600">{{ $currentList['description'] }}</p>
                <p class="mt-2 text-xs text-blue-500">Updated: {{ $currentList['updated'] }}</p>
            </div>
        @endif
        
        <!-- Books Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @forelse($books as $book)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <a href="{{ route('books.show', $book) }}" class="block h-full">
                        <div class="aspect-w-2 aspect-h-3 w-full">
                            <img class="object-cover w-full h-full" src="{{ $book->image_url ?: 'https://placehold.co/600x900/e2e8f0/1e293b?text=No+Cover' }}" alt="{{ $book->title }}">
                        </div>
                        <div class="p-4">
                            <h3 class="font-medium text-gray-900 line-clamp-2">{{ $book->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $book->author }}</p>
                            
                            @if($book->weeks_on_list)
                                <div class="mt-2 inline-flex items-center px-2 py-1 bg-blue-100 text-xs text-blue-800 rounded-full">
                                    {{ $book->weeks_on_list }} {{ Str::plural('week', $book->weeks_on_list) }} on list
                                </div>
                            @endif
                            
                            @auth
                                <div class="mt-3 flex items-center justify-between">
                                    <button 
                                        class="favorite-button text-gray-400 hover:text-yellow-500 transition-colors"
                                        data-id="{{ $book->id }}"
                                        data-type="book"
                                        title="Add to favorites">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="{{ $book->is_favorited ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" color="{{ $book->is_favorited ? 'gold' : 'currentColor' }}">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    </button>
                                </div>
                            @endauth
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <p class="mt-4 text-gray-500">No books found. Please try a different category or check back later.</p>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $books->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle favorite buttons
        document.querySelectorAll('.favorite-button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
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
                        svg.setAttribute('color', 'gold');
                    } else {
                        svg.setAttribute('fill', 'none');
                        svg.setAttribute('color', 'currentColor');
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