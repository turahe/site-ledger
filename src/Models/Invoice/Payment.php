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

    public $dateFormat = 'U';

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

    // Business Logic Methods
    public function isExpired(): bool
    {
        return $this->payment_expires_at && $this->payment_expires_at->isPast();
    }

    public function isPending(): bool
    {
        return $this->payment_status_code === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->payment_status_code === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->payment_status_code === 'failed';
    }

    public function getNetAmount(): float
    {
        return $this->amount - $this->payment_fee;
    }

    public function getAmountFormatted(): string
    {
        return number_format($this->amount, 2);
    }

    public function getPaymentFeeFormatted(): string
    {
        return number_format($this->payment_fee, 2);
    }
}
