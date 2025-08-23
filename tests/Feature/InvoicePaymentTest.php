<?php

namespace Turahe\Ledger\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Turahe\Ledger\Database\Factories\InvoiceFactory;
use Turahe\Ledger\Database\Factories\VoucherFactory;
use Turahe\Ledger\Models\Invoice\Payment as InvoicePayment;
use Turahe\Ledger\Tests\Models\User;
use Turahe\Ledger\Tests\TestCase;

class InvoicePaymentTest extends TestCase
{
    #[Test]
    public function it_can_create_the_invoice()
    {
        $user = User::factory()->create();
        $invoice = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);
        $voucher = VoucherFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);

        InvoicePayment::create($data = [
            'invoice_id' => $invoice->getKey(),
            'receipt_id' => $voucher->getKey(),
            'currency' => 'USD',
            'amount' => 100,

        ]);

        $this->assertDatabaseHas('invoice_payments', $data);
    }
}
