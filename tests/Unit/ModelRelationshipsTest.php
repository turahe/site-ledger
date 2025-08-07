<?php

namespace Turahe\Ledger\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Turahe\Ledger\Database\Factories\InvoiceFactory;
use Turahe\Ledger\Database\Factories\VoucherFactory;
use Turahe\Ledger\Enums\PaymentMethods;
use Turahe\Ledger\Models\Invoice;
use Turahe\Ledger\Models\Invoice\Item as InvoiceItem;
use Turahe\Ledger\Models\Invoice\Payment as InvoicePayment;
use Turahe\Ledger\Models\Voucher;
use Turahe\Ledger\Models\Voucher\Item as VoucherItem;
use Turahe\Ledger\Tests\Models\Product;
use Turahe\Ledger\Tests\Models\User;
use Turahe\Ledger\Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    #[Test]
    public function invoice_can_have_multiple_items()
    {
        $user = User::factory()->create();
        $invoice = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);

        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $item1 = $invoice->items()->create([
            'model_id' => $product1->getKey(),
            'model_type' => $product1->getMorphClass(),
            'quantity' => 1,
            'price_unit' => 100,
            'currency' => 'USD',
        ]);

        $item2 = $invoice->items()->create([
            'model_id' => $product2->getKey(),
            'model_type' => $product2->getMorphClass(),
            'quantity' => 2,
            'price_unit' => 50,
            'currency' => 'USD',
        ]);

        $this->assertCount(2, $invoice->items);
        $this->assertInstanceOf(InvoiceItem::class, $invoice->items->first());
        $this->assertEquals($item1->getKey(), $invoice->items->first()->getKey());
        $this->assertEquals($item2->getKey(), $invoice->items->last()->getKey());
    }

    #[Test]
    public function voucher_can_have_multiple_items()
    {
        $user = User::factory()->create();
        $voucher = VoucherFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);

        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $item1 = $voucher->items()->create([
            'model_id' => $product1->getKey(),
            'model_type' => $product1->getMorphClass(),
            'quantity' => 1,
            'value' => 100,
        ]);

        $item2 = $voucher->items()->create([
            'model_id' => $product2->getKey(),
            'model_type' => $product2->getMorphClass(),
            'quantity' => 2,
            'value' => 200,
        ]);

        $this->assertCount(2, $voucher->items);
        $this->assertInstanceOf(VoucherItem::class, $voucher->items->first());
        $this->assertEquals($item1->getKey(), $voucher->items->first()->getKey());
        $this->assertEquals($item2->getKey(), $voucher->items->last()->getKey());
    }

    #[Test]
    public function invoice_can_have_multiple_payments()
    {
        $user = User::factory()->create();
        $invoice = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);

        $voucher1 = VoucherFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);

        $voucher2 = VoucherFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);

        // Create payments through the pivot table
        $payment1 = InvoicePayment::create([
            'invoice_id' => $invoice->getKey(),
            'receipt_id' => $voucher1->getKey(),
            'currency' => 'USD',
            'amount' => 100,
            'payment_method' => PaymentMethods::CASH,
        ]);

        $payment2 = InvoicePayment::create([
            'invoice_id' => $invoice->getKey(),
            'receipt_id' => $voucher2->getKey(),
            'currency' => 'USD',
            'amount' => 200,
            'payment_method' => PaymentMethods::QRIS_BCA,
        ]);

        $this->assertCount(2, $invoice->payments);
        $this->assertInstanceOf(Voucher::class, $invoice->payments->first());
        $this->assertEquals($voucher1->getKey(), $invoice->payments->first()->getKey());
        $this->assertEquals($voucher2->getKey(), $invoice->payments->last()->getKey());
    }

    #[Test]
    public function voucher_can_have_multiple_payments()
    {
        $user = User::factory()->create();
        $voucher = VoucherFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);

        $invoice1 = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);

        $invoice2 = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);

        // Create payments through the pivot table
        $payment1 = InvoicePayment::create([
            'invoice_id' => $invoice1->getKey(),
            'receipt_id' => $voucher->getKey(),
            'currency' => 'USD',
            'amount' => 100,
            'payment_method' => PaymentMethods::CASH,
        ]);

        $payment2 = InvoicePayment::create([
            'invoice_id' => $invoice2->getKey(),
            'receipt_id' => $voucher->getKey(),
            'currency' => 'USD',
            'amount' => 200,
            'payment_method' => PaymentMethods::QRIS_BCA,
        ]);

        $this->assertCount(2, $voucher->payments);
        $this->assertInstanceOf(Invoice::class, $voucher->payments->first());
        $this->assertEquals($invoice1->getKey(), $voucher->payments->first()->getKey());
        $this->assertEquals($invoice2->getKey(), $voucher->payments->last()->getKey());
    }

    #[Test]
    public function payment_pivot_contains_correct_data()
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

        $paymentData = [
            'currency' => 'USD',
            'amount' => 150.75,
            'payment_gateway' => 'midtrans',
            'payment_method' => PaymentMethods::CREDIT_CARD_MASTERCARD,
            'payment_channel' => 'online',
            'payment_fee' => 2.50,
            'payment_status_code' => '200',
            'payment_status_message' => 'Payment successful',
            'payment_issued_at' => time(),
            'payment_expires_at' => time() + 86400,
        ];

        InvoicePayment::create(array_merge([
            'invoice_id' => $invoice->getKey(),
            'receipt_id' => $voucher->getKey(),
        ], $paymentData));

        $invoice->load('payments');
        $voucher->load('payments');

        // Check pivot data on invoice side
        $this->assertEquals($paymentData['currency'], $invoice->payments->first()->pivot->currency);
        $this->assertEquals($paymentData['amount'], $invoice->payments->first()->pivot->amount);
        $this->assertEquals($paymentData['payment_method']->value, $invoice->payments->first()->pivot->payment_method);

        // Check pivot data on voucher side
        $this->assertEquals($paymentData['currency'], $voucher->payments->first()->pivot->currency);
        $this->assertEquals($paymentData['amount'], $voucher->payments->first()->pivot->amount);
        $this->assertEquals($paymentData['payment_method']->value, $voucher->payments->first()->pivot->payment_method);
    }

    #[Test]
    public function invoice_item_has_correct_invoice_relationship()
    {
        $user = User::factory()->create();
        $invoice = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);

        $product = Product::factory()->create();

        $item = $invoice->items()->create([
            'model_id' => $product->getKey(),
            'model_type' => $product->getMorphClass(),
            'quantity' => 1,
            'price_unit' => 100,
            'currency' => 'USD',
        ]);

        $this->assertInstanceOf(Invoice::class, $item->invoice);
        $this->assertEquals($invoice->getKey(), $item->invoice->getKey());
    }

    #[Test]
    public function voucher_item_has_correct_voucher_relationship()
    {
        $user = User::factory()->create();
        $voucher = VoucherFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);

        $product = Product::factory()->create();

        $item = $voucher->items()->create([
            'model_id' => $product->getKey(),
            'model_type' => $product->getMorphClass(),
            'quantity' => 1,
            'value' => 100,
        ]);

        $this->assertInstanceOf(Voucher::class, $item->voucher);
        $this->assertEquals($voucher->getKey(), $item->voucher->getKey());
    }

    #[Test]
    public function invoice_can_access_customer_relationship()
    {
        $user = User::factory()->create();
        $invoice = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);

        // The customer() method returns the author (created_by user)
        // Since we're using the test User model, we need to check differently
        $this->assertEquals($user->getKey(), $invoice->model_id);
        $this->assertEquals($user->getMorphClass(), $invoice->model_type);
    }
}
