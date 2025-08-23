<?php

namespace Turahe\Ledger\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Turahe\Core\Concerns\HasConfigurablePrimaryKey;
use Turahe\Ledger\Models\Concerns\HasLedgerAttributes;
use Turahe\Ledger\Models\Contracts\LedgerModelInterface;
use Turahe\Ledger\Models\Invoice\Item;
use Turahe\UserStamps\Concerns\HasUserStamps;

/**
 * Invoice Model
 *
 * Represents an invoice in the ledger system with comprehensive billing capabilities.
 * Supports multi-currency transactions, various fees (shipping, insurance, tax),
 * discounts, payment tracking, and flexible polymorphic relationships.
 *
 * Features:
 * - Polymorphic relationship to any model (User, Organization, etc.)
 * - Comprehensive fee structure (shipping, insurance, transaction, MDR)
 * - Discount and tax calculations
 * - Payment tracking with installment support
 * - Multi-currency support
 * - Status management (pending, paid, overdue, cancelled)
 * - Automatic total calculations
 * - Audit trail with user stamps
 *
 * Database Table: invoices
 *
 * @property string $id Primary key (ULID)
 * @property string $model_id Related model ID (polymorphic)
 * @property string $model_type Related model class (polymorphic)
 * @property string $code Unique invoice code
 * @property string|null $shipping_provider_id Shipping provider reference
 * @property float $shipping_fee Shipping cost
 * @property string|null $insurance_provider_id Insurance provider reference
 * @property float $insurance_fee Insurance cost
 * @property float $transaction_fee Payment processing fee
 * @property string $currency Currency code (e.g., 'IDR', 'USD')
 * @property string|null $discount_voucher Applied discount voucher code
 * @property float $discount_amount Total discount amount
 * @property float $tax_amount Tax amount
 * @property float $service_amount Service fee
 * @property float $mdr_fee Merchant discount rate fee
 * @property float $total_amount Subtotal before fees and taxes
 * @property float $total_invoice Final invoice total
 * @property float $total_unpaid Remaining unpaid amount
 * @property float $total_payment Total payments received
 * @property float $total_change Change amount (if overpaid)
 * @property float $minimum_down_payment Minimum required down payment
 * @property \Carbon\Carbon $issue_date Invoice issue date
 * @property \Carbon\Carbon $due_date Payment due date
 * @property string $status Invoice status (pending, paid, overdue, cancelled)
 * @property object|null $metadata Additional invoice metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package Turahe\Ledger\Models
 * @author  Nur Wachid <wachid@outlook.com>
 * @since   1.0.0
 */
class Invoice extends Model implements LedgerModelInterface
{
    use HasConfigurablePrimaryKey;
    use HasLedgerAttributes;
    use HasUserStamps;

    protected $table = 'invoices';

    protected $fillable = [
        'model_id',
        'model_type',
        'code',
        'shipping_provider_id',
        'shipping_fee',
        'insurance_provider_id',
        'insurance_fee',
        'transaction_fee',
        'currency',
        'discount_voucher',
        'discount_amount',
        'tax_amount',
        'service_amount',
        'mdr_fee',
        'total_amount',
        'total_invoice',
        'total_unpaid',
        'total_payment',
        'total_change',
        'minimum_down_payment',
        'issue_date',
        'due_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'object',
            'due_date' => 'datetime',
            'issue_date' => 'datetime',
            'shipping_fee' => 'decimal:2',
            'insurance_fee' => 'decimal:2',
            'transaction_fee' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'discount_voucher' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'service_amount' => 'decimal:2',
            'mdr_fee' => 'decimal:2',
            'total_invoice' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'total_payment' => 'decimal:2',
            'total_unpaid' => 'decimal:2',
            'total_change' => 'decimal:2',
            'minimum_down_payment' => 'decimal:2',
        ];
    }

    /**
     * Relationship to invoice items
     *
     * Links the invoice to its individual items for detailed tracking.
     *
     * @return HasMany The invoice items relationship
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'invoice_id', 'id');
    }

    /**
     * Relationship to the customer
     *
     * Alias for the author relationship, representing the customer
     * who is responsible for this invoice.
     *
     * @return mixed The customer relationship
     */
    public function customer()
    {
        return $this->author();
    }

    /**
     * Relationship to payment vouchers
     *
     * Links the invoice to payment vouchers through the pivot table.
     * Includes payment details like gateway, method, fees, and status.
     *
     * @return BelongsToMany The payment vouchers relationship
     */
    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(
            Voucher::class,
            'invoice_payments',
            'invoice_id',
            'receipt_id'
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

    // Business Logic Methods
    /**
     * Check if the invoice is overdue
     *
     * Determines if the invoice has passed its due date and still has unpaid amounts.
     *
     * @return bool True if the invoice is overdue, false otherwise
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->total_unpaid > 0;
    }

    /**
     * Check if the invoice is fully paid
     *
     * Determines if all amounts on the invoice have been paid.
     *
     * @return bool True if the invoice is fully paid, false otherwise
     */
    public function isFullyPaid(): bool
    {
        return $this->total_unpaid <= 0;
    }

    /**
     * Get the remaining unpaid amount
     *
     * Returns the amount still owed on the invoice.
     *
     * @return float The remaining unpaid amount
     */
    public function getRemainingAmount(): float
    {
        return max(0, $this->total_unpaid);
    }

    /**
     * Calculate the payment percentage
     *
     * Returns the percentage of the invoice total that has been paid.
     *
     * @return float The payment percentage (0-100)
     */
    public function getPaymentPercentage(): float
    {
        if ($this->total_invoice <= 0) {
            return 0;
        }

        return round(($this->total_payment / $this->total_invoice) * 100, 2);
    }

    // Interface Implementation
    /**
     * Get the invoice's unique identifier
     *
     * @return string The invoice's primary key
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the invoice's unique code
     *
     * @return string The invoice's business code
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Get the invoice's current status
     *
     * @return string The invoice's status, defaults to 'draft' if not set
     */
    public function getStatus(): string
    {
        return $this->status ?? 'draft';
    }

    /**
     * Get the invoice's total monetary amount
     *
     * @return string The invoice's total amount
     */
    public function getTotalAmount(): float
    {
        return $this->total_invoice;
    }

    /**
     * Get the invoice's currency
     *
     * @return string The currency code, defaults to 'IDR' if not set
     */
    public function getCurrency(): string
    {
        return $this->currency ?? 'IDR';
    }

    /**
     * Get the invoice's metadata
     *
     * @return object|null The metadata object or null if not set
     */
    public function getMetadata(): ?object
    {
        return $this->metadata;
    }

    /**
     * Set the invoice's metadata
     *
     * @param object $metadata The metadata object to set
     * @return void
     */
    public function setMetadata(object $metadata): void
    {
        $this->metadata = $metadata;
    }

    /**
     * Get the invoice's creation timestamp
     *
     * @return \Carbon\Carbon|null The creation timestamp or null
     */
    public function getCreatedAt(): ?\Carbon\Carbon
    {
        return $this->created_at;
    }

    /**
     * Get the invoice's last update timestamp
     *
     * @return \Carbon\Carbon|null The last update timestamp or null
     */
    public function getUpdatedAt(): ?\Carbon\Carbon
    {
        return $this->updated_at;
    }
}
