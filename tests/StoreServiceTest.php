<?php

use MVPS\Lumis\Services\StoreService;

it('resolves StoreService and normalizes paths', function () {
	$store = resolve(StoreService::class);

	expect($store->code())->toBe('s01');
	expect($store->url())->toBe('https://default.test');
	expect($store->rootPath())->toBe('/mm5/');
	expect($store->graphicsPath())->toBe('graphics/00000001/');
});

it('builds graphics, json, and root URLs', function () {
	$store = resolve(StoreService::class);

	expect($store->rootUrl())->toBe('https://default.test/mm5/');
	expect($store->jsonUrl())->toBe('https://default.test/mm5/json.mvc');
	expect($store->graphicsUrl())->toBe('https://default.test/mm5/graphics/00000001/');
});

it('returns empty auth header when not configured', function () {
	$store = resolve(StoreService::class);

	expect($store->authHeader())->toBe([]);
});

it('includes basic auth header when configured', function () {
	config()->set('miva.default', 'default');
	config()->set('miva.connections.default.store.auth.username', 'u');
	config()->set('miva.connections.default.store.auth.password', 'p');

	$store = resolve(StoreService::class);

	expect($store->authHeader())->toHaveKey('Authorization')
		->and($store->authHeader()['Authorization'])->toStartWith('Basic ');
});
