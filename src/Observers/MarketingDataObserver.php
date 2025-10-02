<?php

namespace Marshmallow\MarketingData\Observers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Marshmallow\MarketingData\Events\ClickIdDetected;
use Marshmallow\MarketingData\Events\MarketingDataCreated;
use Marshmallow\MarketingData\Events\MarketingDataUpdated;
use Marshmallow\MarketingData\Services\PlatformManager;

class MarketingDataObserver
{
    protected array $config;
    protected PlatformManager $platformManager;

    public function __construct(PlatformManager $platformManager)
    {
        $this->config = config('marketing-data-tracker.observers', []);
        $this->platformManager = $platformManager;
    }

    /**
     * Handle the model "created" event.
     */
    public function created(Model $model): void
    {
        if (!$this->isObserverEnabled()) {
            return;
        }

        $oldData = $this->getModelMarketingData($model);

        // Auto-set UTM data if enabled
        if ($this->config['auto_set_utm'] ?? true) {
            $this->autoSetUtmData($model);
        }

        // Auto-detect click IDs if enabled
        if ($this->config['auto_detect_click_ids'] ?? true) {
            $this->detectAndSetClickIds($model);
        }

        $newData = $this->getModelMarketingData($model);

        // Fire event if events are enabled
        if ($this->areEventsEnabled()) {
            event(new MarketingDataCreated($model, [
                'created_data' => $newData,
                'auto_set_utm' => $this->config['auto_set_utm'] ?? false,
                'auto_detect_click_ids' => $this->config['auto_detect_click_ids'] ?? false,
            ]));
        }
    }

    /**
     * Handle the model "updated" event.
     */
    public function updated(Model $model): void
    {
        if (!$this->isObserverEnabled()) {
            return;
        }

        // Get the original marketing data before update
        $oldData = $this->getOriginalMarketingData($model);
        $newData = $this->getModelMarketingData($model);

        // Check if marketing data actually changed
        if ($this->hasMarketingDataChanged($oldData, $newData)) {
            // Fire event if events are enabled
            if ($this->areEventsEnabled()) {
                event(new MarketingDataUpdated($model, [
                    'old_data' => $oldData,
                    'new_data' => $newData,
                ]));
            }
        }
    }

    /**
     * Handle the model "saving" event.
     */
    public function saving(Model $model): void
    {
        if (!$this->isObserverEnabled()) {
            return;
        }

        // Store original marketing data for comparison in updated event
        $this->storeOriginalMarketingData($model);
    }

    /**
     * Auto-set UTM data from session
     */
    protected function autoSetUtmData(Model $model): void
    {
        if (!method_exists($model, 'setUtmSourceData')) {
            return;
        }

        $forgetAfterSave = $this->config['forget_after_save'] ?? true;

        try {
            $model->setUtmSourceData($forgetAfterSave);
        } catch (Exception $e) {
            // Log error but don't break the flow
            logger()->warning('Failed to auto-set UTM data', [
                'model' => $model::class,
                'model_id' => $model->getKey(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Detect and set click IDs
     */
    protected function detectAndSetClickIds(Model $model): void
    {
        if (!method_exists($model, 'getAllRawMarketingListAttribute')) {
            return;
        }

        $marketingData = $model->getAllRawMarketingListAttribute();
        $clickIdParams = $this->platformManager->getAllClickIdParameters();

        foreach ($marketingData as $param => $value) {
            if (in_array($param, $clickIdParams) && !empty($value)) {
                $platform = $this->detectPlatformFromClickId($param);
                $this->handleClickIdDetection($model, $value, $param, $platform);
            }
        }
    }

    /**
     * Handle click ID detection
     */
    protected function handleClickIdDetection(Model $model, string $clickId, string $clickIdType, ?string $platform): void
    {
        // Store click ID in session for custom handling
        $sessionKey = "marketing_click_id_{$model->getKey()}_{$clickIdType}";
        session()->put($sessionKey, [
            'click_id' => $clickId,
            'type' => $clickIdType,
            'platform' => $platform,
            'detected_at' => now()->toISOString(),
            'model_class' => $model::class,
            'model_id' => $model->getKey(),
        ]);

        // Fire event if events are enabled
        if ($this->areEventsEnabled()) {
            event(new ClickIdDetected($model, $clickId, $clickIdType, $platform, [
                'source' => 'parameter', // or 'cookie' depending on source
                'session_key' => $sessionKey,
            ]));
        }
    }

    /**
     * Detect platform from click ID parameter
     */
    protected function detectPlatformFromClickId(string $clickIdParam): ?string
    {
        $platformMappings = [
            'gclid' => 'google_ads',
            'gbraid' => 'google_ads',
            'wbraid' => 'google_ads',
            'fbclid' => 'meta',
            'msclkid' => 'microsoft',
            'li_fat_id' => 'linkedin',
            'twclid' => 'twitter',
            'epik' => 'pinterest',
            'ttclid' => 'tiktok',
            'rdt_cid' => 'reddit',
            'sscid' => 'snapchat',
        ];

        return $platformMappings[$clickIdParam] ?? null;
    }

    /**
     * Get marketing data from model
     */
    protected function getModelMarketingData(Model $model): array
    {
        if (method_exists($model, 'getAllRawMarketingListAttribute')) {
            return $model->getAllRawMarketingListAttribute();
        }

        return [];
    }

    /**
     * Store original marketing data before update
     */
    protected function storeOriginalMarketingData(Model $model): void
    {
        if (!$model->exists) {
            return; // New model, no original data
        }

        $original = $this->getModelMarketingData($model->getOriginal() ? $model->newInstance($model->getOriginal()) : $model);
        $sessionKey = "marketing_original_data_{$model->getKey()}";
        session()->put($sessionKey, $original);
    }

    /**
     * Get original marketing data from session
     */
    protected function getOriginalMarketingData(Model $model): array
    {
        $sessionKey = "marketing_original_data_{$model->getKey()}";
        $original = session()->pull($sessionKey, []);

        return $original;
    }

    /**
     * Check if marketing data has changed
     */
    protected function hasMarketingDataChanged(array $oldData, array $newData): bool
    {
        // Remove empty values for comparison
        $oldData = array_filter($oldData, fn ($value) => !empty($value));
        $newData = array_filter($newData, fn ($value) => !empty($value));

        return $oldData !== $newData;
    }

    /**
     * Check if observer is enabled
     */
    protected function isObserverEnabled(): bool
    {
        return $this->config['enabled'] ?? false;
    }

    /**
     * Check if events are enabled
     */
    protected function areEventsEnabled(): bool
    {
        return config('marketing-data-tracker.events.enabled', true);
    }

    /**
     * Check if model should be observed
     */
    public function shouldObserveModel(string $modelClass): bool
    {
        if (!$this->isObserverEnabled()) {
            return false;
        }

        $modelsToObserve = $this->config['models'] ?? [];

        return in_array($modelClass, $modelsToObserve);
    }

    /**
     * Register observer for configured models
     */
    public static function registerForConfiguredModels(): void
    {
        $config = config('marketing-data-tracker.observers', []);

        if (!($config['enabled'] ?? false)) {
            return;
        }

        $models = $config['models'] ?? [];

        foreach ($models as $modelClass) {
            if (class_exists($modelClass)) {
                $modelClass::observe(self::class);
            }
        }
    }

    /**
     * Get configured models
     */
    public function getConfiguredModels(): array
    {
        return $this->config['models'] ?? [];
    }

    /**
     * Add model to observation list
     */
    public function addModel(string $modelClass): void
    {
        $models = $this->getConfiguredModels();

        if (!in_array($modelClass, $models)) {
            $models[] = $modelClass;
            config(['marketing-data-tracker.observers.models' => $models]);
        }
    }

    /**
     * Remove model from observation list
     */
    public function removeModel(string $modelClass): void
    {
        $models = $this->getConfiguredModels();
        $models = array_filter($models, fn ($model) => $model !== $modelClass);
        config(['marketing-data-tracker.observers.models' => array_values($models)]);
    }

    /**
     * Enable observer
     */
    public function enable(): void
    {
        config(['marketing-data-tracker.observers.enabled' => true]);
    }

    /**
     * Disable observer
     */
    public function disable(): void
    {
        config(['marketing-data-tracker.observers.enabled' => false]);
    }

    /**
     * Get observer configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Update observer configuration
     */
    public function updateConfig(array $config): void
    {
        $this->config = array_merge($this->config, $config);
        config(['marketing-data-tracker.observers' => $this->config]);
    }
}
