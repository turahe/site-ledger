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

    protected const string TABLE_NAME = 'invoice_items';
    protected const string DATE_FORMAT = 'U';

    public $dateFormat = self::DATE_FORMAT;
    protected $table = self::TABLE_NAME;

    protected $fillable = [
        'invoice_id',
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

    protected $casts = [
        'quantity' => 'float',
        'shipping_fee' => 'float',
        'insurance_fee' => 'float',
        'transaction_fee' => 'float',
        'discount_amount' => 'float',
        'tax_amount' => 'decimal:4',
        'service_amount' => 'decimal:4',
        'mdr_fee' => 'decimal:4',
        'price_unit' => 'float',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->quantity * $this->price_unit;
    }
}
