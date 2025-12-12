<?php

use MVPS\Lumis\Facades\MivaApi;
use MVPS\Lumis\Facades\Store;

it('resolves MivaApi facade and proxies builder methods', function () {
    MivaApi::func('ProductList_Load_Query')
        ->odc(['url'])
        ->count(5)
        ->add();

    $body = MivaApi::getRequestBody(JSON_THROW_ON_ERROR);
    $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

    expect($data['Function'] ?? null)->toBe('ProductList_Load_Query');
});

it('resolves Store facade and returns url', function () {
    expect(Store::url())->toBe('https://default.test');
});
