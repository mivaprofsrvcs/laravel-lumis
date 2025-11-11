<?php

namespace MVPS\Lumis\Facades;

use Illuminate\Support\Facades\Facade;
use MVPS\Lumis\MivaApiManager;

/**
 * @see \MVPS\Lumis\Services\ApiClientService
 * @mixin \MVPS\Lumis\MivaApiManager
 * @mixin \MVPS\Lumis\Services\ApiClientService
 * @mixin \pdeans\Miva\Api\Client
 *
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
 */
class MivaApi extends Facade
{
	protected static function getFacadeAccessor(): string
	{
		return MivaApiManager::class;
	}
}
