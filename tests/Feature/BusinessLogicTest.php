<?php

namespace Turahe\Ledger\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Turahe\Ledger\Enums\PaymentMethods;
use Turahe\Ledger\Models\Invoice;
use Turahe\Ledger\Models\Voucher;
use Turahe\Ledger\Tests\TestCase;

class BusinessLogicTest extends TestCase
{
    #[Test]
    public function test_payment_methods_categorization()
    {
        $qrisMethods = PaymentMethods::getByCategory('qris');
        $this->assertNotEmpty($qrisMethods);
        $this->assertContains(PaymentMethods::QRIS_BCA, $qrisMethods);
        $this->assertContains(PaymentMethods::QRIS_BRI, $qrisMethods);

        $eWalletMethods = PaymentMethods::getByCategory('e_wallet');
        $this->assertNotEmpty($eWalletMethods);
        $this->assertContains(PaymentMethods::E_WALLET_GOPAY, $eWalletMethods);
        $this->assertContains(PaymentMethods::E_WALLET_OVO, $eWalletMethods);
    }

    #[Test]
    public function test_payment_method_digital_detection()
    {
        $this->assertTrue(PaymentMethods::QRIS_BCA->isDigital());
        $this->assertTrue(PaymentMethods::E_WALLET_GOPAY->isDigital());
        $this->assertFalse(PaymentMethods::CASH->isDigital());
        $this->assertFalse(PaymentMethods::CHEQUE->isDigital());
    }

    #[Test]
    public function test_payment_method_instant_detection()
    {
        $this->assertTrue(PaymentMethods::CASH->isInstant());
        $this->assertTrue(PaymentMethods::E_WALLET_GOPAY->isInstant());
        $this->assertTrue(PaymentMethods::DEBIT_CARD->isInstant());
        $this->assertFalse(PaymentMethods::CREDIT_CARD_MASTERCARD->isInstant());
    }

    #[Test]
    public function test_invoice_business_logic()
    {
        $invoice = Invoice::create([
            'model_id' => '01HXYZ123456789ABCDEFGHIJ',
            'model_type' => 'Turahe\Ledger\Tests\Models\User',
            'code' => 'TEST-001',
            'total_invoice' => 1000,
            'total_payment' => 600,
            'total_unpaid' => 400,
            'due_date' => now()->addDays(30),
        ]);

        $this->assertFalse($invoice->isOverdue());
        $this->assertFalse($invoice->isFullyPaid());
        $this->assertEquals(400, $invoice->getRemainingAmount());
        $this->assertEquals(60.0, $invoice->getPaymentPercentage());
    }

    #[Test]
    public function test_voucher_business_logic()
    {
        $voucher = Voucher::create([
            'model_id' => '01HXYZ123456789ABCDEFGHIJ',
            'model_type' => 'Turahe\Ledger\Tests\Models\User',
            'code' => 'VOUCHER-001',
            'total_value' => 500,
            'due_date' => now()->addDays(15),
            'status' => 'active',
        ]);

        $this->assertFalse($voucher->isExpired());
        $this->assertTrue($voucher->isActive());
        $this->assertEquals('500.00', $voucher->getTotalValueFormatted());
        $this->assertGreaterThan(0, $voucher->getDaysUntilExpiry());
    }

    #[Test]
    public function test_has_ledger_attributes_trait()
    {
        $invoice = Invoice::create([
            'model_id' => '01HXYZ123456789ABCDEFGHIJ',
            'model_type' => 'Turahe\Ledger\Tests\Models\User',
            'code' => 'TEST-002',
            'total_invoice' => 1000,
        ]);

        // Test scope methods
        $foundInvoice = Invoice::byCode('TEST-002')->first();
        $this->assertNotNull($foundInvoice);
        $this->assertEquals($invoice->id, $foundInvoice->id);

        // Test metadata methods
        $invoice->setMetadataValue('test_key', 'test_value');
        $this->assertEquals('test_value', $invoice->getMetadataValue('test_key'));
        $this->assertEquals('default_value', $invoice->getMetadataValue('non_existent', 'default_value'));
    }
}
