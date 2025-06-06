<?php

namespace Marshmallow\MarketingData\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Marshmallow\MarketingData\MarketingData
 */
class MarketingDataTracker extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Marshmallow\MarketingData\MarketingData::class;
    }
}
