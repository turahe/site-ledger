<?php

namespace Turahe\Ledger\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Has Ledger Attributes Trait
 *
 * Provides common functionality and query scopes for ledger models.
 * This trait encapsulates reusable methods for filtering, formatting,
 * and manipulating ledger-related data across different models.
 *
 * Features:
 * - Query scopes for filtering by model type, ID, code, date range, and status
 * - Amount formatting with currency support
 * - Safe metadata handling
 * - Common status checking methods
 *
 * @package Turahe\Ledger\Models\Concerns
 * @author  Nur Wachid <wachid@outlook.com>
 * @since   1.0.0
 */
trait HasLedgerAttributes
{
    /**
     * Scope to filter by model type
     *
     * Filters records based on the polymorphic model_type field.
     *
     * @param Builder $query The query builder instance
     * @param string $modelType The model class name to filter by
     * @return Builder The modified query builder
     *
     * @example
     * ```php
     * Invoice::byModelType(User::class)->get();
     * ```
     */
    public function scopeByModelType(Builder $query, string $modelType): Builder
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope to filter by model ID
     *
     * Filters records based on the polymorphic model_id field.
     *
     * @param Builder $query The query builder instance
     * @param string $modelId The model ID to filter by
     * @return Builder The modified query builder
     *
     * @example
     * ```php
     * Invoice::byModelId($user->id)->get();
     * ```
     */
    public function scopeByModelId(Builder $query, string $modelId): Builder
    {
        return $query->where('model_id', $modelId);
    }

    /**
     * Scope to filter by code
     *
     * Filters records by their unique business code.
     *
     * @param Builder $query The query builder instance
     * @param string $code The code to filter by
     * @return Builder The modified query builder
     *
     * @example
     * ```php
     * Invoice::byCode('INV-001')->first();
     * ```
     */
    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('code', $code);
    }

    /**
     * Scope to filter by date range
     *
     * Filters records within a specified date range for any date field.
     *
     * @param Builder $query The query builder instance
     * @param string $dateField The date field name to filter on
     * @param mixed $startDate The start date
     * @param mixed $endDate The end date
     * @return Builder The modified query builder
     *
     * @example
     * ```php
     * Invoice::byDateRange('created_at', '2025-01-01', '2025-12-31')->get();
     * ```
     */
    public function scopeByDateRange(Builder $query, string $dateField, $startDate, $endDate): Builder
    {
        return $query->whereBetween($dateField, [$startDate, $endDate]);
    }

    /**
     * Scope to filter by status
     *
     * Filters records by their current status.
     *
     * @param Builder $query The query builder instance
     * @param string $status The status to filter by
     * @return Builder The modified query builder
     *
     * @example
     * ```php
     * Invoice::byStatus('pending')->get();
     * ```
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Get formatted amount with currency
     *
     * Formats a monetary amount with currency symbol and proper number formatting.
     *
     * @param string $field The field name containing the amount
     * @param string $currency The currency code (default: 'IDR')
     * @return string The formatted amount string
     *
     * @example
     * ```php
     * $invoice->getFormattedAmount('total_invoice', 'USD'); // "USD 1,500.00"
     * ```
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
     *
     * Determines if the record is in an active state (not cancelled or deleted).
     *
     * @return bool True if the record is active, false otherwise
     */
    public function isActive(): bool
    {
        return $this->status !== 'cancelled' && $this->status !== 'deleted';
    }

    /**
     * Get metadata value safely
     *
     * Safely retrieves a value from the metadata object without throwing errors.
     *
     * @param string $key The metadata key to retrieve
     * @param mixed $default The default value if key is not found
     * @return mixed The metadata value or default
     *
     * @example
     * ```php
     * $invoice->getMetadataValue('shipping_notes', 'No notes');
     * ```
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
     *
     * Safely sets a value in the metadata object, creating the object if it doesn't exist.
     *
     * @param string $key The metadata key to set
     * @param mixed $value The value to set
     * @return void
     *
     * @example
     * ```php
     * $invoice->setMetadataValue('shipping_notes', 'Handle with care');
     * ```
     */
    public function setMetadataValue(string $key, $value): void
    {
        $metadata = $this->metadata ?? (object) [];
        $metadata->{$key} = $value;
        $this->metadata = $metadata;
    }
}
