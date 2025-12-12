<?php

namespace MVPS\Lumis\Facades;

use Illuminate\Support\Facades\Facade;
use MVPS\Lumis\MivaStoreManager;

/**
 * @method static \MVPS\Lumis\Services\StoreService connection(?string $name = null)
 * @method static array auth()
 * @method static array authHeader()
 * @method static string authHeaderValue()
 * @method static string code()
 * @method static string graphicsPath()
 * @method static string graphicsUrl()
 * @method static string jsonUrl(string $jsonFile = 'json.mvc')
 * @method static string rootPath()
 * @method static string rootUrl()
 * @method static string url()
 * @method static string urlWithPath(string $path)
 *
 * @see \MVPS\Lumis\Services\StoreService
 * @mixin \MVPS\Lumis\MivaStoreManager
 * @mixin \MVPS\Lumis\Services\StoreService
 */
class Store extends Facade
{
    /**
     * @inheritdoc
     */
    protected static function getFacadeAccessor(): string
    {
        return MivaStoreManager::class;
    }
}
