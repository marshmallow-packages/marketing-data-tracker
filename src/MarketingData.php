<?php

namespace Marshmallow\MarketingData;

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

        $ignore_expression = '/^(?:' . implode('|', $ignored_list) . ').*/';

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

    public static function getCookieValues(array $cookie_data): array
    {
        $cookie_keys = self::getMarketingDataCookies();
        $cookie_value_set = self::getValuesFromSet($cookie_keys, $cookie_data);

        return $cookie_value_set;
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

                if (!$keep_empty_keys) {
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

        if (!$keep_empty_keys) {
            $marketing_values = $marketing_values->reject(function ($marketing_value, $marketing_key) {
                return is_null($marketing_value);
            });
        }

        return $marketing_values->toArray();
    }
}
