<?php

namespace Marshmallow\MarketingData\Http\Controllers;

class StoreMarketingCookiesController
{
    public function __invoke(): void
    {
        $marketing_cookies = request('marketing_cookies', []);
        ray('Storing marketing cookies', $marketing_cookies);
    }
}
