<?php

namespace Marshmallow\MarketingData\Nova\Traits;

use Illuminate\Support\Arr;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Panel;

trait MarketingDataFields
{
    public function getMarketingDataFields($with_google_ids = false, $with_utm_data = true, $with_all_data = false, $with_raw_data = false)
    {

        $fields = [];

        if ($with_utm_data) {
            $fields[] = Stack::make(__('UTM Data'), [
                Line::make(__('Source'), 'marketing_source')
                    ->extraClasses('font-semibold text-sm'),
                Line::make(__('Medium'), 'utm_medium_term')
                    ->extraClasses('font-normal text-xs'),
            ]);
        }

        if ($with_google_ids) {
            $fields[] = Boolean::make(__('Has Google ID'), 'has_google_id')->readonly();
            $fields[] = KeyValue::make(__('Google IDs'), 'google_ids')
                ->readonly()
                ->hideFromIndex();
        }

        $fields[] = KeyValue::make(__('Marketing Parameters'), 'marketing_parameter_list')
            ->keyLabel(__('Tag'), 'tag')
            ->valueLabel(__('Value'), 'value')
            ->fullWidth()
            ->readonly()
            ->exceptOnForms()
            ->hideFromIndex();

        if ($with_all_data) {
            $fields[] = KeyValue::make(__('All Marketing Parameters'), 'all_marketing_parameters')
                ->keyLabel(__('Tag'), 'tag')
                ->valueLabel(__('Value'), 'value')
                ->fullWidth()
                ->readonly()
                ->exceptOnForms()
                ->hideFromIndex();
        }

        if ($with_raw_data) {
            $fields[] = KeyValue::make(__('Raw Marketing Parameters'), 'all_raw_marketing_parameters')
                ->keyLabel(__('Tag'), 'tag')
                ->valueLabel(__('Value'), 'value')
                ->fullWidth()
                ->readonly()
                ->exceptOnForms()
                ->hideFromIndex();
        }

        $panel_content = Arr::prepend($fields, Heading::make(__('Marketing Metadata')));

        return Panel::make(__('Marketing Data'), $panel_content);
    }
}
