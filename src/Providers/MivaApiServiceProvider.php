<?php

namespace MVPS\Lumis\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use MVPS\Lumis\MivaApiManager;
use MVPS\Lumis\Services\ApiClientService;
use pdeans\Miva\Api\Client as MivaApiClient;

class MivaApiServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * @inheritdoc
     */
    public function register(): void
    {
        $this->app->singleton(
            MivaApiManager::class,
            fn ($app) => new MivaApiManager($app)
        );

        $this->app->singleton(
            ApiClientService::class,
            fn ($app) => $app->make(MivaApiManager::class)->connection()
        );

        $this->app->bind(
            MivaApiClient::class,
            fn ($app) => $app->make(ApiClientService::class)->client()
        );
    }

    /**
     * @inheritdoc
     */
    public function provides(): array
    {
        return [
            MivaApiManager::class,
            ApiClientService::class,
        ];
    }
}
