<?php

namespace Marshmallow\MarketingData\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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
        $this->setUtmValues($request, 'mm_utm_values');

        // Set Source values
        $this->setSourceValues($request, 'mm_source_values');

        // Set Cookie values
        $this->setCookieValues($request, 'mm_cookie_values');

        return $next($request);
    }

    public function setCookieValues($request, $session_key)
    {
        if (session()->has($session_key)) {
            return;
        }

        /// WIP FOR ALL Cookies

        $cookie_values = MarketingDataTracker::getCookieValues($request->cookie());

        if ($cookie_values && ! empty($cookie_values)) {
            $request->session()->put($session_key, $cookie_values);
        }

        if (session()->has('mm_utm_values')) {
            $session_data = session()->get('mm_utm_values');
            $session_data['cookie_values'] = $cookie_values;
            $request->session()->put('mm_utm_values', $session_data);
        }
    }

    public function setUtmValues($request, $session_key)
    {
        if (session()->has($session_key)) {
            $session_data = session()->get($session_key);
            $utm_source = Arr::get($session_data, 'utm_source', null);
            $gclid = Arr::get($session_data, 'gclid', null);
            $gclid_request = $request->input('gclid', null);
            if ($gclid_request && $gclid_request == $gclid) {
                return;
            } elseif ($gclid || $session_data || $utm_source) {
                return;
            }
        }

        $parameter_values_set = MarketingDataTracker::getRequestValues($request->all());

        $parameter_values = collect($parameter_values_set)->mapWithKeys(function ($parameter_value, $parameter_key) use ($request) {

            if ($parameter_key === 'landing_url') {
                $parameter_value = $request->url();
            }

            if ($parameter_key === 'landing_path') {
                $parameter_value = $request->path();
                if (! Str::startsWith($parameter_value, '/')) {
                    $parameter_value = '/' . $parameter_value;
                }
            }

            if ($parameter_key === 'landing_full_url') {
                $parameter_value = $request->fullUrl();
            }

            return [$parameter_key => $parameter_value];
        })->reject(function ($parameter_value) {
            return is_null($parameter_value);
        })->toArray();

        if ($parameter_values && ! empty($parameter_values)) {
            $request->session()->put($session_key, $parameter_values);
        }
    }

    public function setSourceValues($request, $session_key)
    {
        if (! session()->has($session_key)) {
            $intended_url = session()->get('url.intended');
            $source_url = $request->server('HTTP_REFERER');

            if (Str::of($source_url)->contains([
                'livewire',
                'oauth',
                'password',
                'reset',
                'apple.com',
            ]) && $intended_url) {
                $source_url = $intended_url;
            }

            $source_path = null;
            if ($source_url) {
                $app_url = config('app.url');
                $source_path = Str::of($source_url)
                    ->before('?')
                    ->after($app_url)
                    ->toString();
            }

            $source_parameters = [
                'source_url' => $source_url,
                'source_path' => $source_path,
            ];
        } else {
            $source_parameters = session()->get($session_key);
        }

        $source_parameters['previous_url'] = session()->get('_previous.url');
        $source_parameters['request_url'] = $request->url();
        $source_parameters['referer_url'] = $request->server('HTTP_REFERER');

        if ($source_parameters && ! empty($source_parameters)) {
            $request->session()->put($session_key, $source_parameters);
        }
    }
}
