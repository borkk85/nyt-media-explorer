<footer class="bg-white border-t border-gray-200">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                <p class="text-sm text-gray-500">
                    &copy; {{ date('Y') }} NYT Media Explorer
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    Powered by The New York Times APIs
                </p>
            </div>
            
            <div class="flex space-x-6">
                <a href="https://developer.nytimes.com" target="_blank" class="text-sm text-gray-500 hover:text-gray-700">
                    NYT API
                </a>
                <a href="{{ route('about') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    About
                </a>
                <a href="{{ route('privacy') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    Privacy
                </a>
                <a href="{{ route('terms') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    Terms
                </a>
            </div>
        </div>
    </div>
</footer>