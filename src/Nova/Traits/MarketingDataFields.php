<?php

namespace Marshmallow\MarketingData\Nova\Traits;

use Illuminate\Support\Arr;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Stack;

trait MarketingDataFields
{
    public function getMarketingDataFields()
    {
        $fields = [];

        $fields[] = Stack::make(__('UTM Data'), [
            Line::make(__('Source'), 'marketing_source')
                ->extraClasses('font-semibold text-sm'),
            Line::make(__('Medium'), 'utm_medium_term')
                ->extraClasses('font-normal text-xs'),
        ]);

        $fields[] = KeyValue::make(__('Marketing Parameters'), 'marketing_parameter_list')
            ->keyLabel(__('Tag'), 'tag')
            ->valueLabel(__('Value'), 'value')
            ->fullWidth()
            ->readonly()
            ->exceptOnForms()
            ->hideFromIndex();

        return Arr::prepend($fields, Heading::make(__('Marketing Metadata')));
    }
}
