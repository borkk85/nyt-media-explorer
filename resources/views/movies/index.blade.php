@extends('layouts.app')

@section('title', 'Movie Reviews')

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">NYT Movie Reviews</h1>
                <p class="mt-1 text-sm text-gray-600">Explore film reviews from The New York Times critics</p>
            </div>
            
            <div class="mt-4 md:mt-0">
                <form method="GET" action="{{ route('movies.index') }}" class="flex flex-wrap gap-2">
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                    
                    <select name="sort" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title</option>
                    </select>
                </form>
            </div>
        </div>
        
        <!-- Search Box -->
        <div class="mb-8">
            <form action="{{ route('movies.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                <div class="flex-grow">
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search movie reviews..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 pl-10">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Search
                </button>
                @if(request('q'))
                    <a href="{{ route('movies.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Clear
                    </a>
                @endif
            </form>
        </div>
        
        <!-- Movies List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($movies as $movie)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <a href="{{ route('movies.show', $movie) }}" class="block">
                        <div class="aspect-w-16 aspect-h-9 w-full">
                            <img class="object-cover w-full h-full" src="{{ $movie->image_url ?: 'https://placehold.co/800x450/e2e8f0/1e293b?text=No+Image' }}" alt="{{ $movie->display_title }}">
                        </div>
                        <div class="p-4">
                            <h3 class="font-medium text-gray-900">{{ $movie->display_title }}</h3>
                            @if($movie->publication_date)
                                <p class="text-sm text-gray-500 mt-1">Reviewed {{ $movie->publication_date->format('M d, Y') }}</p>
                            @endif
                            <p class="text-sm text-gray-600 mt-2 line-clamp-3">{{ $movie->summary }}</p>
                            
                            @auth
                                <div class="mt-3 flex items-center justify-between">
                                    <button 
                                        class="favorite-button text-gray-400 hover:text-yellow-500 transition-colors"
                                        data-id="{{ $movie->id }}"
                                        data-type="movie"
                                        title="Add to favorites">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="{{ $movie->is_favorited ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" color="{{ $movie->is_favorited ? 'gold' : 'currentColor' }}">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                    </svg>
                    <p class="mt-4 text-gray-500">
                        @if(request('q'))
                            No movies found matching "{{ request('q') }}". Please try a different search term.
                        @else
                            No movie reviews found. Please check back later.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $movies->links() }}
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
@endsection