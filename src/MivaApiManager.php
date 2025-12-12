<?php

namespace MVPS\Lumis;

use Illuminate\Support\Manager;
use InvalidArgumentException;
use MVPS\Lumis\Services\ApiClientService;
use MVPS\Lumis\Services\StoreService;
use pdeans\Miva\Api\Client as MivaApiClient;

/**
 * @property \Illuminate\Foundation\Application $container
 * @mixin \MVPS\Lumis\Services\ApiClientService
 */
class MivaApiManager extends Manager
{
    /**
     * {@inheritdoc}
     */
    public function getDefaultDriver()
    {
        return (string) $this->container['config']->get('miva.default', 'default');
    }

    /**
     * Get a connection by name.
     *
     * Alias of `driver()` method.
     */
    public function connection(?string $name = null): ApiClientService
    {
        return $this->driver($name);
    }

    /**
     * Create the client service for a named connection.
     */
    protected function createDriver($name): ApiClientService
    {
        $config = (array) $this->container['config']->get("miva.connections.$name", []);

        if ($config === []) {
            throw new InvalidArgumentException("Miva store connection [$name] is not configured.");
        }

        $api = (array) ($config['api'] ?? []);
        $store = (array) ($config['store'] ?? []);

        $storeService = new StoreService(
            code: (string) ($store['code'] ?? ''),
            url: (string) ($store['url'] ?? ''),
            graphicsPath: (string) ($store['graphics_path'] ?? 'graphics/00000001/'),
            rootPath: (string) ($store['root_path'] ?? '/mm5/'),
            auth: (array) ($store['auth'] ?? []),
        );

        $headers = [
            'Cache-Control' => 'no-cache',
        ];

        $authHeader = $storeService->authHeader();

        if (! empty($authHeader)) {
            $headers = array_merge($headers, $authHeader);
        }

        $verifySsl = (bool) ($api['verify_ssl'] ?? false);

        $clientOptions = [
            'url' => (string) ($api['url'] ?? ''),
            'store_code' => $storeService->code(),
            'access_token' => (string) ($api['token'] ?? ''),
            'private_key' => (string) ($api['key'] ?? ''),
            'http_headers' => $headers,
            'http_client' => $api['http_client'] ?? ['verify' => $verifySsl],
        ];

        if (isset($api['hmac'])) {
            $clientOptions['hmac'] = (string) $api['hmac'];
        }

        if (isset($api['timeout'])) {
            $clientOptions['timeout'] = (int) $api['timeout'];
        }

        if (isset($api['binary_encoding'])) {
            $clientOptions['binary_encoding'] = (string) $api['binary_encoding'];
        }

        if (isset($api['range'])) {
            $clientOptions['range'] = (string) $api['range'];
        }

        if (! empty($api['ssh_auth']) && is_array($api['ssh_auth'])) {
            $sshAuth = $api['ssh_auth'];

            if (! empty($sshAuth['username']) && ! empty($sshAuth['private_key'])) {
                $clientOptions['ssh_auth'] = [
                    'username' => (string) $sshAuth['username'],
                    'private_key' => (string) $sshAuth['private_key'],
                    'algorithm' => (string) ($sshAuth['algorithm'] ?? 'sha256'),
                ];
            }
        }

        $client = new MivaApiClient($clientOptions);

        return new ApiClientService($client);
    }
}
