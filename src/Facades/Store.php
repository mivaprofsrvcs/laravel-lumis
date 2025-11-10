<?php

namespace MVPS\Lumis\Facades;

use Illuminate\Support\Facades\Facade;
use MVPS\Lumis\Services\StoreService;

/**
 * @see \MVPS\Lumis\Services\StoreService
 * @mixin \MVPS\Lumis\Services\StoreService
 */
class Store extends Facade
{
	/**
	 * @inheritdoc
	 */
	protected static function getFacadeAccessor(): string
	{
		return StoreService::class;
	}
}
