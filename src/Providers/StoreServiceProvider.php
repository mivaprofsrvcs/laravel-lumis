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
			$config = (array) $app['config']->get('miva.store', []);
			$auth = (array) ($config['auth'] ?? []);

			$username = trim((string) ($auth['username'] ?? ''));
			$password = trim((string) ($auth['password'] ?? ''));

			$auth = $username !== '' && $password !== ''
				? ['username' => $username, 'password' => $password]
				: [];

			return new StoreService(
				code: (string) ($config['code'] ?? ''),
				url: (string) ($config['url'] ?? ''),
				graphicsPath: (string) ($config['graphics_path'] ?? 'graphics/'),
				rootPath: (string) ($config['root_path'] ?? '/mm5/'),
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
