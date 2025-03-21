@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Terms of Service</h1>
                
                <div class="prose max-w-none">
                    <p class="mb-4">Last Updated: {{ date('F d, Y') }}</p>
                    
                    <p>Welcome to NYT Media Explorer. Please read these terms carefully before using this application.</p>
                    
                    <h2 class="text-xl font-semibold mt-6 mb-3">Educational Purpose</h2>
                    
                    <p>NYT Media Explorer is an educational project created to demonstrate Laravel development techniques and API integration. By using this application, you acknowledge that it is provided for educational and demonstration purposes only.</p>
                    
                    <h2 class="text-xl font-semibold mt-6 mb-3">User Accounts</h2>
                    
                    <p>When you create an account with us, you agree to provide accurate information and keep it updated. You are responsible for maintaining the confidentiality of your password and account information.</p>
                    
                    <h2 class="text-xl font-semibold mt-6 mb-3">Content and Intellectual Property</h2>
                    
                    <p>All book and movie data displayed in this application is sourced from The New York Times API and is the intellectual property of The New York Times and/or the respective publishers, authors, and studios. This content is used under fair use for educational purposes only.</p>
                    
                    <p>This project is not affiliated with, endorsed by, or sponsored by The New York Times. It is an independent educational project that utilizes The New York Times API in accordance with their developer terms of service.</p>
                    
                    <h2 class="text-xl font-semibold mt-6 mb-3">User-Generated Content</h2>
                    
                    <p>When you post reviews, comments, or other content on this application, you grant us a non-exclusive, royalty-free license to use, store, and display that content in connection with the service.</p>
                    
                    <p>You are solely responsible for the content you post. Content must not be illegal, offensive, or infringing on someone else's rights.</p>
                    
                    <h2 class="text-xl font-semibold mt-6 mb-3">Fair Use and Educational Purpose</h2>
                    
                    <p>This application uses content from The New York Times API under fair use for educational purposes:</p>
                    
                    <ul>
                        <li>To demonstrate techniques for API integration in Laravel applications</li>
                        <li>To show how to build features such as favorites, reviews, and user interactions</li>
                        <li>To provide a realistic learning environment for web development education</li>
                    </ul>
                    
                    <p>No commercial use is intended, and all New York Times content is properly attributed.</p>
                    
                    <h2 class="text-xl font-semibold mt-6 mb-3">Changes to These Terms</h2>
                    
                    <p>As this is an educational project, these terms may be updated for learning purposes. Any significant changes will be posted on this page.</p>
                    
                    <h2 class="text-xl font-semibold mt-6 mb-3">Contact</h2>
                    
                    <p>If you have any questions about these Terms, please contact the project maintainer.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection