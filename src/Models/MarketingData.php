<?php

namespace Marshmallow\MarketingData\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Marshmallow\MarketingData\Facades\MarketingDataTracker;

class MarketingData extends Model
{
    protected $casts = [
        'data' => AsCollection::class,
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(MarketingDataTracker::getMarketingDataTableName());
    }

    protected $guarded = [];

    public function marketingDatable()
    {
        return $this->morphTo();
    }

    protected function getDataArray(): array
    {
        if (!filled($this->data)) {
            return [];
        }

        if (is_array($this->data)) {
            return $this->data;
        }

        return json_decode($this->data, true);
    }

    public function addMarketingData($key, $value): void
    {
        $current_data = $this->getDataArray();
        $new_data = array_merge($current_data, [$key => $value]);

        if (!filled($value)) {
            $new_data = Arr::except($new_data, $key);
        }

        $this->update([
            'data' => $new_data,
        ]);
    }

    /**
     * Add multiple marketing data parameters in a single database operation.
     * This is more efficient than calling addMarketingData() multiple times.
     *
     * @param array $data Key-value pairs to add/merge
     */
    public function addMarketingDataBatch(array $data): void
    {
        if (empty($data)) {
            return;
        }

        $current_data = $this->getDataArray();
        $new_data = array_merge($current_data, $data);

        // Remove any keys with empty values
        foreach ($data as $key => $value) {
            if (!filled($value)) {
                $new_data = Arr::except($new_data, $key);
            }
        }

        $this->update([
            'data' => $new_data,
        ]);
    }

    public function getMarketingData(string $key)
    {
        $data = $this->getDataArray();
        if (!array_key_exists($key, $data)) {
            return null;
        }

        return $data[$key];
    }

    public function removeKey($key): void
    {
        $this->addMarketingData($key, null);
    }

    public function clear(): void
    {
        $this->update([
            'data' => [],
        ]);
    }
}
