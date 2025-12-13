<?php

namespace MVPS\Lumis\Services;

use pdeans\Miva\Api\Client as MivaApiClient;
use pdeans\Miva\Api\Response as MivaApiResponse;

/**
 * @method \pdeans\Miva\Api\Client func(string $name)
 * @method \pdeans\Miva\Api\Client add(\pdeans\Miva\Api\Builders\FunctionBuilder $function = null)
 * @method \pdeans\Miva\Api\Client addHeader(string $headerName, string $headerValue)
 * @method \pdeans\Miva\Api\Client addHeaders(array<string, string> $headers)
 * @method array<int, string> getFunctionList()
 * @method array<string, string> getHeaders()
 * @method \Psr\Http\Message\RequestInterface|null getPreviousRequest()
 * @method \Psr\Http\Message\ResponseInterface|null getPreviousResponse()
 * @method array<string, mixed> getOptions()
 * @method \pdeans\Miva\Api\Request getRequest()
 * @method string getRequestBody(int $encodeOpts = 4194432, int $depth = 512)
 * @method string getUrl()
 * @method string|\pdeans\Miva\Api\Response send(bool $rawResponse = false)
 * @method \pdeans\Miva\Api\Client setOptions(array<string, mixed> $options)
 * @method \pdeans\Miva\Api\Client setUrl(string $url)
 * @method \pdeans\Miva\Api\Client setTimeout(int $seconds)
 * @method \pdeans\Miva\Api\Client setBinaryEncoding(string $encoding)
 * @method \pdeans\Miva\Api\Client setOperationsRange(int $start, ?int $end = null)
 * @method \pdeans\Miva\Api\Client clearOperationsRange()
 * @method \pdeans\Miva\Api\Client setSshAuth(string $username, string $privateKey, string $algorithm = 'sha256')
 * @method \pdeans\Miva\Api\Client count(int $count)
 * @method \pdeans\Miva\Api\Client filter(string $filterName, mixed $filterValue)
 * @method \pdeans\Miva\Api\Client filters(array<int|string, mixed> $filters)
 * @method \pdeans\Miva\Api\Client offset(int $offset)
 * @method \pdeans\Miva\Api\Client odc(array<int|string, mixed> $columns)
 * @method \pdeans\Miva\Api\Client ondemandcolumns(array<int|string, mixed> $columns)
 * @method \pdeans\Miva\Api\Client params(array<int|string, mixed> $parameters)
 * @method \pdeans\Miva\Api\Client passphrase(string $passphrase)
 * @method \pdeans\Miva\Api\Client search(mixed ...$args)
 * @method \pdeans\Miva\Api\Client show(string $showValue)
 * @method \pdeans\Miva\Api\Client sort(string $sortColumn)
 * @method \pdeans\Miva\Api\Client sortDesc(string $sortColumn)
 *
 * @see \pdeans\Miva\Api\Client
 * @mixin \pdeans\Miva\Api\Client
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
     * This method prepares a Miva list-load function by setting the provided
     * sort, count, offset, and filter options before adding it to the
     * client's function queue.
     *
     * @param  string  $function  The Miva JSON API function name.
     * @param  array<string>  $onDemandColumns  List of on-demand columns to include.
     * @param  ?string  $sort  Optional column name to sort results by.
     * @param  ?int  $count  Optional number of records to return.
     * @param  ?int  $offset  Optional number of records to skip.
     * @param  array<string,mixed>|array<int,array{name:string,value:mixed}>|null  $filters
     *         Filters to apply. Supports:
     *         - Assoc map: ['ondemandcolumns' => [...], 'search' => [SearchCond, ...]]
     *         - List of pairs: [['name' => '...', 'value' => mixed], ...]
     *         - Single-key maps: [['search' => mixed], ['ondemandcolumns' => mixed], ...]
     * @return \pdeans\Miva\Api\Client
     */
    public function listLoadQuery(
        string $function,
        array $onDemandColumns = [],
        ?string $sort = null,
        ?int $count = null,
        ?int $offset = null,
        ?array $filters = null
    ): MivaApiClient {
        $this->client->func($function);

        if (! empty($onDemandColumns)) {
            $this->client->odc($onDemandColumns);
        }

        if ($sort !== null && $sort !== '') {
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
     * Normalize $filters to [['name' => string, 'value' => mixed], ...].
     *
     * Accepts one of:
     *  A) assoc map:
     *     ['ondemandcolumns' => [...], 'search' => [SearchCond|Subwhere, ...], ...]
     *  B) list of pairs:
     *     [['name' => '...', 'value' => mixed], ...]
     *  C) list of single-key maps:
     *     [['search' => mixed], ['ondemandcolumns' => mixed], ...]
     *
     * Notes:
     * - For AND logic: pass multiple top-level 'search' entries (B/C), each
     *   with a single SearchCond in 'value'.
     * - For OR logic: pass one 'search' entry with multiple SearchCond items
     *   in 'value'.
     * - For parentheses: include a SearchCond with operator 'SUBWHERE' and
     *   field 'search_OR' or 'search_AND'.
     *
     * @param  array<string,mixed>|array<int,array<string,mixed>> $filters
     * @return array<int,array{name:string,value:mixed}>
     */
    private function normalizeFilters(array $filters): array
    {
        $normalized = [];

        $addFilter = static function (string $name, mixed $value) use (&$normalized): void {
            $name = trim($name);

            if ($name !== '') {
                $normalized[] = ['name' => $name, 'value' => $value];
            }
        };

        $isAssoc = array_keys($filters) !== range(0, count($filters) - 1);

        if ($isAssoc) {
            // Assoc map
            foreach ($filters as $name => $value) {
                $addFilter((string) $name, $value);
            }

            return $normalized;
        }

        foreach ($filters as $item) {
            if (! is_array($item) || $item === []) {
                continue;
            }

            // Explicit {name,value}
            if (array_key_exists('name', $item) && array_key_exists('value', $item)) {
                $addFilter((string) $item['name'], $item['value']);

                continue;
            }

            // Single-key map
            if (count($item) === 1) {
                $addFilter((string) array_key_first($item), current($item));
            }
        }

        return $normalized;
    }

    /**
     * Send the request and optionally target a specific function's response.
     *
     * @return array<int|string, mixed>|null
     */
    public function sendRequest(?string $functionName = null): ?array
    {
        $response = $this->client->send();

        if ($response instanceof MivaApiResponse) {
            return $response->getResponse($functionName);
        }

        return null;
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
     *
     * @param  array<int|string, mixed>  $arguments
     */
    public function __call(string $method, array $arguments): mixed
    {
        return $this->client->{$method}(...$arguments);
    }
}
