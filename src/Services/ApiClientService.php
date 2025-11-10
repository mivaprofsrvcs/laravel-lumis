<?php

namespace MVPS\Lumis\Services;

use pdeans\Miva\Api\Client as MivaApiClient;

/**
 * @see \pdeans\Miva\Api\Client
 * @mixin \pdeans\Miva\Api\Client
 *
 * @method \pdeans\Miva\Api\Client count(int $count)
 * @method \pdeans\Miva\Api\Client filter(string $filterName, mixed $filterValue)
 * @method \pdeans\Miva\Api\Client filters(array $filters)
 * @method \pdeans\Miva\Api\Client offset(int $offset)
 * @method \pdeans\Miva\Api\Client odc(array $columns)
 * @method \pdeans\Miva\Api\Client ondemandcolumns(array $columns)
 * @method \pdeans\Miva\Api\Client params(array $parameters)
 * @method \pdeans\Miva\Api\Client passphrase(string $passphrase)
 * @method \pdeans\Miva\Api\Client search(mixed ...$args)
 * @method \pdeans\Miva\Api\Client show(string $showValue)
 * @method \pdeans\Miva\Api\Client sort(string $sortColumn)
 * @method \pdeans\Miva\Api\Client sortDesc(string $sortColumn)
 */
class ApiClientService
{
	/**
	 * The Miva API client.
	 *
	 * @var \pdeans\Miva\Api\Client
	 */
	protected MivaApiClient $client;

	/**
	 * Create a new Miva API client service.
	 */
	public function __construct(MivaApiClient $apiClient)
	{
		$this->client = $apiClient;
	}

	/**
	 * Get the Miva Api client instance.
	 */
	public function client(): MivaApiClient
	{
		return $this->client;
	}

	/**
	 * Build a list-load query and enqueue it on the client.
	 *
	 * @param  array<int,string>                 $onDemandColumns
	 * @param  ?string                           $sort
	 * @param  ?int                              $count
	 * @param  ?int                              $offset
	 * @param  array<string,mixed>|array<int,array{name:string,value:mixed}>|null  $filters
	 */
	public function listLoadQuery(
		array $onDemandColumns = [],
		?string $sort = null,
		?int $count = null,
		?int $offset = null,
		?array $filters = null
	): MivaApiClient {
		if (! empty($onDemandColumns)) {
			$this->client->odc($onDemandColumns);
		}

		if ((string) $sort !== '') {
			$this->client->sort($sort);
		}

		if ($count !== null) {
			$this->client->count($count);
		}

		if ($offset !== null) {
			$this->client->offset($offset);
		}

		if (! empty($filters)) {
			foreach ($this->normalizeFilters($filters) as $filter) {
				$this->client->filter($filter['name'], $filter['value']);
			}
		}

		return $this->client->add();
	}

	/**
	 * Accepts either:
	 *  - associative map: ['ondemandcolumns' => ['url'], 'code' => 'ABC']
	 *  - list of pairs:   [['name' => 'ondemandcolumns','value' => ['url']], ...]
	 *
	 * @param  array<string,mixed>|array<int,array{name:string,value:mixed}> $filters
	 * @return array<int,array{name:string,value:mixed}>
	 */
	private function normalizeFilters(array $filters): array
	{
		$isAssoc = array_keys($filters) !== range(0, count($filters) - 1);
		$normalizedFilters = [];

		if ($isAssoc) {
			foreach ($filters as $name => $value) {
				$normalizedFilters[] = ['name' => trim((string) $name), 'value' => $value];
			}

			return $normalizedFilters;
		}

		foreach ($filters as $filter) {
			if (is_array($filter) && array_key_exists('name', $filter) && array_key_exists('value', $filter)) {
				$normalizedFilters[] = [
					'name' => trim((string) $filter['name']),
					'value' => $filter['value'],
				];
			}
		}

		return $normalizedFilters;
	}

	/**
	 * Send the request and optionally target a specific function's response.
	 */
	public function sendRequest(?string $functionName = null): ?array
	{
		$response = $this->client->send();

		return $response?->getResponse($functionName);
	}

	/**
	 * Set the Miva Api client instance.
	 */
	public function setClient(MivaApiClient $apiClient): static
	{
		$this->client = $apiClient;

		return $this;
	}

	/**
	 * Dynamically handle calls into the client instance.
	 */
	public function __call(string $method, array $arguments): mixed
	{
		return $this->client->{$method}(...$arguments);
	}
}
