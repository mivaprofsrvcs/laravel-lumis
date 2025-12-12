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

    $req = decodeRequest($apiClient);
    $search = findFilter($req, 'search');
    $odc = findFilter($req, 'ondemandcolumns');

    expect($req['Function'] ?? null)->toBe('ProductList_Load_Query');

    expect($req)->toHaveKey('Count', 25)
        ->and($req)->toHaveKey('Offset', 10)
        ->and($req)->toHaveKey('Filter');

    expect($search)->not->toBeNull()
        ->and($search['value'])->toHaveCount(2);

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

    $req = decodeRequest($apiClient);
    $filters = $req['Filter'] ?? [];

    $searchFilters = array_values(
        array_filter($filters, fn ($filter) => ($filter['name'] ?? null) === 'search')
    );

    expect($req['Function'] ?? null)->toBe('CustomerList_Load_Query');

    expect($req['Function'] ?? null)->toBe('CustomerList_Load_Query');

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

    $req = decodeRequest($apiClient);
    $odc = findFilter($req, 'ondemandcolumns');
    $search = findFilter($req, 'search');

    expect($req['Function'] ?? null)->toBe('OrderList_Load_Query');

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

    $req = decodeRequest($apiClient);
    $search = findFilter($req, 'search');
    $sub = collect($search['value'])->first(fn ($filter) => ($filter['operator'] ?? null) === 'SUBWHERE');

    expect($req['Function'] ?? null)->toBe('CustomerList_Load_Query');

    expect($search)->not->toBeNull();

    expect($sub)->not->toBeNull()
        ->and($sub['field'])->toBe('search_OR')
        ->and($sub['value'])->toHaveCount(2);
});

it('applies ondemandcolumns via onDemandColumns argument', function () {
    $apiClient = resolve(ApiClientService::class);

    $apiClient->listLoadQuery(
        function: 'ProductList_Load_Query',
        onDemandColumns: ['url']
    );

    $req = decodeRequest($apiClient);
    $odc = findFilter($req, 'ondemandcolumns');

    expect($req['Function'] ?? null)->toBe('ProductList_Load_Query');

    expect($odc)->not->toBeNull()
        ->and($odc['value'])->toBe(['url']);
});

it('applies ondemandcolumns via filters parameter (and not the argument)', function () {
    $apiClient = resolve(ApiClientService::class);

    $apiClient->listLoadQuery(
        function: 'ProductList_Load_Query',
        filters: [
            [
                'name' => 'ondemandcolumns',
                'value' => ['attributes'],
            ],
        ]
    );

    $req = decodeRequest($apiClient);
    $odc = findFilter($req, 'ondemandcolumns');

    expect($req['Function'] ?? null)->toBe('ProductList_Load_Query');

    expect($odc)->not->toBeNull()
        ->and($odc['value'])->toBe(['attributes']);
});
