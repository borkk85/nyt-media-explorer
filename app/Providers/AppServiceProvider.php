<?php

namespace App\Providers;

use App\Services\NYT\BooksService;
use App\Services\NYT\MoviesService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BooksService::class, function ($app) {
            return new BooksService();
        });
    
        $this->app->singleton(MoviesService::class, function ($app) {
            return new MoviesService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        config(['services.nyt.api_key' => env('NYT_API_KEY')]);

    }
}
