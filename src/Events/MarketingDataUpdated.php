<?php

namespace Marshmallow\MarketingData\Events;

use Illuminate\Database\Eloquent\Model;

class MarketingDataUpdated extends MarketingDataEvent
{
    public function __construct(
        Model $model,
        array $parameters = []
    ) {
        parent::__construct($model, $parameters);
    }

    /**
     * Get the event description
     */
    public function getDescription(): string
    {
        return "Marketing data was updated for {$this->getModelClass()} with ID {$this->getModelId()}";
    }

    /**
     * Get the old marketing data
     */
    public function getOldData(): array
    {
        return $this->parameters['old_data'] ?? [];
    }

    /**
     * Get the new marketing data
     */
    public function getNewData(): array
    {
        return $this->parameters['new_data'] ?? [];
    }

    /**
     * Get the changed fields
     */
    public function getChangedFields(): array
    {
        $oldData = $this->getOldData();
        $newData = $this->getNewData();
        $changed = [];

        // Find fields that exist in new data but not old, or have different values
        foreach ($newData as $key => $value) {
            if (!array_key_exists($key, $oldData) || $oldData[$key] !== $value) {
                $changed[$key] = [
                    'old' => $oldData[$key] ?? null,
                    'new' => $value,
                ];
            }
        }

        // Find fields that existed in old data but are removed
        foreach ($oldData as $key => $value) {
            if (!array_key_exists($key, $newData)) {
                $changed[$key] = [
                    'old' => $value,
                    'new' => null,
                ];
            }
        }

        return $changed;
    }

    /**
     * Check if UTM data was changed
     */
    public function hasUtmChanges(): bool
    {
        $changed = $this->getChangedFields();

        foreach (array_keys($changed) as $field) {
            if (str_starts_with($field, 'utm_')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get UTM changes specifically
     */
    public function getUtmChanges(): array
    {
        $changed = $this->getChangedFields();
        $utmChanges = [];

        foreach ($changed as $field => $change) {
            if (str_starts_with($field, 'utm_')) {
                $utmChanges[$field] = $change;
            }
        }

        return $utmChanges;
    }

    /**
     * Check if click ID data was changed
     */
    public function hasClickIdChanges(): bool
    {
        $changed = $this->getChangedFields();
        $clickIdParams = ['gclid', 'fbclid', 'msclkid', 'ttclid', 'twclid', 'li_fat_id', 'epik', 'rdt_cid', 'sscid', 'gbraid', 'wbraid'];

        foreach (array_keys($changed) as $field) {
            if (in_array($field, $clickIdParams)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get click ID changes specifically
     */
    public function getClickIdChanges(): array
    {
        $changed = $this->getChangedFields();
        $clickIdParams = ['gclid', 'fbclid', 'msclkid', 'ttclid', 'twclid', 'li_fat_id', 'epik', 'rdt_cid', 'sscid', 'gbraid', 'wbraid'];
        $clickIdChanges = [];

        foreach ($changed as $field => $change) {
            if (in_array($field, $clickIdParams)) {
                $clickIdChanges[$field] = $change;
            }
        }

        return $clickIdChanges;
    }

    /**
     * Get the number of changes
     */
    public function getChangeCount(): int
    {
        return count($this->getChangedFields());
    }

    /**
     * Convert to array with additional updated-specific data
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'description' => $this->getDescription(),
            'old_data' => $this->getOldData(),
            'new_data' => $this->getNewData(),
            'changed_fields' => $this->getChangedFields(),
            'change_count' => $this->getChangeCount(),
            'has_utm_changes' => $this->hasUtmChanges(),
            'utm_changes' => $this->getUtmChanges(),
            'has_click_id_changes' => $this->hasClickIdChanges(),
            'click_id_changes' => $this->getClickIdChanges(),
        ]);
    }
}
