<?php

namespace Antoniputra\Reviz\Facades;

use Antoniputra\Reviz\RevizManager;

/**
 * @method static void isEnabled()
 * @method static void enable()
 * @method static void disable()
 * @method static array pushItems(array $item)
 * @method static \Illuminate\Support\Collection getItems()
 * @method static void clearItems()
 * @method static bool store($event)
 * 
 * @see \Antoniputra\Reviz\RevizManager
 */
class Reviz extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return RevizManager::class;
    }
}
