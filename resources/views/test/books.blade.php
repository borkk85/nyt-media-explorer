<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test NYT Books API</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">NYT Bestsellers</h1>
        
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">Available Lists</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($lists as $list)
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                        {{ $list['display_name'] }}
                    </span>
                @endforeach
            </div>
        </div>
        
        <h2 class="text-2xl font-semibold mb-4">{{ ucwords(str_replace('-', ' ', $currentList)) }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($books as $book)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    @if($book->image_url)
                        <img src="{{ $book->image_url }}" alt="{{ $book->title }}" class="w-full h-64 object-cover">
                    @else
                        <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-500">No image available</span>
                        </div>
                    @endif
                    
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-1">{{ $book->title }}</h3>
                        <p class="text-gray-600 mb-2">by {{ $book->author }}</p>
                        <p class="text-sm text-gray-500 mb-3">{{ $book->publisher }}</p>
                        <p class="text-sm mb-4">{{ $book->description }}</p>
                        
                        @if($book->weeks_on_list)
                            <p class="text-sm font-medium">{{ $book->weeks_on_list }} weeks on list</p>
                        @endif
                        
                        @if($book->amazon_url)
                            <a href="{{ $book->amazon_url }}" target="_blank" class="mt-3 inline-block text-blue-600 hover:underline">View on Amazon</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>