<?php

namespace Marshmallow\MarketingData\Traits;

trait HasTraitsWithCasts
{
    /**
     * Static cache for compiled casts per class to avoid expensive reflection on every call.
     *
     * @var array
     */
    protected static $compiledTraitCasts = [];

    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getCasts()
    {
        $class = static::class;

        // Only compile trait casts once per class
        if (!isset(static::$compiledTraitCasts[$class])) {
            foreach (class_uses_recursive($class) as $trait) {
                $method = 'get' . class_basename($trait) . 'Casts';

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

            // Cache the compiled casts for this class
            static::$compiledTraitCasts[$class] = true;
        }

        return parent::getCasts();
    }
}
