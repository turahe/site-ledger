<?php

namespace Turahe\Ledger\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Turahe\Ledger\Services\LedgerService;

class LedgerServiceTest extends TestCase
{
    private LedgerService $ledgerService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ledgerService = new LedgerService;
    }

    public function test_ledger_service_can_be_instantiated(): void
    {
        $this->assertInstanceOf(LedgerService::class, $this->ledgerService);
    }

    public function test_ledger_service_has_required_methods(): void
    {
        $this->assertTrue(method_exists($this->ledgerService, 'createInvoice'));
        $this->assertTrue(method_exists($this->ledgerService, 'createVoucher'));
        $this->assertTrue(method_exists($this->ledgerService, 'processPayment'));
        $this->assertTrue(method_exists($this->ledgerService, 'updateInvoiceTotals'));
        $this->assertTrue(method_exists($this->ledgerService, 'getOverdueInvoices'));
        $this->assertTrue(method_exists($this->ledgerService, 'getInvoicesByStatus'));
        $this->assertTrue(method_exists($this->ledgerService, 'getVouchersByStatus'));
        $this->assertTrue(method_exists($this->ledgerService, 'getLedgerSummary'));
        $this->assertTrue(method_exists($this->ledgerService, 'cancelInvoice'));
        $this->assertTrue(method_exists($this->ledgerService, 'cancelVoucher'));
    }
}
