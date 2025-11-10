<?php

require_once __DIR__ . '/Support/TestHelpers.php';

use MVPS\Lumis\Services\ApiClientService;

it('builds OR search via assoc map and sets function name', function () {
	$apiClient = resolve(ApiClientService::class);

	$apiClient->listLoadQuery(
		function: 'ProductList_Load_Query',
		onDemandColumns: ['url', 'attributes'],
		sort: 'code',
		count: 25,
		offset: 10,
		filters: [
			'search' => [
				[
					'field' => 'code',
					'operator' => 'EQ',
					'value' => 'ABC',
				],
				[
					'field' => 'code',
					'operator' => 'EQ',
					'value' => 'DEF',
				],
			],
		]
	);

	[$name, $payload] = decodeFirstFunction($apiClient);

	expect($name)->toBe('ProductList_Load_Query');

	expect($payload)->toHaveKey('Count', 25)
		->and($payload)->toHaveKey('Offset', 10)
		->and($payload)->toHaveKey('Filter');

	$filters = $payload['Filter'];
	$search = findFilter($filters, 'search');
	$odc = findFilter($filters, 'ondemandcolumns');

	expect($search)->not->toBeNull()
		->and($search['value'])->toBeArray()->toHaveCount(2);

	expect($odc)->not->toBeNull()
		->and($odc['value'])->toContain('url', 'attributes');
});

it('builds AND search via list-of-pairs with two top-level search entries', function () {
	$apiClient = resolve(ApiClientService::class);

	$apiClient->listLoadQuery(
		function: 'CustomerList_Load_Query',
		filters: [
			[
				'name' => 'search',
				'value' => [
					[
						'field' => 'ship_fname',
						'operator' => 'EQ',
						'value' => 'Jonathan',
					],
				],
			],
			[
				'name' => 'search',
				'value' => [
					[
						'field' => 'ship_lname',
						'operator' => 'EQ',
						'value' => 'Order',
					],
				]
			],
		]
	);

	[, $payload] = decodeFirstFunction($apiClient);
	$filters = $payload['Filter'];

	$searchFilters = array_values(
		array_filter($filters, fn ($filter) => ($filter['name'] ?? null) === 'search')
	);

	expect($searchFilters)->toHaveCount(2)
		->and($searchFilters[0]['value'])->toHaveCount(1)
		->and($searchFilters[1]['value'])->toHaveCount(1);
});

it('builds filters via single-key maps list', function () {
	$apiClient = resolve(ApiClientService::class);

	$apiClient->listLoadQuery(
		function: 'OrderList_Load_Query',
		filters: [
			[
				'ondemandcolumns' => ['items'],
			],
			[
				'search' => [
					[
						'field' => 'id',
						'operator' => 'EQ',
						'value' => 123,
					],
				],
			],
		]
	);

	[, $payload] = decodeFirstFunction($apiClient);
	$filters = $payload['Filter'];

	$odc = findFilter($filters, 'ondemandcolumns');
	$search = findFilter($filters, 'search');

	expect($odc)->not->toBeNull()
		->and($odc['value'])->toBe(['items']);

	expect($search)->not->toBeNull()
		->and($search['value'][0])->toMatchArray([
			'field' => 'id',
			'operator' => 'EQ',
			'value' => 123,
		]);
});

it('builds SUBWHERE group (OR) inside a single search value array', function () {
	$apiClient = resolve(ApiClientService::class);

	$apiClient->listLoadQuery(
		function: 'CustomerList_Load_Query',
		filters: [
			[
				'name' => 'search',
				'value' => [
					[
						'field' => 'ship_lname',
						'operator' => 'EQ',
						'value' => 'Jones',
					],
					[
						'field' => 'search_OR',
						'operator' => 'SUBWHERE',
						'value' => [
							[
								'field' => 'ship_fname',
								'operator' => 'EQ',
								'value' => 'John',
							],
							[
								'field' => 'ship_lname',
								'operator' => 'EQ',
								'value' => 'Smith',
							],
						],
					],
				],
			],
		]
	);

	[, $payload] = decodeFirstFunction($apiClient);
	$filters = $payload['Filter'];

	$search = findFilter($filters, 'search');

	expect($search)->not->toBeNull();

	$sub = collect($search['value'])->first(fn ($f) => ($f['operator'] ?? null) === 'SUBWHERE');

	expect($sub)->not->toBeNull()
		->and($sub['field'])->toBe('search_OR')
		->and($sub['value'])->toHaveCount(2);
});

it('applies ondemandcolumns only when provided explicitly (not both)', function () {
	$apiClient = resolve(ApiClientService::class);

	$apiClient->listLoadQuery(
		function: 'ProductList_Load_Query',
		onDemandColumns: ['url']
	);

	[, $payload1] = decodeFirstFunction($apiClient);
	$odc1 = findFilter($payload1['Filter'], 'ondemandcolumns');

	expect($odc1)->not->toBeNull()
		->and($odc1['value'])->toBe(['url']);

	$apiClient->listLoadQuery(
		function: 'ProductList_Load_Query',
		filters: [['name' => 'ondemandcolumns', 'value' => ['attributes']]]
	);

	[, $payload2] = decodeFirstFunction($apiClient);

	$odc2 = findFilter($payload2['Filter'], 'ondemandcolumns');

	expect($odc2)->not->toBeNull()
		->and($odc2['value'])->toBe(['attributes']);

	expect($odc1['value'])->not->toEqual($odc2['value']);
});
