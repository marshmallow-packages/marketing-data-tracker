<?php

namespace Marshmallow\MarketingData\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Marshmallow\MarketingData\Facades\MarketingDataTracker;

class StoreMarketingCookiesController
{
    public function __invoke(Request $request): void
    {
        $session_key = 'mm_cookie_values';
        $cookie_marketing_values = request('marketing_cookies', []);

        $cookie_values = MarketingDataTracker::getCookieValues($cookie_marketing_values);

        $session_data = $request->session()->get($session_key, []);

        if ($cookie_values && !empty($cookie_values)) {
            $cookie_values = array_merge($cookie_values, $session_data ?? []);
            request()->session()->put($session_key, $cookie_values);
        }

        if (session()->has('mm_utm_values')) {
            $session_data = session()->get('mm_utm_values');
            $session_data['cookie_values'] = $cookie_values;
            request()->session()->put('mm_utm_values', $session_data);
        }
    }
}
