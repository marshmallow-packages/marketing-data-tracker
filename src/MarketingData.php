<?php

namespace Marshmallow\MarketingData;

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
}
