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
        // session()->forget('mm_utm_values');
        // session()->forget('mm_source_values');

        // Set UTM values
        $this->setUtmValues($request, 'mm_utm_values');

        // Set Source values
        $this->setSourceValues($request, 'mm_source_values');

        return $next($request);
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

        $parameters = MarketingDataTracker::getMarketingDataParameters();
        $utm_parameters = collect($parameters)->mapWithKeys(function ($parameter_value, $parameter_key) {
            return [$parameter_value => null];
        });

        $parameter_values = $utm_parameters->mapWithKeys(function ($paramater_value, $parameter_key) use ($request) {

            // Handle parameters that ends with '_*'
            if (Str::endsWith($parameter_key, '_*')) {
                $all_input_keys = $request->keys();
                $parameter_group_key = Str::before($parameter_key, '_*');
                $parameter_key = Str::before($parameter_key, '*');

                $matching_keys = collect($all_input_keys)->filter(function ($key) use ($parameter_key) {
                    return Str::startsWith($key, $parameter_key);
                })->mapWithKeys(function ($matching_key) use ($request) {
                    $paramater_value = null;
                    if ($request->has($matching_key)) {
                        $paramater_value = $request->input($matching_key);
                    }

                    return [$matching_key => $paramater_value];
                })->toArray();

                return [$parameter_group_key => $matching_keys];
            }

            if ($request->has($parameter_key)) {
                $paramater_value = $request->input($parameter_key);
            }

            if ($parameter_key === 'landing_url') {
                $paramater_value = $request->url();
            }

            if ($parameter_key === 'landing_path') {
                $paramater_value = $request->path();
                if (! Str::startsWith($paramater_value, '/')) {
                    $paramater_value = '/' . $paramater_value;
                }
            }

            if ($parameter_key === 'landing_full_url') {
                $paramater_value = $request->fullUrl();
            }

            return [$parameter_key => $paramater_value];
        })->reject(function ($session_value) {
            return is_null($session_value);
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
