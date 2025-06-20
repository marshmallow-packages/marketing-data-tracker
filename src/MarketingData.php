<?php

namespace Marshmallow\MarketingData;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Marshmallow\MarketingData\Casts\MarketingDataCast;
use Marshmallow\MarketingData\Models\MarketingData as MarketingDataModel;

class MarketingData
{
    public static function getDatabaseTableConnection(string $table_name)
    {
        $connection = config('marketing-data-tracker.marketing_data_db_connection');
        if ($connection) {
            return "{$connection}.{$table_name}";
        }

        return $table_name;
    }

    public static function getMarketingDataTableName()
    {
        return config('marketing-data-tracker.marketing_data_table_name', 'mm_marketing_data');
    }

    public static function getMarketingDataClassName(): string
    {
        return config('marketing-data-tracker.marketing_data_model', MarketingDataModel::class);
    }

    public static function getMarketingDataCastClassName(): string
    {
        return config('marketing-data-tracker.marketing_data_cast', MarketingDataCast::class);
    }

    public static function getMarketingDataParameters(): array
    {
        $parameters = config('marketing-data-tracker.store_marketing_parameters', []);

        if (empty($parameters)) {
            return [];
        }

        return $parameters;
    }

    public static function getMarketingDataCookies(): array
    {
        $parameters = config('marketing-data-tracker.store_marketing_cookies', []);

        if (empty($parameters)) {
            return [];
        }

        return $parameters;
    }

    public static function shouldIgnoreRequest($request)
    {
        if (app()->runningInConsole()) {
            return true;
        }

        if (self::isNova($request)) {
            return true;
        }

        if (self::shouldIgnorePath($request)) {
            return true;
        }

        return false;
    }

    public static function shouldIgnorePath($request)
    {
        $ignored_list = config('marketing-data-tracker.ignore_paths', []);

        $ignore_expression = '/^(?:'.implode('|', $ignored_list).').*/';

        return preg_match($ignore_expression, $request->path());
    }

    public static function isNova($request)
    {
        return isset($request->segments()[0]) && in_array($request->segments()[0], [
            'nova-api',
            'nova-vendor',
            ltrim(config('nova.path'), '/'),
        ]);
    }

    public static function getRequestValues(array $request_data): array
    {
        $parameter_keys = self::getMarketingDataParameters();
        $parameter_values_set = self::getValuesFromSet($parameter_keys, $request_data, true);

        return $parameter_values_set;
    }

    public static function setCookieValues($request, $session_key)
    {
        if (session()->has($session_key)) {
            $session_data = session()->get($session_key);
        }

        $cookie_data = $request->cookie();

        $cookie_values = self::getCookieValues($cookie_data);

        if ($cookie_values && ! empty($cookie_values)) {
            $cookie_values = array_merge($session_data, $cookie_values ?? []);
            $request->session()->put($session_key, $cookie_values);
        }

        if (session()->has('mm_utm_values')) {
            $session_data = session()->get('mm_utm_values');
            $session_data['cookie_values'] = $cookie_values;
            $request->session()->put('mm_utm_values', $session_data);
        }
    }

    public static function getCookieValues($cookie_data): array
    {
        $cookie_keys = self::getMarketingDataCookies();
        $cookie_value_set = self::getValuesFromSet($cookie_keys, $cookie_data);

        return $cookie_value_set;
    }

    public static function setUtmValues($request, $session_key)
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

        $request_data = $request->all();
        $parameter_values_set = self::getRequestValues($request_data);

        $parameter_values = collect($parameter_values_set)->mapWithKeys(function ($parameter_value, $parameter_key) use ($request) {

            if ($parameter_key === 'landing_url') {
                $parameter_value = $request->url();
            }

            if ($parameter_key === 'landing_path') {
                $parameter_value = $request->path();
                if (! Str::startsWith($parameter_value, '/')) {
                    $parameter_value = '/'.$parameter_value;
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

    public static function setSourceValues($request, $session_key)
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

    public static function getValuesFromSet(array $marketing_keys, array $data_set, bool $keep_empty_keys = false): array
    {
        $marketing_keys = collect($marketing_keys)->mapWithKeys(function ($marketing_value, $marketing_key) {
            return [$marketing_value => null];
        });

        $data_set = collect($data_set)->mapWithKeys(function ($value, $key) {
            return [$key => $value];
        });

        $marketing_values = $marketing_keys->mapWithKeys(function ($marketing_value, $marketing_key) use ($data_set, $keep_empty_keys) {

            // Handle keys that ends with '*'
            if (Str::endsWith($marketing_key, '*')) {
                $all_input_keys = $data_set->keys();
                $marketing_group_key = Str::of($marketing_key)->before('*')->beforeLast('_');
                if ($marketing_group_key->isEmpty()) {
                    $marketing_group_key = Str::of($marketing_key)->before('*');
                }
                if (Str::of($marketing_group_key)->startsWith('_')) {
                    $marketing_group_key = Str::of($marketing_group_key)->after('_');
                }

                $marketing_key = Str::before($marketing_key, '*');

                $matching_keys = collect($all_input_keys)->filter(function ($key) use ($marketing_key) {
                    return Str::startsWith($key, $marketing_key);
                })->mapWithKeys(function ($matching_key) use ($data_set) {
                    $marketing_value = null;
                    if ($data_set->has($matching_key)) {
                        $marketing_value = $data_set->get($matching_key);
                    }

                    return [$matching_key => $marketing_value];
                });

                if (! $keep_empty_keys) {
                    $matching_keys = $matching_keys->reject(function ($marketing_value, $marketing_key) {
                        return is_null($marketing_value);
                    });
                }

                $matching_keys = $matching_keys->toArray();

                if (empty($matching_keys)) {
                    return [];
                }

                return [$marketing_group_key->toString() => $matching_keys];
            }

            if ($data_set->has($marketing_key)) {
                $marketing_value = $data_set->get($marketing_key);
            }

            return [$marketing_key => $marketing_value];
        });

        if (! $keep_empty_keys) {
            $marketing_values = $marketing_values->reject(function ($marketing_value, $marketing_key) {
                return is_null($marketing_value);
            });
        }

        return $marketing_values->toArray();
    }
}
