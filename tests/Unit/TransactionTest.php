<?php

namespace Turahe\Ledger\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Turahe\Ledger\Enums\Transaction;

/**
 * Test class for Transaction enum
 */
class TransactionTest extends TestCase
{
    public function test_enum_has_expected_cases(): void
    {
        $this->assertTrue(defined('Turahe\Ledger\Enums\Transaction::Deposit'));
        $this->assertTrue(defined('Turahe\Ledger\Enums\Transaction::Withdraw'));
    }

    public function test_deposit_case_value(): void
    {
        $this->assertEquals('DEPOSIT', Transaction::Deposit->value);
    }

    public function test_withdraw_case_value(): void
    {
        $this->assertEquals('WITHDRAW', Transaction::Withdraw->value);
    }

    public function test_get_description_for_deposit(): void
    {
        $this->assertEquals(
            'Money received or deposited into the account',
            Transaction::Deposit->getDescription()
        );
    }

    public function test_get_description_for_withdraw(): void
    {
        $this->assertEquals(
            'Money withdrawn or paid out from the account',
            Transaction::Withdraw->getDescription()
        );
    }

    public function test_increases_balance_for_deposit(): void
    {
        $this->assertTrue(Transaction::Deposit->increasesBalance());
    }

    public function test_increases_balance_for_withdraw(): void
    {
        $this->assertFalse(Transaction::Withdraw->increasesBalance());
    }

    public function test_get_opposite_for_deposit(): void
    {
        $this->assertEquals(Transaction::Withdraw, Transaction::Deposit->getOpposite());
    }

    public function test_get_opposite_for_withdraw(): void
    {
        $this->assertEquals(Transaction::Deposit, Transaction::Withdraw->getOpposite());
    }

    public function test_opposite_relationship_is_consistent(): void
    {
        $deposit = Transaction::Deposit;
        $withdraw = Transaction::Withdraw;

        $this->assertEquals($withdraw, $deposit->getOpposite());
        $this->assertEquals($deposit, $withdraw->getOpposite());
    }

    public function test_enum_values_are_unique(): void
    {
        $values = [
            Transaction::Deposit->value,
            Transaction::Withdraw->value,
        ];

        $this->assertCount(2, array_unique($values));
        $this->assertCount(2, $values);
    }

    public function test_enum_cases_are_immutable(): void
    {
        $deposit = Transaction::Deposit;
        $withdraw = Transaction::Withdraw;

        $this->assertNotSame($deposit, $withdraw);
        $this->assertNotEquals($deposit, $withdraw);
    }
}
