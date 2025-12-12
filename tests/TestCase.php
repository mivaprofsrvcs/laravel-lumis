<?php

namespace Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use MVPS\Lumis\Providers\LumisServiceProvider;
use MVPS\Lumis\Providers\StoreServiceProvider;
use MVPS\Lumis\Providers\MivaApiServiceProvider;

class TestCase extends Orchestra
{
    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [
            LumisServiceProvider::class,
            StoreServiceProvider::class,
            MivaApiServiceProvider::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('miva', [
            'default' => 'default',
            'connections' => [
                'default' => [
                    'api' => [
                        'key' => 'priv_key',
                        'token' => 'token_123',
                        'url' => 'https://default.test/mm5/json.mvc',
                        'verify_ssl' => false,
                    ],
                    'store' => [
                        'auth' => ['username' => '', 'password' => ''],
                        'code' => 's01',
                        'url' => 'https://default.test',
                        'graphics_path' => 'graphics/00000001/',
                        'root_path' => '/mm5/',
                    ],
                ],
                'store02' => [
                    'api' => [
                        'key' => 'priv_key',
                        'token' => 'token_123',
                        'url' => 'https://store02.test/mm5/json.mvc',
                        'verify_ssl' => true,
                    ],
                    'store' => [
                        'auth' => ['username' => 'u', 'password' => 'p'],
                        'code' => 's02',
                        'url' => 'https://store02.test',
                        'graphics_path' => 'graphics/00000002/',
                        'root_path' => '/mm5/',
                    ],
                ],
            ],
        ]);
    }
}
