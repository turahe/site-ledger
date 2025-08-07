<?php

namespace Turahe\Ledger\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Turahe\Ledger\Enums\PaymentMethods;
use Turahe\Ledger\Enums\RecordEntry;
use Turahe\Ledger\Enums\Transaction;
use Turahe\Ledger\Tests\TestCase;

class EnumTest extends TestCase
{
    #[Test]
    public function it_can_use_payment_methods_enum()
    {
        $this->assertEquals('CASH', PaymentMethods::CASH->value);
        $this->assertEquals('QRIS_BCA', PaymentMethods::QRIS_BCA->value);
        $this->assertEquals('CREDIT_CARD_MASTERCARD', PaymentMethods::CREDIT_CARD_MASTERCARD->value);
        $this->assertEquals('E_WALLET_GOPAY', PaymentMethods::E_WALLET_GOPAY->value);
        $this->assertEquals('PAYPAL', PaymentMethods::PAYPAL->value);
    }

    #[Test]
    public function it_can_use_record_entry_enum()
    {
        $this->assertEquals('CREDIT', RecordEntry::Credit->value);
        $this->assertEquals('DEBIT', RecordEntry::Debit->value);
        $this->assertEquals('IN', RecordEntry::In->value);
        $this->assertEquals('OUT', RecordEntry::Out->value);
    }

    #[Test]
    public function it_can_use_transaction_enum()
    {
        $this->assertEquals('DEPOSIT', Transaction::Deposit->value);
        $this->assertEquals('WITHDRAW', Transaction::Withdraw->value);
    }

    #[Test]
    public function it_can_get_all_payment_methods()
    {
        $methods = PaymentMethods::cases();
        $this->assertIsArray($methods);
        $this->assertGreaterThan(0, count($methods));

        // Check that we have some expected payment methods
        $methodValues = array_map(fn ($method) => $method->value, $methods);
        $this->assertContains('CASH', $methodValues);
        $this->assertContains('QRIS_BCA', $methodValues);
        $this->assertContains('CREDIT_CARD_MASTERCARD', $methodValues);
    }

    #[Test]
    public function it_can_get_all_record_entries()
    {
        $entries = RecordEntry::cases();
        $this->assertIsArray($entries);
        $this->assertCount(4, $entries);

        $entryValues = array_map(fn ($entry) => $entry->value, $entries);
        $this->assertContains('CREDIT', $entryValues);
        $this->assertContains('DEBIT', $entryValues);
        $this->assertContains('IN', $entryValues);
        $this->assertContains('OUT', $entryValues);
    }

    #[Test]
    public function it_can_get_payment_methods_by_category()
    {
        $cashMethods = PaymentMethods::byCategory('cash');
        $this->assertIsArray($cashMethods);
        $this->assertCount(1, $cashMethods);
        $this->assertEquals(PaymentMethods::CASH, $cashMethods[0]);

        $qrisMethods = PaymentMethods::byCategory('qris');
        $this->assertIsArray($qrisMethods);
        $this->assertGreaterThan(0, count($qrisMethods));
        $this->assertContains(PaymentMethods::QRIS_BCA, $qrisMethods);

        $creditCardMethods = PaymentMethods::byCategory('credit_card');
        $this->assertIsArray($creditCardMethods);
        $this->assertCount(3, $creditCardMethods);
        $this->assertContains(PaymentMethods::CREDIT_CARD_MASTERCARD, $creditCardMethods);
    }

    #[Test]
    public function it_can_check_record_entry_types()
    {
        $credit = RecordEntry::Credit;
        $this->assertTrue($credit->isCredit());
        $this->assertFalse($credit->isDebit());

        $debit = RecordEntry::Debit;
        $this->assertTrue($debit->isDebit());
        $this->assertFalse($debit->isCredit());

        $in = RecordEntry::In;
        $this->assertTrue($in->isIncoming());
        $this->assertFalse($in->isOutgoing());

        $out = RecordEntry::Out;
        $this->assertTrue($out->isOutgoing());
        $this->assertFalse($out->isIncoming());
    }

    #[Test]
    public function it_can_check_transaction_types()
    {
        $deposit = Transaction::Deposit;
        $this->assertTrue($deposit->isDeposit());
        $this->assertFalse($deposit->isWithdraw());

        $withdraw = Transaction::Withdraw;
        $this->assertTrue($withdraw->isWithdraw());
        $this->assertFalse($withdraw->isDeposit());
    }

    #[Test]
    public function it_can_get_enum_values()
    {
        $paymentValues = PaymentMethods::values();
        $this->assertIsArray($paymentValues);
        $this->assertContains('CASH', $paymentValues);
        $this->assertContains('QRIS_BCA', $paymentValues);

        $recordValues = RecordEntry::values();
        $this->assertIsArray($recordValues);
        $this->assertContains('CREDIT', $recordValues);
        $this->assertContains('DEBIT', $recordValues);

        $transactionValues = Transaction::values();
        $this->assertIsArray($transactionValues);
        $this->assertContains('DEPOSIT', $transactionValues);
        $this->assertContains('WITHDRAW', $transactionValues);
    }
}
