<?php

namespace Turahe\Ledger\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Turahe\Ledger\Database\Factories\InvoiceFactory;
use Turahe\Ledger\Database\Factories\VoucherFactory;
use Turahe\Ledger\Enums\PaymentMethods;
use Turahe\Ledger\Models\Invoice\Payment;
use Turahe\Ledger\Tests\Models\User;
use Turahe\Ledger\Tests\TestCase;

class InvoicePaymentModelTest extends TestCase
{
    #[Test]
    public function it_can_create_invoice_payment()
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

        $data = [
            'invoice_id' => $invoice->getKey(),
            'receipt_id' => $voucher->getKey(),
            'currency' => 'USD',
            'amount' => 100.50,
            'payment_gateway' => 'midtrans',
            'payment_method' => PaymentMethods::CREDIT_CARD_MASTERCARD,
            'payment_channel' => 'online',
            'payment_fee' => 2.50,
            'payment_status_code' => '200',
            'payment_status_message' => 'Payment successful',
            'payment_issued_at' => time(),
            'payment_expires_at' => time() + 86400, // 24 hours
        ];

        $payment = Payment::create($data);

        // Remove datetime fields from assertion since they're cast to Carbon
        $expectedData = array_diff_key($data, array_flip(['payment_issued_at', 'payment_expires_at']));
        $this->assertDatabaseHas('invoice_payments', $expectedData);
        $this->assertEquals($data['amount'], $payment->amount);
        $this->assertEquals($data['currency'], $payment->currency);
        $this->assertEquals($data['payment_method'], $payment->payment_method);
    }

    #[Test]
    public function it_can_find_invoice_payment()
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

        $payment = Payment::create([
            'invoice_id' => $invoice->getKey(),
            'receipt_id' => $voucher->getKey(),
            'currency' => 'USD',
            'amount' => 100,
            'payment_method' => PaymentMethods::CASH,
        ]);

        $found = Payment::find($payment->getKey());

        $this->assertInstanceOf(Payment::class, $found);
        $this->assertEquals($payment->amount, $found->amount);
        $this->assertEquals($payment->currency, $found->currency);
    }

    #[Test]
    public function it_can_update_invoice_payment()
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

        $payment = Payment::create([
            'invoice_id' => $invoice->getKey(),
            'receipt_id' => $voucher->getKey(),
            'currency' => 'USD',
            'amount' => 100,
            'payment_method' => PaymentMethods::CASH,
        ]);

        $updated = $payment->update([
            'amount' => 150.75,
            'payment_method' => PaymentMethods::E_WALLET_GOPAY,
        ]);

        $this->assertTrue($updated);
        $this->assertEquals(150.75, $payment->fresh()->amount);
        $this->assertEquals(PaymentMethods::E_WALLET_GOPAY, $payment->fresh()->payment_method);
    }

    #[Test]
    public function it_can_delete_invoice_payment()
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

        $payment = Payment::create([
            'invoice_id' => $invoice->getKey(),
            'receipt_id' => $voucher->getKey(),
            'currency' => 'USD',
            'amount' => 100,
            'payment_method' => PaymentMethods::CASH,
        ]);

        $paymentId = $payment->getKey();
        $deleted = $payment->delete();

        // Check if the record was actually deleted
        $this->assertDatabaseMissing('invoice_payments', ['id' => $paymentId]);
    }

    #[Test]
    public function it_casts_payment_method_to_enum()
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

        $payment = Payment::create([
            'invoice_id' => $invoice->getKey(),
            'receipt_id' => $voucher->getKey(),
            'currency' => 'USD',
            'amount' => 100,
            'payment_method' => PaymentMethods::QRIS_BCA,
        ]);

        $this->assertInstanceOf(PaymentMethods::class, $payment->payment_method);
        $this->assertEquals(PaymentMethods::QRIS_BCA, $payment->payment_method);
    }

    #[Test]
    public function it_casts_datetime_fields()
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

        $issuedAt = time();
        $expiresAt = time() + 86400; // 24 hours

        $payment = Payment::create([
            'invoice_id' => $invoice->getKey(),
            'receipt_id' => $voucher->getKey(),
            'currency' => 'USD',
            'amount' => 100,
            'payment_method' => PaymentMethods::CASH,
            'payment_issued_at' => $issuedAt,
            'payment_expires_at' => $expiresAt,
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $payment->payment_issued_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $payment->payment_expires_at);
        $this->assertEquals($issuedAt, $payment->payment_issued_at->timestamp);
        $this->assertEquals($expiresAt, $payment->payment_expires_at->timestamp);
    }

    #[Test]
    public function it_uses_ulid_as_primary_key()
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

        $payment = Payment::create([
            'invoice_id' => $invoice->getKey(),
            'receipt_id' => $voucher->getKey(),
            'currency' => 'USD',
            'amount' => 100,
            'payment_method' => PaymentMethods::CASH,
        ]);

        $this->assertIsString($payment->getKey());
        $this->assertEquals(26, strlen($payment->getKey())); // ULID length
    }

    #[Test]
    public function it_can_handle_different_payment_methods()
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

        $paymentMethods = [
            PaymentMethods::CASH,
            PaymentMethods::QRIS_BCA,
            PaymentMethods::CREDIT_CARD_MASTERCARD,
            PaymentMethods::E_WALLET_GOPAY,
            PaymentMethods::PAYPAL,
        ];

        foreach ($paymentMethods as $method) {
            $payment = Payment::create([
                'invoice_id' => $invoice->getKey(),
                'receipt_id' => $voucher->getKey(),
                'currency' => 'USD',
                'amount' => 100,
                'payment_method' => $method,
            ]);

            $this->assertEquals($method, $payment->payment_method);
        }
    }
}
