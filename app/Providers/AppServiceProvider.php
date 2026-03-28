<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
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
        // Share student wallet and user data to all student views
        View::composer(['layouts.student', 'student.*'], function ($view) {
            if (auth()->check() && auth()->user()->role === 'student') {
                $user = auth()->user();
                
                // Eager load wallet to avoid N+1
                $user->loadMissing('studentWallet');
                
                $view->with([
                    'totalCoins' => $user->studentWallet?->total_coins ?? 0,
                    'headerUser' => $user,
                ]);
            }
        });
    }
}
