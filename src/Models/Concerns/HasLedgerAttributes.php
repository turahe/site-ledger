<?php

namespace Turahe\Ledger\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasLedgerAttributes
{
    /**
     * Scope to filter by model type
     */
    public function scopeByModelType(Builder $query, string $modelType): Builder
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope to filter by model ID
     */
    public function scopeByModelId(Builder $query, string $modelId): Builder
    {
        return $query->where('model_id', $modelId);
    }

    /**
     * Scope to filter by code
     */
    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('code', $code);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange(Builder $query, string $dateField, $startDate, $endDate): Builder
    {
        return $query->whereBetween($dateField, [$startDate, $endDate]);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmount(string $field, string $currency = 'IDR'): string
    {
        $amount = $this->getAttribute($field);
        if ($amount === null) {
            return '0.00';
        }

        return $currency.' '.number_format($amount, 2);
    }

    /**
     * Check if record is active
     */
    public function isActive(): bool
    {
        return $this->status !== 'cancelled' && $this->status !== 'deleted';
    }

    /**
     * Get metadata value safely
     */
    public function getMetadataValue(string $key, $default = null)
    {
        $metadata = $this->metadata;
        if (! $metadata || ! is_object($metadata)) {
            return $default;
        }

        return $metadata->{$key} ?? $default;
    }

    /**
     * Set metadata value safely
     */
    public function setMetadataValue(string $key, $value): void
    {
        $metadata = $this->metadata ?? (object) [];
        $metadata->{$key} = $value;
        $this->metadata = $metadata;
    }
}
