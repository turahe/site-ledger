<?php

namespace Turahe\Ledger\Models\Invoice;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Turahe\Ledger\Models\Invoice;
use Turahe\UserStamps\Concerns\HasUserStamps;

class Item extends Model
{
    use HasUlids;
    use HasUserStamps;

    public $dateFormat = 'U';

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

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    // Business Logic Methods
    public function getSubtotal(): float
    {
        return $this->quantity * $this->price_unit;
    }

    public function getTotalFees(): float
    {
        return $this->shipping_fee + $this->insurance_fee + $this->transaction_fee + $this->service_amount;
    }

    public function getTotalDiscount(): float
    {
        return $this->discount_voucher + $this->discount_amount;
    }

    public function getTotalTax(): float
    {
        return $this->tax_amount;
    }

    public function getTotalAmount(): float
    {
        return $this->getSubtotal() + $this->getTotalFees() - $this->getTotalDiscount() + $this->getTotalTax();
    }

    public function getDiscountPercentage(): float
    {
        $subtotal = $this->getSubtotal();
        if ($subtotal <= 0) {
            return 0;
        }

        return round(($this->getTotalDiscount() / $subtotal) * 100, 2);
    }
}
