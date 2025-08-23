<?php

namespace Turahe\Ledger\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Turahe\Ledger\Database\Factories\InvoiceFactory;
use Turahe\Ledger\Models\Concerns\HasLedgerAttributes;
use Turahe\Ledger\Models\Contracts\LedgerModelInterface;
use Turahe\Ledger\Models\Invoice\Item;
use Turahe\UserStamps\Concerns\HasUserStamps;

class Invoice extends Model implements LedgerModelInterface
{
    use HasLedgerAttributes;
    use HasUlids;
    use HasUserStamps;

    protected $table = 'invoices';

    public $dateFormat = 'U';

    protected $fillable = [
        'model_id',
        'model_type',
        'code',
        'shipping_provider_id',
        'shipping_fee',
        'insurance_provider_id',
        'insurance_fee',
        'transaction_fee',
        'currency',
        'discount_voucher',
        'discount_amount',
        'tax_amount',
        'service_amount',
        'mdr_fee',
        'total_amount',
        'total_invoice',
        'total_unpaid',
        'total_payment',
        'total_change',
        'minimum_down_payment',
        'issue_date',
        'due_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'object',
            'due_date' => 'datetime',
            'issue_date' => 'datetime',
            'shipping_fee' => 'decimal:2',
            'insurance_fee' => 'decimal:2',
            'transaction_fee' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'discount_voucher' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'service_amount' => 'decimal:2',
            'mdr_fee' => 'decimal:2',
            'total_invoice' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'total_payment' => 'decimal:2',
            'total_unpaid' => 'decimal:2',
            'total_change' => 'decimal:2',
            'minimum_down_payment' => 'decimal:2',
        ];
    }

    // Relationships
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'invoice_id', 'id');
    }

    public function customer()
    {
        return $this->author();
    }

    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(
            Voucher::class,
            'invoice_payments',
            'invoice_id',
            'receipt_id'
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
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->total_unpaid > 0;
    }

    public function isFullyPaid(): bool
    {
        return $this->total_unpaid <= 0;
    }

    public function getRemainingAmount(): float
    {
        return max(0, $this->total_unpaid);
    }

    public function getPaymentPercentage(): float
    {
        if ($this->total_invoice <= 0) {
            return 0;
        }

        return round(($this->total_payment / $this->total_invoice) * 100, 2);
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
        return $this->status ?? 'draft';
    }

    public function getTotalAmount(): float
    {
        return $this->total_invoice;
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
        return InvoiceFactory::new();
    }
}
