<?php

use InvalidArgumentException;
use MVPS\Lumis\Facades\Store;
use MVPS\Lumis\MivaStoreManager;
use MVPS\Lumis\Services\StoreService;

it('resolves default store via manager', function () {
    $storeManager = resolve(MivaStoreManager::class);

    $store = $storeManager->connection();

    expect($store)->toBeInstanceOf(StoreService::class)
        ->and($store->url())->toBe('https://default.test');
});

it('resolves named store via manager', function () {
    $storeManager = resolve(MivaStoreManager::class);

    $store = $storeManager->connection('store02');

    expect($store->url())->toBe('https://store02.test');
});

it('Store facade proxies to default connection', function () {
    expect(Store::url())->toBe('https://default.test');
});

it('Store facade supports connection switching', function () {
    expect(Store::connection('store02')->url())->toBe('https://store02.test');
});

it('throws exception when store connection is not configured', function () {
    $manager = resolve(MivaStoreManager::class);

    expect(fn () => $manager->connection('unknown'))
        ->toThrow(InvalidArgumentException::class, 'Miva connection [unknown] is not configured.');
});
