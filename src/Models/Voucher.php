<?php

namespace Turahe\Ledger\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Turahe\Ledger\Database\Factories\VoucherFactory;
use Turahe\Ledger\Models\Concerns\HasLedgerAttributes;
use Turahe\Ledger\Models\Contracts\LedgerModelInterface;
use Turahe\Ledger\Models\Voucher\Item;
use Turahe\UserStamps\Concerns\HasUserStamps;

class Voucher extends Model implements LedgerModelInterface
{
    use HasLedgerAttributes;
    use HasUlids;
    use HasUserStamps;

    const EXPIRES_AT = 'due_date';

    public $dateFormat = 'U';

    protected $fillable = [
        'model_id',
        'model_type',
        'code',
        'note',
        'total_unit',
        'total_value',
        'due_date',
        'status',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'total_unit' => 'integer',
            'total_value' => 'decimal:2',
            'due_date' => 'datetime',
            'status' => 'string',
            'metadata' => 'object',
        ];
    }

    // Relationships
    public function shipping_provider(): BelongsTo
    {
        return $this->belongsTo(config('ledger.shipping_provider'), 'shipping_provider_id');
    }

    public function insurance_provider(): BelongsTo
    {
        return $this->belongsTo(config('ledger.insurance_provider'), 'insurance_provider_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'voucher_id');
    }

    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(
            Invoice::class,
            'invoice_payments',
            'receipt_id',
            'invoice_id',
        )->withPivot([
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
        ]);
    }

    // Business Logic Methods
    public function isExpired(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }

    public function isActive(): bool
    {
        return ! $this->isExpired() && $this->status !== 'cancelled';
    }

    public function getTotalValueFormatted(): string
    {
        return number_format($this->total_value, 2);
    }

    public function getDaysUntilExpiry(): ?int
    {
        if (! $this->due_date) {
            return null;
        }

        return now()->diffInDays($this->due_date, false);
    }

    // Interface Implementation
    public function getId(): string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getStatus(): string
    {
        return $this->status ?? 'active';
    }

    public function getTotalAmount(): float
    {
        return $this->total_value;
    }

    public function getCurrency(): string
    {
        return $this->currency ?? 'IDR';
    }

    public function getMetadata(): ?object
    {
        return $this->metadata;
    }

    public function setMetadata(object $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getCreatedAt(): ?\Carbon\Carbon
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?\Carbon\Carbon
    {
        return $this->updated_at;
    }

    protected static function newFactory()
    {
        return VoucherFactory::new();
    }
}
