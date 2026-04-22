<?php

namespace Marshmallow\MarketingData\Traits;

trait HasTraitsWithCasts
{
    /**
     * Static per-class cache of the trait-derived cast list.
     *
     * Keyed by concrete class name; the cast list for a given class never
     * changes at runtime, so there's no need to rebuild it on every
     * attribute access.
     *
     * @var array<class-string, array<string, string>>
     */
    protected static array $traitCastsCache = [];

    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getCasts()
    {
        $class = static::class;

        // Build the trait-derived cast list once per class, then cache it.
        // Before this cache, Laravel's getAttribute → getCasts hot path was
        // running class_uses_recursive + two Collections + a unique() closure
        // on EVERY attribute read. On yardy-s001 that accounted for ~172 slow
        // traces per day (MM-21431).
        if (!isset(self::$traitCastsCache[$class])) {
            $casts = [];

            foreach (class_uses_recursive($class) as $trait) {
                $method = 'get'.class_basename($trait).'Casts';

                if (method_exists($class, $method)) {
                    $casts = array_merge($casts, (array) $this->{$method}());
                }
            }

            self::$traitCastsCache[$class] = $casts;
        }

        // Merge cached trait casts into the model's own casts (model-level
        // casts can still change between instances in theory, so we don't
        // cache the final merge — only the trait lookup).
        $this->casts = array_merge($this->casts, self::$traitCastsCache[$class]);

        return parent::getCasts();
    }
}
