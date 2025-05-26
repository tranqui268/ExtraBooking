<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
   
    public function register(): void
    {
        
    }

 
    public function boot(): void
    {
        Route::prefix('api')
        ->middleware('api')
        ->group(base_path('routes/api.php'));
    }
}
