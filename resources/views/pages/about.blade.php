@extends('layouts.app')

@section('title', 'About NYT Media Explorer')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">About NYT Media Explorer</h1>
                
                <div class="prose max-w-none">
                    <p>NYT Media Explorer is a learning project created to explore Laravel development and API integration techniques. This application showcases how to build a modern web application using Laravel, with features like:</p>
                    
                    <ul>
                        <li>Integration with The New York Times API</li>
                        <li>User authentication and profiles</li>
                        <li>Interactive favorites and bookmarking</li>
                        <li>Responsive design with Tailwind CSS</li>
                        <li>Background jobs for data processing</li>
                    </ul>
                    
                    <h2 class="text-xl font-semibold mt-6 mb-3">Educational Purpose</h2>
                    
                    <p>This project is built solely for educational purposes to demonstrate Laravel development techniques and API integration. All book and movie data is sourced from The New York Times API and is used under fair use for educational purposes.</p>
                    
                    <h2 class="text-xl font-semibold mt-6 mb-3">Technology Stack</h2>
                    
                    <p>NYT Media Explorer is built with the following technologies:</p>
                    
                    <ul>
                        <li><strong>Laravel</strong> - PHP framework providing the application foundation</li>
                        <li><strong>MySQL</strong> - Database for storing user data and caching API responses</li>
                        <li><strong>Tailwind CSS</strong> - Utility-first CSS framework for the user interface</li>
                        <li><strong>Alpine.js</strong> - Lightweight JavaScript framework for interactive components</li>
                        <li><strong>New York Times API</strong> - Data source for books and movie reviews</li>
                    </ul>
                    
                    <h2 class="text-xl font-semibold mt-6 mb-3">Credits</h2>
                    
                    <p>This project wouldn't be possible without:</p>
                    
                    <ul>
                        <li>The New York Times for providing their excellent APIs</li>
                        <li>The Laravel community for creating such a powerful framework</li>
                        <li>The open source community for all the tools and libraries used</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection