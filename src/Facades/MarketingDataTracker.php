<?php

namespace Marshmallow\MarketingData\Facades;

use Illuminate\Support\Facades\Facade;
use Marshmallow\MarketingData\MarketingData;

/**
 * @see MarketingData
 */
class MarketingDataTracker extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MarketingData::class;
    }
}
