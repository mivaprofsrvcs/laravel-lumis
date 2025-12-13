<?php

use MVPS\Lumis\Facades\MivaApi;
use MVPS\Lumis\Facades\Store;
use MVPS\Lumis\Services\ApiClientService;
use pdeans\Miva\Api\Client as MivaApiClient;
use pdeans\Miva\Api\Response as MivaApiResponse;

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

it('allows swapping the client and still supports sendRequest', function () {
    $service = resolve(ApiClientService::class);
    $originalClient = $service->client();

    $fakeClient = new class () extends MivaApiClient {
        public function __construct()
        {
            // Do not call parent constructor.
        }

        public function send(bool $rawResponse = false): MivaApiResponse
        {
            return new class (['ok' => true]) extends MivaApiResponse {
                /**
                 * @param  array<string, mixed>  $payload
                 */
                public function __construct(private array $payload)
                {
                    // Skip parent initialization.
                }

                public function getResponse(?string $functionName = null): array
                {
                    return $this->payload;
                }
            };
        }
    };

    MivaApi::setClient($fakeClient);

    expect(MivaApi::sendRequest('anything'))->toMatchArray(['ok' => true]);

    $service->setClient($originalClient);
});
