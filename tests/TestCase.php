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
			'api' => [
				'key'        => 'priv_key',
				'token'      => 'token_123',
				'url'        => 'https://example.test/mm5/json.mvc',
				'verify_ssl' => false,
			],
			'store' => [
				'code'          => 's01',
				'url'           => 'https://example.test',
				'graphics_path' => 'graphics/00000001/',
				'root_path'     => '/mm5/',
				'auth'          => [
					'username' => '',
					'password' => '',
				],
			],
		]);
	}
}
