<?php

namespace Turahe\Ledger\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Turahe\Ledger\Database\Factories\InvoiceFactory;
use Turahe\Ledger\Models\Invoice\Item;
use Turahe\UserStamps\Concerns\HasUserStamps;

class Invoice extends Model
{
    use HasUlids;
    use HasUserStamps;

    protected const string TABLE_NAME = 'invoices';
    protected const string DATE_FORMAT = 'U';

    protected $table = self::TABLE_NAME;
    public $dateFormat = self::DATE_FORMAT;

    protected $fillable = [
        'model_id',
        'model_type',
        'code',
        'shipping_provider_id',
        'shipping_fee',
        'insurance_provider_id',
        'insurance_fee',
        'transaction_fee',
        'discount_voucher',
        'discount_amount',
        'currency',
        'issue_date',
        'due_date',
        'tax_amount',
        'service_amount',
        'mdr_fee',
        'total_amount',
        'total_invoice',
        'total_payment',
        'total_unpaid',
        'total_change',
        'minimum_down_payment',
    ];

    protected $casts = [
        'issue_date' => 'datetime',
        'due_date' => 'datetime',
        'tax_amount' => 'decimal:4',
        'service_amount' => 'decimal:4',
        'mdr_fee' => 'decimal:4',
        'total_amount' => 'decimal:4',
        'total_invoice' => 'decimal:4',
        'total_payment' => 'decimal:4',
        'total_unpaid' => 'decimal:4',
        'total_change' => 'decimal:4',
        'minimum_down_payment' => 'decimal:4',
        'shipping_fee' => 'float',
        'insurance_fee' => 'float',
        'transaction_fee' => 'float',
        'discount_amount' => 'float',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(Voucher::class, 'invoice_payments', 'invoice_id', 'receipt_id')
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

    protected static function newFactory(): InvoiceFactory
    {
        return new InvoiceFactory();
    }
}
