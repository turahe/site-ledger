<?php

namespace Turahe\Ledger\Models\Voucher;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Turahe\Ledger\Models\Voucher;
use Turahe\UserStamps\Concerns\HasUserStamps;

class Item extends Model
{
    use HasUlids;
    use HasUserStamps;

    public $dateFormat = 'U';

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

    // Relationships
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    // Business Logic Methods
    public function getSubtotal(): float
    {
        return $this->quantity * $this->unit_price;
    }

    public function getTotalPrice(): float
    {
        return $this->total_price ?? $this->getSubtotal();
    }

    public function getUnitPriceFormatted(): string
    {
        return number_format($this->unit_price, 2);
    }

    public function getTotalPriceFormatted(): string
    {
        return number_format($this->getTotalPrice(), 2);
    }
}
