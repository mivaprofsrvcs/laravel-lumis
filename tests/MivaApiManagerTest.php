<?php

use GuzzleHttp\Client;
use MVPS\Lumis\Facades\MivaApi;
use MVPS\Lumis\MivaApiManager;
use MVPS\Lumis\Services\ApiClientService;

it('resolves default connection as ApiClientService', function () {
    $apiManager = resolve(MivaApiManager::class);
    $apiClient = $apiManager->connection();

    expect($apiClient)->toBeInstanceOf(ApiClientService::class);
});

it('resolves named connection and applies its config', function () {
    $apiManager = resolve(MivaApiManager::class);
    $apiClient = $apiManager->connection('store02');

    expect($apiClient->getOptions()['store_code'] ?? null)->toBe('s02');
});

it('facade supports connection switching', function () {
    MivaApi::connection()
        ->func('ProductList_Load_Query')
        ->add();

    $apiClientDefault = MivaApi::connection();

    $req1 = decodeRequest($apiClientDefault);

    expect($req1['Store_Code'] ?? null)->toBe('s01');
    expect($req1['Function'] ?? null)->toBe('ProductList_Load_Query');

    MivaApi::connection('store02')
        ->func('OrderList_Load_Query')
        ->add();

    $apiClientStore02 = MivaApi::connection('store02');

    $req2 = decodeRequest($apiClientStore02);

    expect($req2['Store_Code'] ?? null)->toBe('s02');
    expect($req2['Function'] ?? null)->toBe('OrderList_Load_Query');
});

it('passes optional api client options through to the Miva client', function () {
    $apiOptions = array_merge(config('miva.connections.default.api'), [
        'hmac' => 'sha1',
        'timeout' => 10,
        'binary_encoding' => 'base64',
        'range' => '1-50',
        'ssh_auth' => [
            'username' => 'ssh-user',
            'private_key' => 'ssh-private-key',
            'algorithm' => 'sha512',
        ],
        'http_client' => [
            'connect_timeout' => 1.5,
        ],
    ]);

    config()->set('miva.connections.default.api', $apiOptions);

    $options = resolve(MivaApiManager::class)->connection()->client()->getOptions();

    expect($options)->toMatchArray([
        'hmac' => 'sha1',
        'timeout' => 10,
        'binary_encoding' => 'base64',
        'range' => '1-50',
        'ssh_auth' => [
            'username' => 'ssh-user',
            'private_key' => 'ssh-private-key',
            'algorithm' => 'sha512',
        ],
    ]);

    expect($options['http_client'] ?? [])->toMatchArray([
        'connect_timeout' => 1.5,
    ]);
});

it('accepts a Guzzle client instance for http_client', function () {
    $httpClient = new Client(['verify' => false]);

    config()->set('miva.connections.default.api.http_client', $httpClient);

    $options = resolve(MivaApiManager::class)->connection()->client()->getOptions();

    expect($options['http_client'] ?? null)->toBe($httpClient);
});
