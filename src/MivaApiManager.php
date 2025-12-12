<?php

namespace MVPS\Lumis;

use Illuminate\Support\Manager;
use InvalidArgumentException;
use MVPS\Lumis\Services\ApiClientService;
use MVPS\Lumis\Services\StoreService;
use pdeans\Miva\Api\Client as MivaApiClient;

/**
 * @property \Illuminate\Foundation\Application $container
 * @mixin \MVPS\Lumis\Services\ApiClientService
 */
class MivaApiManager extends Manager
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
	public function connection(?string $name = null): ApiClientService
	{
		return $this->driver($name);
	}

	/**
	 * Create the client service for a named connection.
	 */
	protected function createDriver($name): ApiClientService
	{
		$config = (array) $this->container['config']->get("miva.connections.$name", []);

		if ($config === []) {
			throw new InvalidArgumentException("Miva store connection [$name] is not configured.");
		}

		$api = (array) ($config['api'] ?? []);
		$store = (array) ($config['store'] ?? []);

		$storeService = new StoreService(
			code: (string) ($store['code'] ?? ''),
			url: (string) ($store['url'] ?? ''),
			graphicsPath: (string) ($store['graphics_path'] ?? 'graphics/00000001/'),
			rootPath: (string) ($store['root_path'] ?? '/mm5/'),
			auth: (array) ($store['auth'] ?? []),
		);

		$headers = [
			'Cache-Control' => 'no-cache',
			'User-Agent' => sprintf('Lumis (Laravel/%s)', $this->container->version() ?: 'unknown'),
		];

		$authHeader = $storeService->authHeader();

		if (! empty($authHeader)) {
			$headers = array_merge($headers, $authHeader);
		}

		$verifySsl = (bool) ($api['verify_ssl'] ?? false);

		$client = new MivaApiClient([
			'url' => (string) ($api['url'] ?? ''),
			'store_code' => $storeService->code(),
			'access_token' => (string) ($api['token'] ?? ''),
			'private_key' => (string) ($api['key'] ?? ''),
			'http_headers' => $headers,
			'http_client' => ['verify' => $verifySsl],
		]);

		return new ApiClientService($client);
	}
}
