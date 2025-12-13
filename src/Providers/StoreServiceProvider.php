<?php

namespace MVPS\Lumis\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use MVPS\Lumis\MivaStoreManager;
use MVPS\Lumis\Services\StoreService;

class StoreServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * @inheritdoc
     */
    public function register(): void
    {
        $this->app->singleton(
            MivaStoreManager::class,
            fn ($app) => new \MVPS\Lumis\MivaStoreManager($app)
        );

        $this->app->singleton(
            StoreService::class,
            fn ($app) => $app->make(MivaStoreManager::class)->connection()
        );
    }

    /**
     * @inheritdoc
     *
     * @return array<int, class-string>
     */
    public function provides(): array
    {
        return [
            MivaStoreManager::class,
            StoreService::class,
        ];
    }
}
