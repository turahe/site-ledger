<?php

namespace Turahe\Ledger\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Turahe\Ledger\Enums\RecordEntry;

/**
 * Test class for RecordEntry enum
 */
class RecordEntryTest extends TestCase
{
    public function test_enum_has_expected_cases(): void
    {
        $this->assertTrue(defined('Turahe\Ledger\Enums\RecordEntry::Credit'));
        $this->assertTrue(defined('Turahe\Ledger\Enums\RecordEntry::Debit'));
        $this->assertTrue(defined('Turahe\Ledger\Enums\RecordEntry::In'));
        $this->assertTrue(defined('Turahe\Ledger\Enums\RecordEntry::Out'));
    }

    public function test_credit_case_value(): void
    {
        $this->assertEquals('CREDIT', RecordEntry::Credit->value);
    }

    public function test_debit_case_value(): void
    {
        $this->assertEquals('DEBIT', RecordEntry::Debit->value);
    }

    public function test_in_case_value(): void
    {
        $this->assertEquals('IN', RecordEntry::In->value);
    }

    public function test_out_case_value(): void
    {
        $this->assertEquals('OUT', RecordEntry::Out->value);
    }

    public function test_get_description_for_credit(): void
    {
        $this->assertEquals(
            'Credit entry - increases liabilities, equity, or revenue',
            RecordEntry::Credit->getDescription()
        );
    }

    public function test_get_description_for_debit(): void
    {
        $this->assertEquals(
            'Debit entry - increases assets or expenses',
            RecordEntry::Debit->getDescription()
        );
    }

    public function test_get_description_for_in(): void
    {
        $this->assertEquals(
            'Inflow - money or assets coming into the business',
            RecordEntry::In->getDescription()
        );
    }

    public function test_get_description_for_out(): void
    {
        $this->assertEquals(
            'Outflow - money or assets going out of the business',
            RecordEntry::Out->getDescription()
        );
    }

    public function test_is_double_entry_type_for_credit(): void
    {
        $this->assertTrue(RecordEntry::Credit->isDoubleEntryType());
    }

    public function test_is_double_entry_type_for_debit(): void
    {
        $this->assertTrue(RecordEntry::Debit->isDoubleEntryType());
    }

    public function test_is_double_entry_type_for_in(): void
    {
        $this->assertFalse(RecordEntry::In->isDoubleEntryType());
    }

    public function test_is_double_entry_type_for_out(): void
    {
        $this->assertFalse(RecordEntry::Out->isDoubleEntryType());
    }

    public function test_is_cash_flow_type_for_in(): void
    {
        $this->assertTrue(RecordEntry::In->isCashFlowType());
    }

    public function test_is_cash_flow_type_for_out(): void
    {
        $this->assertTrue(RecordEntry::Out->isCashFlowType());
    }

    public function test_is_cash_flow_type_for_credit(): void
    {
        $this->assertFalse(RecordEntry::Credit->isCashFlowType());
    }

    public function test_is_cash_flow_type_for_debit(): void
    {
        $this->assertFalse(RecordEntry::Debit->isCashFlowType());
    }

    public function test_get_opposite_for_credit(): void
    {
        $this->assertEquals(RecordEntry::Debit, RecordEntry::Credit->getOpposite());
    }

    public function test_get_opposite_for_debit(): void
    {
        $this->assertEquals(RecordEntry::Credit, RecordEntry::Debit->getOpposite());
    }

    public function test_get_opposite_for_in(): void
    {
        $this->assertEquals(RecordEntry::Out, RecordEntry::In->getOpposite());
    }

    public function test_get_opposite_for_out(): void
    {
        $this->assertEquals(RecordEntry::In, RecordEntry::Out->getOpposite());
    }

    public function test_increases_balance_for_debit_with_asset(): void
    {
        $this->assertTrue(RecordEntry::Debit->increasesBalance('asset'));
    }

    public function test_increases_balance_for_debit_with_expense(): void
    {
        $this->assertTrue(RecordEntry::Debit->increasesBalance('expense'));
    }

    public function test_increases_balance_for_debit_with_liability(): void
    {
        $this->assertFalse(RecordEntry::Debit->increasesBalance('liability'));
    }

    public function test_increases_balance_for_credit_with_liability(): void
    {
        $this->assertTrue(RecordEntry::Credit->increasesBalance('liability'));
    }

    public function test_increases_balance_for_credit_with_equity(): void
    {
        $this->assertTrue(RecordEntry::Credit->increasesBalance('equity'));
    }

    public function test_increases_balance_for_credit_with_revenue(): void
    {
        $this->assertTrue(RecordEntry::Credit->increasesBalance('revenue'));
    }

    public function test_increases_balance_for_credit_with_asset(): void
    {
        $this->assertFalse(RecordEntry::Credit->increasesBalance('asset'));
    }

    public function test_increases_balance_for_in(): void
    {
        $this->assertTrue(RecordEntry::In->increasesBalance('asset'));
        $this->assertTrue(RecordEntry::In->increasesBalance('liability'));
        $this->assertTrue(RecordEntry::In->increasesBalance('equity'));
        $this->assertTrue(RecordEntry::In->increasesBalance('revenue'));
        $this->assertTrue(RecordEntry::In->increasesBalance('expense'));
    }

    public function test_increases_balance_for_out(): void
    {
        $this->assertFalse(RecordEntry::Out->increasesBalance('asset'));
        $this->assertFalse(RecordEntry::Out->increasesBalance('liability'));
        $this->assertFalse(RecordEntry::Out->increasesBalance('equity'));
        $this->assertFalse(RecordEntry::Out->increasesBalance('revenue'));
        $this->assertFalse(RecordEntry::Out->increasesBalance('expense'));
    }
}
