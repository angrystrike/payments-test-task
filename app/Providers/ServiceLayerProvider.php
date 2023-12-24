<?php

namespace App\Providers;

use App\Services\PaymentService;
use App\Services\PromoCodeService;
use App\Services\CardService;
use App\Interfaces\CardServiceInterface;
use App\Interfaces\PaymentServiceInterface;
use App\Interfaces\PromoCodeServiceInterface;
use Illuminate\Support\ServiceProvider;


class ServiceLayerProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            PaymentServiceInterface::class,
            PaymentService::class
        );

        $this->app->bind(
            PromoCodeServiceInterface::class,
            PromoCodeService::class
        );

        $this->app->bind(
            CardServiceInterface::class,
            CardService::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        
    }
}