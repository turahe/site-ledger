<?php

namespace Turahe\Ledger\Models\Invoice;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Turahe\Core\Concerns\HasConfigurablePrimaryKey;
use Turahe\Ledger\Models\Invoice;
use Turahe\UserStamps\Concerns\HasUserStamps;

/**
 * Invoice Item Model
 *
 * Represents individual items within an invoice, providing detailed tracking
 * of quantities, prices, fees, discounts, and taxes for each invoice component.
 *
 * Features:
 * - Polymorphic relationship to any model (User, Organization, etc.)
 * - Comprehensive fee structure (shipping, insurance, transaction, service, MDR)
 * - Discount and tax calculations
 * - Multi-currency support
 * - Automatic total calculations
 * - Metadata support for additional item information
 * - Audit trail with user stamps
 *
 * Database Table: invoice_items
 *
 * @property string $id Primary key (ULID)
 * @property string $model_id Related model ID (polymorphic)
 * @property string $model_type Related model class (polymorphic)
 * @property int $quantity Number of units for this item
 * @property string|null $shipping_provider_id Shipping provider reference
 * @property float $shipping_fee Shipping cost for this item
 * @property string|null $insurance_provider_id Insurance provider reference
 * @property float $insurance_fee Insurance cost for this item
 * @property float $transaction_fee Payment processing fee
 * @property float $discount_voucher Applied voucher discount
 * @property float $discount_amount Additional discount amount
 * @property float $tax_amount Tax amount for this item
 * @property float $service_amount Service fee for this item
 * @property float $mdr_fee Merchant discount rate fee
 * @property string $currency Currency code (e.g., 'IDR', 'USD')
 * @property float $price_unit Unit price for this item
 * @property object|null $metadata Additional item metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @author  Nur Wachid <wachid@outlook.com>
 *
 * @since   1.0.0
 */
class Item extends Model
{
    use HasConfigurablePrimaryKey;
    use HasUserStamps;

    protected $table = 'invoice_items';

    protected $fillable = [
        'model_id',
        'model_type',
        'quantity',
        'shipping_provider_id',
        'shipping_fee',
        'insurance_provider_id',
        'insurance_fee',
        'transaction_fee',
        'discount_voucher',
        'discount_amount',
        'tax_amount',
        'service_amount',
        'mdr_fee',
        'currency',
        'price_unit',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'shipping_fee' => 'decimal:2',
            'insurance_fee' => 'decimal:2',
            'transaction_fee' => 'decimal:2',
            'discount_voucher' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'service_amount' => 'decimal:2',
            'mdr_fee' => 'decimal:2',
            'price_unit' => 'decimal:2',
            'metadata' => 'object',
        ];
    }

    /**
     * Relationship to the parent invoice
     *
     * Links the item back to its parent invoice for navigation and calculations.
     *
     * @return BelongsTo The invoice relationship
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    // Business Logic Methods
    /**
     * Calculate the subtotal for this item
     *
     * Multiplies quantity by unit price to get the base subtotal.
     *
     * @return float The calculated subtotal
     */
    public function getSubtotal(): float
    {
        return $this->quantity * $this->price_unit;
    }

    /**
     * Calculate the total fees for this item
     *
     * Sums all applicable fees: shipping, insurance, transaction, and service fees.
     *
     * @return float The total fees amount
     */
    public function getTotalFees(): float
    {
        return $this->shipping_fee + $this->insurance_fee + $this->transaction_fee + $this->service_amount;
    }

    /**
     * Calculate the total discount for this item
     *
     * Sums voucher discounts and additional discount amounts.
     *
     * @return float The total discount amount
     */
    public function getTotalDiscount(): float
    {
        return $this->discount_voucher + $this->discount_amount;
    }

    /**
     * Get the tax amount for this item
     *
     * @return float The tax amount
     */
    public function getTotalTax(): float
    {
        return $this->tax_amount;
    }

    /**
     * Calculate the total amount for this item
     *
     * Formula: Subtotal + Fees - Discounts + Taxes
     * This represents the final amount to be charged for this item.
     *
     * @return float The total amount for this item
     */
    public function getTotalAmount(): float
    {
        return $this->getSubtotal() + $this->getTotalFees() - $this->getTotalDiscount() + $this->getTotalTax();
    }

    /**
     * Calculate the discount percentage for this item
     *
     * Returns the percentage of discount applied relative to the subtotal.
     *
     * @return float The discount percentage (0-100)
     */
    public function getDiscountPercentage(): float
    {
        $subtotal = $this->getSubtotal();
        if ($subtotal <= 0) {
            return 0;
        }

        return round(($this->getTotalDiscount() / $subtotal) * 100, 2);
    }
}
