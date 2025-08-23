<?php

namespace Turahe\Ledger\Exceptions;

use Exception;

class LedgerException extends Exception
{
    /**
     * Create a new ledger exception instance.
     */
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create an exception for invalid payment amount.
     */
    public static function invalidPaymentAmount(float $amount, float $maxAmount): self
    {
        return new self(
            "Payment amount {$amount} exceeds maximum allowed amount {$maxAmount}"
        );
    }

    /**
     * Create an exception for insufficient funds.
     */
    public static function insufficientFunds(float $required, float $available): self
    {
        return new self(
            "Insufficient funds. Required: {$required}, Available: {$available}"
        );
    }

    /**
     * Create an exception for expired voucher.
     */
    public static function expiredVoucher(string $voucherCode): self
    {
        return new self(
            "Voucher {$voucherCode} has expired and cannot be used"
        );
    }

    /**
     * Create an exception for invalid status transition.
     */
    public static function invalidStatusTransition(string $currentStatus, string $newStatus): self
    {
        return new self(
            "Cannot transition from status '{$currentStatus}' to '{$newStatus}'"
        );
    }

    /**
     * Create an exception for duplicate code.
     */
    public static function duplicateCode(string $code, string $type): self
    {
        return new self(
            "A {$type} with code '{$code}' already exists"
        );
    }
}
