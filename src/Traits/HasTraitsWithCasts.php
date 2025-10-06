<?php

namespace Marshmallow\MarketingData\Traits;

trait HasTraitsWithCasts
{
    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getCasts()
    {
        $class = static::class;

        foreach (class_uses_recursive($class) as $trait) {
            $method = 'get'.class_basename($trait).'Casts';

            if (method_exists($class, $method)) {
                $casts = $this->{$method}();
                $casts = collect($casts);
                $this->casts = collect($this->casts)
                    ->merge($casts)
                    ->unique(function ($item, $key) {
                        return $key;
                    })->toArray();
            }
        }

        return parent::getCasts();
    }
}
