<?php

namespace Turahe\Ledger\Models\Voucher;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Turahe\Core\Concerns\HasConfigurablePrimaryKey;
use Turahe\Ledger\Models\Voucher;
use Turahe\UserStamps\Concerns\HasUserStamps;

/**
 * Voucher Item Model
 *
 * Represents individual items within a voucher, allowing detailed tracking
 * of quantities, prices, and descriptions for each voucher component.
 *
 * Features:
 * - Polymorphic relationship to any model (User, Organization, etc.)
 * - Quantity and unit price tracking
 * - Automatic total price calculations
 * - Metadata support for additional item information
 * - Audit trail with user stamps
 *
 * Database Table: voucher_items
 *
 * @property string $id Primary key (ULID)
 * @property string $model_id Related model ID (polymorphic)
 * @property string $model_type Related model class (polymorphic)
 * @property int $quantity Number of units for this item
 * @property float $unit_price Price per unit
 * @property float $total_price Total price for this item
 * @property string|null $description Item description or notes
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

    protected $table = 'voucher_items';

    protected $fillable = [
        'model_id',
        'model_type',
        'quantity',
        'unit_price',
        'total_price',
        'description',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'metadata' => 'object',
        ];
    }

    /**
     * Relationship to the parent voucher
     *
     * Links the item back to its parent voucher for navigation and calculations.
     *
     * @return BelongsTo The voucher relationship
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
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
        return $this->quantity * $this->unit_price;
    }

    /**
     * Get the total price for this item
     *
     * Returns the stored total price if available, otherwise calculates it.
     *
     * @return float The total price for this item
     */
    public function getTotalPrice(): float
    {
        return $this->total_price ?? $this->getSubtotal();
    }

    /**
     * Get the unit price formatted as a string
     *
     * @return string The formatted unit price (e.g., "25.00")
     */
    public function getUnitPriceFormatted(): string
    {
        return number_format($this->unit_price, 2);
    }

    /**
     * Get the total price formatted as a string
     *
     * @return string The formatted total price (e.g., "100.00")
     */
    public function getTotalPriceFormatted(): string
    {
        return number_format($this->getTotalPrice(), 2);
    }
}
