<?php

namespace Marshmallow\MarketingData\Events;

use Illuminate\Database\Eloquent\Model;

class ClickIdDetected extends MarketingDataEvent
{
    public function __construct(
        Model $model,
        public string $clickId,
        public string $clickIdType,
        public ?string $platform = null,
        array $parameters = []
    ) {
        parent::__construct($model, $parameters);
    }

    /**
     * Get the event description
     */
    public function getDescription(): string
    {
        $platform = $this->platform ? " from {$this->platform}" : '';

        return "Click ID '{$this->clickIdType}' detected for {$this->getModelClass()} with ID {$this->getModelId()}{$platform}";
    }

    /**
     * Get click ID data
     */
    public function getClickIdInfo(): array
    {
        return [
            'click_id' => $this->clickId,
            'type' => $this->clickIdType,
            'platform' => $this->platform,
            'detected_at' => $this->getTimestamp()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get the detected platform name
     */
    public function getDetectedPlatform(): ?string
    {
        return $this->platform ?: $this->getPlatformName();
    }

    /**
     * Check if this is a Google click ID
     */
    public function isGoogleClickId(): bool
    {
        return in_array($this->clickIdType, ['gclid', 'gbraid', 'wbraid']);
    }

    /**
     * Check if this is a Facebook click ID
     */
    public function isFacebookClickId(): bool
    {
        return $this->clickIdType === 'fbclid';
    }

    /**
     * Check if this is a Microsoft click ID
     */
    public function isMicrosoftClickId(): bool
    {
        return $this->clickIdType === 'msclkid';
    }

    /**
     * Check if this is a TikTok click ID
     */
    public function isTikTokClickId(): bool
    {
        return $this->clickIdType === 'ttclid';
    }

    /**
     * Check if this is a mobile attribution click ID (iOS)
     */
    public function isMobileClickId(): bool
    {
        return in_array($this->clickIdType, ['gbraid', 'wbraid']);
    }

    /**
     * Get click ID priority based on configuration
     */
    public function getClickIdPriority(): int
    {
        $priorities = config('marketing-data-tracker.click_id_management.platform_priority', []);

        return $priorities[$this->clickIdType] ?? 0;
    }

    /**
     * Check if this click ID should override existing ones
     */
    public function shouldOverrideExisting(): bool
    {
        $currentClickIds = $this->getClickIdData();

        if (empty($currentClickIds)) {
            return true; // No existing click IDs, always set
        }

        $myPriority = $this->getClickIdPriority();

        // Check if any existing click ID has higher priority
        foreach (array_keys($currentClickIds) as $existingType) {
            $existingPriority = config("marketing-data-tracker.click_id_management.platform_priority.{$existingType}", 0);
            if ($existingPriority > $myPriority) {
                return false; // Higher priority click ID exists
            }
        }

        return true; // This click ID has highest priority
    }

    /**
     * Get the clean click ID value
     */
    public function getCleanClickId(): string
    {
        $clickId = $this->clickId;

        // Apply Google-specific cleaning if needed
        if ($this->isGoogleClickId()) {
            $config = config('marketing-data-tracker.click_id_management.google_click_ids', []);
            $extractValue = $config['extract_gclid_value'] ?? true;

            if ($extractValue && $this->clickIdType === 'gclid' && str_contains($clickId, '.')) {
                $clickId = mb_substr($clickId, mb_strrpos($clickId, '.') + 1);
            }
        }

        return mb_trim($clickId);
    }

    /**
     * Get the source of detection (parameter, cookie, etc.)
     */
    public function getDetectionSource(): string
    {
        return $this->parameters['source'] ?? 'unknown';
    }

    /**
     * Check if click ID was detected from a cookie
     */
    public function isFromCookie(): bool
    {
        return $this->getDetectionSource() === 'cookie';
    }

    /**
     * Check if click ID was detected from URL parameter
     */
    public function isFromParameter(): bool
    {
        return $this->getDetectionSource() === 'parameter';
    }

    /**
     * Get additional attribution context
     */
    public function getAttributionContext(): array
    {
        $context = $this->getAttributionData();

        return array_merge($context, [
            'click_id_type' => $this->clickIdType,
            'click_id_value' => $this->getCleanClickId(),
            'detection_source' => $this->getDetectionSource(),
            'detected_platform' => $this->getDetectedPlatform(),
            'priority' => $this->getClickIdPriority(),
            'should_override' => $this->shouldOverrideExisting(),
        ]);
    }

    /**
     * Get session data for storing click ID
     */
    public function getSessionData(): array
    {
        return [
            'click_id' => $this->getCleanClickId(),
            'type' => $this->clickIdType,
            'platform' => $this->getDetectedPlatform(),
            'detected_at' => $this->getTimestamp()->format('Y-m-d H:i:s'),
            'model_class' => $this->getModelClass(),
            'model_id' => $this->getModelId(),
        ];
    }

    /**
     * Convert to array with additional click ID-specific data
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'description' => $this->getDescription(),
            'click_id' => $this->clickId,
            'click_id_type' => $this->clickIdType,
            'platform' => $this->platform,
            'click_id_info' => $this->getClickIdInfo(),
            'detected_platform' => $this->getDetectedPlatform(),
            'clean_click_id' => $this->getCleanClickId(),
            'detection_source' => $this->getDetectionSource(),
            'is_google' => $this->isGoogleClickId(),
            'is_facebook' => $this->isFacebookClickId(),
            'is_microsoft' => $this->isMicrosoftClickId(),
            'is_tiktok' => $this->isTikTokClickId(),
            'is_mobile' => $this->isMobileClickId(),
            'is_from_cookie' => $this->isFromCookie(),
            'is_from_parameter' => $this->isFromParameter(),
            'priority' => $this->getClickIdPriority(),
            'should_override' => $this->shouldOverrideExisting(),
            'attribution_context' => $this->getAttributionContext(),
            'session_data' => $this->getSessionData(),
        ]);
    }
}
