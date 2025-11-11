<?php

namespace MVPS\Lumis\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use MVPS\Lumis\Services\StoreService;

class StoreServiceProvider extends ServiceProvider implements DeferrableProvider
{
	/**
	 * @inheritdoc
	 */
	public function register(): void
	{
		$this->app->singleton(StoreService::class, function ($app) {
			$config = $app['config'];
			$connection = (string) $config->get('miva.default', 'default');

			$store = (array) $config->get("miva.connections.$connection.store", []);
			$auth = (array) ($store['auth'] ?? []);

			$username = trim((string) ($auth['username'] ?? ''));
			$password = trim((string) ($auth['password'] ?? ''));

			$auth = $username !== '' && $password !== ''
				? ['username' => $username, 'password' => $password]
				: [];

			return new StoreService(
				code: (string) ($store['code'] ?? ''),
				url: (string) ($store['url'] ?? ''),
				graphicsPath: (string) ($store['graphics_path'] ?? 'graphics/'),
				rootPath: (string) ($store['root_path'] ?? '/mm5/'),
				auth: $auth
			);
		});
	}

	/**
	 * @inheritdoc
	 */
	public function provides(): array
	{
		return [StoreService::class];
	}
}
