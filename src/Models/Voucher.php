<?php

namespace Turahe\Ledger\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Turahe\Ledger\Database\Factories\VoucherFactory;
use Turahe\Ledger\Models\Voucher\Item;
use Turahe\UserStamps\Concerns\HasUserStamps;

class Voucher extends Model
{
    use HasUlids;
    use HasUserStamps;

    public const string EXPIRES_AT = 'due_date';
    protected const string DATE_FORMAT = 'U';

    public $dateFormat = self::DATE_FORMAT;

    protected $fillable = [
        'model_id',
        'model_type',
        'code',
        'note',
        'total_unit',
        'total_value',
    ];

    protected $casts = [
        'total_unit' => 'decimal:4',
        'total_value' => 'decimal:4',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class, 'invoice_payments', 'receipt_id', 'invoice_id')
            ->withPivot([
                'currency',
                'amount',
                'payment_gateway',
                'payment_method',
                'payment_fee',
                'payment_status_code',
                'payment_status_message',
            ])
            ->withTimestamps();
    }

    protected static function newFactory(): VoucherFactory
    {
        return new VoucherFactory();
    }
}
