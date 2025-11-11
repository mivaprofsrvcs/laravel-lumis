# Lumis for Laravel

**Lumis** is a lightweight, Laravel-compatible package that provides a structured foundation for integrating with the **Miva JSON API**. It includes service providers, facades, and configuration management designed for both single-store and multi-store applications.

This package simplifies working with Miva's API by providing:

* Fluent, Laravel-style access to the Miva API via the `MivaApi` facade.
* A `Store` service for generating store URLs, handling authentication, and building store-related paths.
* Simple configuration publishing and environment variable management.
* Support for multi-store setups via a connection manager.

## Installation

Install the package using Composer:

```bash
composer require mvps/laravel-lumis
```

The service providers and facades will be automatically registered through Laravel's package auto-discovery.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=lumis-config
```

This will create a configuration file at:

```
config/miva.php
```

### Example Configuration

```php
return [
    'default' => env('MM_CONNECTION', 'default'),

    'connections' => [
        'default' => [
            'api' => [
                'key' => env('MM_API_KEY'),
                'token' => env('MM_API_TOKEN'),
                'url' => env('MM_API_URL'),
                'verify_ssl' => (bool) env('MM_API_VERIFY_SSL', false),
            ],

            'store' => [
                'code' => env('MM_STORE_CODE'),
                'url' => env('MM_STORE_URL'),
                'graphics_path' => env('MM_STORE_GRAPHICS_PATH', 'graphics/00000001/'),
                'root_path' => env('MM_STORE_ROOT_PATH', '/mm5/'),
                'auth' => [
                    'username' => env('MM_STORE_AUTH_USERNAME', ''),
                    'password' => env('MM_STORE_AUTH_PASSWORD', ''),
                ],
            ],
        ],

        // Example of a multi-store connection
        'store02' => [
            'api' => [
                'key' => env('S02_MM_API_KEY'),
                'token' => env('S02_MM_API_TOKEN'),
                'url' => env('S02_MM_API_URL'),
            ],

            'store' => [
                'code' => env('S02_MM_STORE_CODE'),
                'url' => env('S02_MM_STORE_URL'),
                'graphics_path' => env('S02_MM_STORE_GRAPHICS_PATH', 'graphics/00000001/'),
                'root_path' => env('S02_MM_STORE_ROOT_PATH', '/mm5/'),
            ],
        ],
    ],
];
```

## Facades

### `MivaApi`

The `MivaApi` facade provides a fluent interface to the underlying Miva API client.

#### Example Usage

```php
use MVPS\Lumis\Facades\MivaApi;

MivaApi::func('ProductList_Load_Query')
    ->count(25)
    ->odc(['url', 'attributes'])
    ->add();

$response = MivaApi::sendRequest('ProductList_Load_Query');
```

#### Named Connections

When using multiple store connections:

```php
MivaApi::connection('store02')
    ->func('OrderList_Load_Query')
    ->count(50)
    ->add();

$response = MivaApi::connection('store02')->sendRequest('OrderList_Load_Query');
```

### `Store`

The `Store` facade provides helpers for accessing Miva store URLs, authentication headers, and path management.

#### Example Usage

```php
use MVPS\Lumis\Facades\Store;

$baseUrl = Store::url();
// https://example.test

$jsonUrl = Store::jsonUrl();
// https://example.test/mm5/json.mvc

$graphicsUrl = Store::graphicsUrl();
// https://example.test/mm5/graphics/00000001/

$authHeader = Store::authHeader();
// ['Authorization' => 'Basic ...']
```

## Core Classes

### `MVPS\Lumis\Services\ApiClientService`

Wraps the base Miva JSON API client, providing fluent access to API-building methods and response handling.

#### Key Methods

| Method                                                                                                                                                | Description                                                      |
| ----------------------------------------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------- |
| `listLoadQuery(string $function, array $onDemandColumns = [], ?string $sort = null, ?int $count = null, ?int $offset = null, ?array $filters = null)` | Builds and queues a list-load query.                             |
| `sendRequest(?string $functionName = null)`                                                                                                           | Sends the current queued request and retrieves a response array. |
| `setClient(MivaApiClient $client)`                                                                                                                    | Overrides the API client instance.                               |
| `client()`                                                                                                                                         | Returns the underlying `pdeans\Miva\Api\Client` instance.        |

### `MVPS\Lumis\Services\StoreService`

Represents a Miva store configuration and provides helpers for constructing URLs and authorization headers.

#### Key Methods

| Method                                   | Description                                            |
| ---------------------------------------- | ------------------------------------------------------ |
| `url()`                                  | Returns the base store URL.                            |
| `jsonUrl(string $jsonFile = 'json.mvc')` | Returns the full Miva JSON endpoint URL.               |
| `graphicsUrl()`                          | Returns the graphics directory URL.                    |
| `authHeader()`                           | Returns HTTP Basic Authorization header if configured. |
| `urlWithPath(string $path)`              | Generates a full URL with the provided path.           |

## Testing

This package uses [Pest](https://pestphp.com) and [Orchestra Testbench](https://github.com/orchestral/testbench) for testing.

Run the test suite with:

```bash
composer test
```

## Contributing

Pull requests are welcome! Before submitting, please ensure:

* Tests are added or updated for new functionality.

To run all checks locally:

```bash
composer test
```
