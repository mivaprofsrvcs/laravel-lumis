<?php

namespace MVPS\Lumis\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use MVPS\Lumis\Services\ApiClientService;
use MVPS\Lumis\Services\StoreService;
use pdeans\Miva\Api\Client as MivaApiClient;

class MivaApiServiceProvider extends ServiceProvider implements DeferrableProvider
{
	/**
	 * @inheritdoc
	 */
	public function register(): void
	{
		$this->app->singleton(ApiClientService::class, function ($app) {
			$apiConfig = $app['config']->get('miva.api');
			$store = $app->make(StoreService::class);

			$headers = [
				'Cache-Control' => 'no-cache',
				'User-Agent' => sprintf('Lumis (Laravel/%s)', $app->version() ?: 'unknown'),
			];

			$authHeader = $store->authHeader();

			if (! empty($authHeader)) {
				$headers = array_merge($headers, $authHeader);
			}

			$verifySsl = (bool) ($apiConfig['verify_ssl'] ?? false);

			$config = [
				'url' => (string) $apiConfig['url'],
				'store_code' => (string) $store->code,
				'access_token' => (string) $apiConfig['token'],
				'private_key' => (string) $apiConfig['key'],
				'http_headers' => $headers,
				'http_client' => [
					CURLOPT_SSL_VERIFYPEER => $verifySsl ? 1 : 0,
					CURLOPT_SSL_VERIFYHOST => $verifySsl ? 2 : 0,
				],
			];

			return new ApiClientService(new MivaApiClient($config));
		});

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
			ApiClientService::class,
			MivaApiClient::class,
		];
	}
}
