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

    protected const string TABLE_NAME = 'voucher_items';
    protected const string DATE_FORMAT = 'U';

    public $dateFormat = self::DATE_FORMAT;
    protected $table = self::TABLE_NAME;

    protected $fillable = [
        'voucher_id',
        'model_id',
        'model_type',
        'quantity',
        'unit',
        'value',
    ];

    protected $casts = [
        'quantity' => 'float',
        'value' => 'decimal:4',
    ];

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }
}
