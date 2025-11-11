<?php

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
