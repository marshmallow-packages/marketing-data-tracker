<?php

namespace Marshmallow\MarketingData\Middleware;

use Closure;
use Illuminate\Http\Request;
use Marshmallow\MarketingData\Facades\MarketingDataTracker;

class ParseMarketingParameters
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (MarketingDataTracker::shouldIgnoreRequest($request)) {
            return $next($request);
        }

        // Flush for Debug
        if ($request->has('mm_flush')) {
            session()->forget('mm_utm_values');
            session()->forget('mm_source_values');
            session()->forget('mm_cookie_values');
        }

        // Set UTM values
        MarketingDataTracker::setUtmValues($request, 'mm_utm_values');

        // Set Source values
        MarketingDataTracker::setSourceValues($request, 'mm_source_values');

        // Set Cookie values
        MarketingDataTracker::setCookieValues($request, 'mm_cookie_values');

        return $next($request);
    }
}
