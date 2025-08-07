<?php

namespace Turahe\Ledger\Models\Invoice;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Turahe\Ledger\Enums\PaymentMethods;
use Turahe\UserStamps\Concerns\HasUserStamps;

class Payment extends Pivot
{
    use HasUlids;
    use HasUserStamps;

    protected const string TABLE_NAME = 'invoice_payments';
    protected const string DATE_FORMAT = 'U';

    public $dateFormat = self::DATE_FORMAT;
    protected $table = self::TABLE_NAME;

    protected $casts = [
        'payment_method' => PaymentMethods::class,
        'payment_expires_at' => 'datetime',
        'payment_issued_at' => 'datetime',
    ];
}
