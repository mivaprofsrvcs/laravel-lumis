<?php

use MVPS\Lumis\Services\ApiClientService;
use MVPS\Lumis\Services\StoreService;
use pdeans\Miva\Api\Client as MivaApiClient;

it('resolves ApiClientService singleton and raw client binding', function () {
    $apiClient = resolve(ApiClientService::class);

    expect($apiClient)->toBeInstanceOf(ApiClientService::class)
        ->and($apiClient)->toBe(resolve(ApiClientService::class));

    expect(resolve(MivaApiClient::class))->toBe($apiClient->client());
});

it('uses StoreService for store_code and builds headers including UA', function () {
    $apiClient = resolve(ApiClientService::class);
    $client = $apiClient->client();

    $options = $client->getOptions();
    $headers = $client->getHeaders();

    expect($options['store_code'])->toBe(resolve(StoreService::class)->code());
});

it('applies SSL verification flags from config', function () {
    config()->set('miva.default', 'default');
    config()->set('miva.connections.default.api.verify_ssl', true);

    $apiClient = resolve(ApiClientService::class);

    expect($apiClient->client()->getOptions()['http_client'] ?? [])->toMatchArray([
        'verify' => true,
    ]);
});
