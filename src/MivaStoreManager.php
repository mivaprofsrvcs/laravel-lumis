<?php

namespace MVPS\Lumis;

use Illuminate\Support\Manager;
use InvalidArgumentException;
use MVPS\Lumis\Services\StoreService;

/**
 * @property \Illuminate\Foundation\Application $container
 * @mixin \MVPS\Lumis\Services\StoreService
 */
class MivaStoreManager extends Manager
{
	/**
	 * {@inheritdoc}
	 */
	public function getDefaultDriver()
	{
		return (string) $this->container['config']->get('miva.default', 'default');
	}

	/**
	 * Get a connection by name.
	 *
	 * Alias of `driver()` method.
	 */
	public function connection(?string $name = null): StoreService
	{
		return $this->driver($name);
	}

	/**
	 * Create the store service for a named connection.
	 */
	protected function createDriver($name): StoreService
	{
		$config = (array) $this->container['config']->get("miva.connections.$name.store", []);

		if ($config === []) {
			throw new InvalidArgumentException("Miva connection [$name] is not configured.");
		}

		$auth = [];

		if (! empty($config['auth'])) {
			$username = trim((string) ($config['auth']['username'] ?? ''));
			$password = trim((string) ($config['auth']['password'] ?? ''));

			if ($username !== '' && $password !== '') {
				$auth = [
					'username' => $username,
					'password' => $password,
				];
			}
		}

		return new StoreService(
			code: (string) ($config['code'] ?? ''),
			url: (string) ($config['url'] ?? ''),
			graphicsPath: (string) ($config['graphics_path'] ?? 'graphics/00000001/'),
			rootPath: (string) ($config['root_path'] ?? '/mm5/'),
			auth: $auth
		);
	}
}
