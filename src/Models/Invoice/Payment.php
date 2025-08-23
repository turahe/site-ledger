<?php

namespace Turahe\Ledger\Models\Invoice;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Turahe\Core\Concerns\HasConfigurablePrimaryKey;
use Turahe\Ledger\Enums\PaymentMethods;
use Turahe\UserStamps\Concerns\HasUserStamps;

/**
 * Invoice Payment Model
 *
 * Represents payment transactions for invoices, extending the pivot model
 * to handle the many-to-many relationship between invoices and payment receipts.
 *
 * Features:
 * - Payment status tracking (pending, completed, failed)
 * - Payment gateway and method information
 * - Payment fee calculations
 * - Expiration date management
 * - Multi-currency support
 * - Metadata support for additional payment information
 * - Audit trail with user stamps
 *
 * Database Table: invoice_payments
 *
 * @property string $id Primary key (ULID)
 * @property string $invoice_id The invoice ID this payment is for
 * @property string $receipt_id The payment receipt ID
 * @property string $currency Currency code (e.g., 'IDR', 'USD')
 * @property float $amount The payment amount
 * @property string $payment_gateway Payment gateway used (e.g., 'midtrans', 'xendit')
 * @property PaymentMethods $payment_method The payment method used
 * @property string $payment_channel Payment channel (e.g., 'web', 'mobile', 'api')
 * @property float $payment_fee Fee charged by the payment processor
 * @property string $payment_status_code Payment status (pending, completed, failed)
 * @property string $payment_status_message Detailed status message
 * @property \Carbon\Carbon $payment_issued_at When the payment was issued
 * @property \Carbon\Carbon $payment_expires_at When the payment expires
 * @property object|null $metadata Additional payment metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @author  Nur Wachid <wachid@outlook.com>
 *
 * @since   1.0.0
 */
class Payment extends Pivot
{
    use HasConfigurablePrimaryKey;
    use HasUserStamps;

    protected $table = 'invoice_payments';

    protected $fillable = [
        'invoice_id',
        'receipt_id',
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
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_fee' => 'decimal:2',
            'payment_method' => PaymentMethods::class,
            'payment_expires_at' => 'datetime',
            'payment_issued_at' => 'datetime',
            'metadata' => 'object',
        ];
    }

    /**
     * Check if the payment has expired
     *
     * Determines if the payment's expiration date has passed.
     *
     * @return bool True if the payment has expired, false otherwise
     */
    public function isExpired(): bool
    {
        return $this->payment_expires_at && $this->payment_expires_at->isPast();
    }

    /**
     * Check if the payment is pending
     *
     * @return bool True if the payment status is pending, false otherwise
     */
    public function isPending(): bool
    {
        return $this->payment_status_code === 'pending';
    }

    /**
     * Check if the payment is completed
     *
     * @return bool True if the payment status is completed, false otherwise
     */
    public function isCompleted(): bool
    {
        return $this->payment_status_code === 'completed';
    }

    /**
     * Check if the payment failed
     *
     * @return bool True if the payment status is failed, false otherwise
     */
    public function isFailed(): bool
    {
        return $this->payment_status_code === 'failed';
    }

    /**
     * Calculate the net amount after payment fees
     *
     * Returns the actual amount received after deducting payment processor fees.
     *
     * @return float The net amount (amount - payment_fee)
     */
    public function getNetAmount(): float
    {
        return $this->amount - $this->payment_fee;
    }

    /**
     * Get the payment amount formatted as a string
     *
     * @return string The formatted payment amount (e.g., "1,500.00")
     */
    public function getAmountFormatted(): string
    {
        return number_format($this->amount, 2);
    }

    /**
     * Get the payment fee formatted as a string
     *
     * @return string The formatted payment fee (e.g., "15.00")
     */
    public function getPaymentFeeFormatted(): string
    {
        return number_format($this->payment_fee, 2);
    }
}
