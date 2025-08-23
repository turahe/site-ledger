<?php

namespace Turahe\Ledger\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Turahe\Ledger\Exceptions\LedgerException;

class LedgerExceptionTest extends TestCase
{
    public function test_ledger_exception_can_be_instantiated(): void
    {
        $exception = new LedgerException('Test error message');
        
        $this->assertInstanceOf(LedgerException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function test_ledger_exception_has_correct_message(): void
    {
        $message = 'Custom error message for ledger';
        $exception = new LedgerException($message);
        
        $this->assertEquals($message, $exception->getMessage());
    }

    public function test_ledger_exception_has_default_code(): void
    {
        $exception = new LedgerException('Test message');
        
        $this->assertEquals(0, $exception->getCode());
    }

    public function test_ledger_exception_has_custom_code(): void
    {
        $code = 500;
        $exception = new LedgerException('Test message', $code);
        
        $this->assertEquals($code, $exception->getCode());
    }

    public function test_ledger_exception_can_be_thrown_and_caught(): void
    {
        $this->expectException(LedgerException::class);
        $this->expectExceptionMessage('Exception should be thrown');
        
        throw new LedgerException('Exception should be thrown');
    }
}
