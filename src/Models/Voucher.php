<?php

namespace Turahe\Ledger\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Turahe\Core\Concerns\HasConfigurablePrimaryKey;
use Turahe\Ledger\Models\Concerns\HasLedgerAttributes;
use Turahe\Ledger\Models\Contracts\LedgerModelInterface;
use Turahe\Ledger\Models\Voucher\Item;
use Turahe\UserStamps\Concerns\HasUserStamps;

/**
 * Voucher Model
 *
 * Represents a voucher in the ledger system for managing prepaid credits, gift cards,
 * and promotional offers. Supports expiration dates, status tracking, and flexible
 * polymorphic relationships.
 *
 * Features:
 * - Polymorphic relationship to any model (User, Organization, etc.)
 * - Expiration date management with automatic status updates
 * - Multi-currency support
 * - Status management (active, expired, cancelled, used)
 * - Item-based voucher structure for detailed tracking
 * - Payment relationship tracking
 * - Audit trail with user stamps
 *
 * Database Table: vouchers
 *
 * @property string $id Primary key (ULID)
 * @property string $model_id Related model ID (polymorphic)
 * @property string $model_type Related model class (polymorphic)
 * @property string $code Unique voucher code
 * @property string|null $note Additional voucher notes
 * @property int $total_unit Total number of units available
 * @property float $total_value Total monetary value of the voucher
 * @property \Carbon\Carbon $due_date Expiration date
 * @property string $status Voucher status (active, expired, cancelled, used)
 * @property string $currency Currency code (e.g., 'IDR', 'USD')
 * @property object|null $metadata Additional voucher metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @author  Nur Wachid <wachid@outlook.com>
 *
 * @since   1.0.0
 */
class Voucher extends Model implements LedgerModelInterface
{
    use HasConfigurablePrimaryKey;
    use HasLedgerAttributes;
    use HasUserStamps;

    /**
     * Field name for expiration date
     *
     * Used to identify which field contains the voucher expiration date
     * for automatic status updates and validation.
     */
    const EXPIRES_AT = 'due_date';

    /**
     * Date format for timestamps
     *
     * Uses Unix timestamp format for efficient date storage and comparison.
     */
    public $dateFormat = 'U';

    protected $fillable = [
        'model_id',
        'model_type',
        'code',
        'note',
        'total_unit',
        'total_value',
        'due_date',
        'status',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'total_unit' => 'integer',
            'total_value' => 'decimal:2',
            'due_date' => 'datetime',
            'status' => 'string',
            'metadata' => 'object',
        ];
    }

    /**
     * Relationship to shipping provider
     *
     * Links the voucher to a shipping service provider for delivery tracking.
     *
     * @return BelongsTo The shipping provider relationship
     */
    public function shipping_provider(): BelongsTo
    {
        return $this->belongsTo(config('ledger.shipping_provider'), 'shipping_provider_id');
    }

    /**
     * Relationship to insurance provider
     *
     * Links the voucher to an insurance provider for coverage details.
     *
     * @return BelongsTo The insurance provider relationship
     */
    public function insurance_provider(): BelongsTo
    {
        return $this->belongsTo(config('ledger.insurance_provider'), 'insurance_provider_id');
    }

    /**
     * Relationship to voucher items
     *
     * Links the voucher to its individual items for detailed tracking.
     *
     * @return HasMany The voucher items relationship
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'voucher_id');
    }

    /**
     * Relationship to invoice payments
     *
     * Links the voucher to invoice payments through the pivot table.
     * Includes payment details like gateway, method, fees, and status.
     *
     * @return BelongsToMany The invoice payments relationship
     */
    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(
            Invoice::class,
            'invoice_payments',
            'receipt_id',
            'invoice_id',
        )->withPivot([
            'currency',
            'amount',
            'payment_gateway',
            'payment_method',
            'payment_channel',
            'payment_fee',
            'payment_status_code',
            'payment_status_message',
            'payment_issued_at',
            'payment_expires_at',
            'metadata',
        ]);
    }

    /**
     * Check if the voucher has expired
     *
     * Determines if the voucher's due date has passed, making it unusable.
     *
     * @return bool True if the voucher has expired, false otherwise
     */
    public function isExpired(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }

    /**
     * Check if the voucher is active and usable
     *
     * A voucher is considered active if it hasn't expired and isn't cancelled.
     *
     * @return bool True if the voucher is active, false otherwise
     */
    public function isActive(): bool
    {
        return ! $this->isExpired() && $this->status !== 'cancelled';
    }

    /**
     * Get the total value formatted as a string
     *
     * Returns the voucher's total value formatted with proper number formatting.
     *
     * @return string The formatted total value (e.g., "1,500.00")
     */
    public function getTotalValueFormatted(): string
    {
        return number_format($this->total_value, 2);
    }

    /**
     * Get the number of days until the voucher expires
     *
     * Returns the number of days remaining before the voucher expires.
     * Negative values indicate the voucher has already expired.
     *
     * @return int|null The number of days until expiry, or null if no due date is set
     */
    public function getDaysUntilExpiry(): ?int
    {
        if (! $this->due_date) {
            return null;
        }

        return now()->diffInDays($this->due_date, false);
    }

    // Interface Implementation
    /**
     * Get the voucher's unique identifier
     *
     * @return string The voucher's primary key
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the voucher's unique code
     *
     * @return string The voucher's business code
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Get the voucher's current status
     *
     * @return string The voucher's status, defaults to 'active' if not set
     */
    public function getStatus(): string
    {
        return $this->status ?? 'active';
    }

    /**
     * Get the voucher's total monetary amount
     *
     * @return float The total value of the voucher
     */
    public function getTotalAmount(): float
    {
        return $this->total_value;
    }

    /**
     * Get the voucher's currency
     *
     * @return string The currency code, defaults to 'IDR' if not set
     */
    public function getCurrency(): string
    {
        return $this->currency ?? 'IDR';
    }

    /**
     * Get the voucher's metadata
     *
     * @return object|null The metadata object or null if not set
     */
    public function getMetadata(): ?object
    {
        return $this->metadata;
    }

    /**
     * Set the voucher's metadata
     *
     * @param  object  $metadata  The metadata object to set
     */
    public function setMetadata(object $metadata): void
    {
        $this->metadata = $metadata;
    }

    /**
     * Get the voucher's creation timestamp
     *
     * @return \Carbon\Carbon|null The creation timestamp or null
     */
    public function getCreatedAt(): ?\Carbon\Carbon
    {
        return $this->created_at;
    }

    /**
     * Get the voucher's last update timestamp
     *
     * @return \Carbon\Carbon|null The last update timestamp or null
     */
    public function getUpdatedAt(): ?\Carbon\Carbon
    {
        return $this->updated_at;
    }
}
