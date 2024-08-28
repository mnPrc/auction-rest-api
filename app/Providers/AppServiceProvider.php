<?php

namespace App\Providers;

use App\Models\Item;
use App\Observers\ItemObserver;
use App\Services\AuctionService;
use App\Services\BuyNowService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AuctionService::class, function () {
            return new AuctionService();
        });
        $this->app->singleton(BuyNowService::class, function () {
            return new BuyNowService();
        });
    }

    /**
     * Bootstrap any application services.
     */
}
