<?php

namespace App\Providers;

use App\Models\CartItem;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Configure route model binding for CartItem to scope to authenticated user
        Route::bind('cartItem', function ($value) {
            return CartItem::where('id', $value)
                ->where('user_id', auth()->id())
                ->firstOrFail();
        });
    }
}
