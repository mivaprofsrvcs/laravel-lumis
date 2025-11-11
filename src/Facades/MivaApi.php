<?php

namespace MVPS\Lumis\Facades;

use Illuminate\Support\Facades\Facade;
use MVPS\Lumis\MivaApiManager;

/**
 * @method static \MVPS\Lumis\Services\ApiClientService connection(?string $name = null)
 * @method static \pdeans\Miva\Api\Client listLoadQuery(
 *     string $function,
 *     array $onDemandColumns = [],
 *     ?string $sort = null,
 *     ?int $count = null,
 *     ?int $offset = null,
 *     ?array $filters = null
 * )
 * @method static array|null sendRequest(?string $functionName = null)
 * @method static \pdeans\Miva\Api\Client client()
 * @method static \MVPS\Lumis\Services\ApiClientService setClient(
 *     \pdeans\Miva\Api\Client $apiClient
 * )
 * @method static \pdeans\Miva\Api\Client func(string $name)
 * @method static \pdeans\Miva\Api\Client add(
 *     \pdeans\Miva\Api\Builders\FunctionBuilder $function = null
 * )
 * @method static \pdeans\Miva\Api\Client addHeader(string $headerName, string $headerValue)
 * @method static \pdeans\Miva\Api\Client addHeaders(array $headers)
 * @method static array getFunctionList()
 * @method static array getHeaders()
 * @method static \pdeans\Http\Request|null getPreviousRequest()
 * @method static \pdeans\Http\Response|null getPreviousResponse()
 * @method static array getOptions()
 * @method static \pdeans\Miva\Api\Request getRequest()
 * @method static string getRequestBody(
 *     int $encodeOpts = JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT,
 *     int $depth = 512
 * )
 * @method static string getUrl()
 * @method static string|\pdeans\Miva\Api\Response send(bool $rawResponse = false)
 * @method static \pdeans\Miva\Api\Client setOptions(array $options)
 * @method static \pdeans\Miva\Api\Client setUrl(string $url)
 * @method static \pdeans\Miva\Api\Client count(int $count)
 * @method static \pdeans\Miva\Api\Client filter(string $filterName, mixed $filterValue)
 * @method static \pdeans\Miva\Api\Client filters(array $filters)
 * @method static \pdeans\Miva\Api\Client offset(int $offset)
 * @method static \pdeans\Miva\Api\Client odc(array $columns)
 * @method static \pdeans\Miva\Api\Client ondemandcolumns(array $columns)
 * @method static \pdeans\Miva\Api\Client params(array $parameters)
 * @method static \pdeans\Miva\Api\Client passphrase(string $passphrase)
 * @method static \pdeans\Miva\Api\Client search(mixed ...$args)
 * @method static \pdeans\Miva\Api\Client show(string $showValue)
 * @method static \pdeans\Miva\Api\Client sort(string $sortColumn)
 * @method static \pdeans\Miva\Api\Client sortDesc(string $sortColumn)
 *
 * @see \MVPS\Lumis\Services\ApiClientService
 * @mixin \MVPS\Lumis\MivaApiManager
 * @mixin \MVPS\Lumis\Services\ApiClientService
 * @mixin \pdeans\Miva\Api\Client
 */
class MivaApi extends Facade
{
	protected static function getFacadeAccessor(): string
	{
		return MivaApiManager::class;
	}
}
