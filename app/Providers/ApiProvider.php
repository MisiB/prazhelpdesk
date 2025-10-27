<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\interfaces\ihttpService;
use App\implementations\_httpService;

class ApiProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(ihttpService::class, _httpService::class);
    }
}
