<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test NYT Movie Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">NYT Movie Reviews</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            @foreach($movies as $movie)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold mb-2">{{ $movie->display_title }}</h3>
                        <p class="text-gray-600 mb-1">{{ $movie->byline }}</p>
                        <p class="text-gray-500 text-sm mb-3">
                            Published: {{ $movie->publication_date ? $movie->publication_date->format('M d, Y') : 'Unknown' }}
                        </p>
                        <p class="mb-4">{{ $movie->summary }}</p>
                        
                        @if($movie->nyt_url)
                            <a href="{{ $movie->nyt_url }}" target="_blank" class="text-blue-600 hover:underline">Read Review</a>
                        @endif
                    </div>
                    
                    @if($movie->image_url)
                        <img src="{{ $movie->image_url }}" alt="{{ $movie->display_title }}" class="w-full h-48 object-cover">
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>