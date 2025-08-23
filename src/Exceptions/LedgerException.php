<?php

namespace Turahe\Ledger\Exceptions;

use Exception;

/**
 * Ledger Exception Class
 *
 * Custom exception class for handling ledger-specific errors and business logic violations.
 * Provides factory methods for creating specific types of exceptions with contextual error messages.
 *
 * This exception class helps provide clear, actionable error messages for common ledger operations
 * such as payment processing, voucher validation, and status transitions.
 *
 * @author  Nur Wachid <wachid@outlook.com>
 *
 * @since   1.0.0
 */
class LedgerException extends Exception
{
    /**
     * Create a new ledger exception instance.
     *
     * @param  string  $message  The exception message
     * @param  int  $code  The exception code
     * @param  Exception|null  $previous  The previous exception for chaining
     */
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create an exception for invalid payment amount.
     *
     * Used when a payment amount exceeds the allowed maximum or violates business rules.
     *
     * @param  float  $amount  The attempted payment amount
     * @param  float  $maxAmount  The maximum allowed amount
     * @return self The exception instance
     *
     * @example
     * ```php
     * throw LedgerException::invalidPaymentAmount(1500.00, 1000.00);
     * ```
     */
    public static function invalidPaymentAmount(float $amount, float $maxAmount): self
    {
        return new self(
            "Payment amount {$amount} exceeds maximum allowed amount {$maxAmount}",
            1001
        );
    }

    /**
     * Create an exception for insufficient funds.
     *
     * Used when there are not enough funds available to complete a transaction.
     *
     * @param  float  $required  The required amount for the transaction
     * @param  float  $available  The available amount in the account
     * @return self The exception instance
     *
     * @example
     * ```php
     * throw LedgerException::insufficientFunds(500.00, 250.00);
     * ```
     */
    public static function insufficientFunds(float $required, float $available): self
    {
        return new self(
            "Insufficient funds. Required: {$required}, Available: {$available}",
            1002
        );
    }

    /**
     * Create an exception for expired voucher.
     *
     * Used when attempting to use a voucher that has passed its expiration date.
     *
     * @param  string  $voucherCode  The code of the expired voucher
     * @return self The exception instance
     *
     * @example
     * ```php
     * throw LedgerException::expiredVoucher('VOUCHER-001');
     * ```
     */
    public static function expiredVoucher(string $voucherCode): self
    {
        return new self(
            "Voucher {$voucherCode} has expired and cannot be used",
            1003
        );
    }

    /**
     * Create an exception for invalid status transition.
     *
     * Used when attempting to change a status in a way that violates business rules.
     *
     * @param  string  $currentStatus  The current status
     * @param  string  $newStatus  The attempted new status
     * @return self The exception instance
     *
     * @example
     * ```php
     * throw LedgerException::invalidStatusTransition('completed', 'pending');
     * ```
     */
    public static function invalidStatusTransition(string $currentStatus, string $newStatus): self
    {
        return new self(
            "Cannot transition from status '{$currentStatus}' to '{$newStatus}'",
            1004
        );
    }

    /**
     * Create an exception for duplicate code.
     *
     * Used when attempting to create a record with a code that already exists.
     *
     * @param  string  $code  The duplicate code
     * @param  string  $type  The type of record (invoice, voucher, etc.)
     * @return self The exception instance
     *
     * @example
     * ```php
     * throw LedgerException::duplicateCode('INV-001', 'invoice');
     * ```
     */
    public static function duplicateCode(string $code, string $type): self
    {
        return new self(
            "A {$type} with code '{$code}' already exists",
            1005
        );
    }

    /**
     * Create an exception for invalid model relationship.
     *
     * Used when a model relationship is missing or invalid.
     *
     * @param  string  $model  The model name
     * @param  string  $relationship  The relationship name
     * @return self The exception instance
     */
    public static function invalidRelationship(string $model, string $relationship): self
    {
        return new self(
            "Invalid relationship '{$relationship}' for model '{$model}'",
            1006
        );
    }

    /**
     * Create an exception for calculation errors.
     *
     * Used when financial calculations result in invalid values.
     *
     * @param  string  $operation  The calculation operation that failed
     * @param  string  $reason  The reason for the failure
     * @return self The exception instance
     */
    public static function calculationError(string $operation, string $reason): self
    {
        return new self(
            "Calculation error in '{$operation}': {$reason}",
            1007
        );
    }

    /**
     * Create an exception for currency mismatch.
     *
     * Used when attempting operations with incompatible currencies.
     *
     * @param  string  $expected  The expected currency
     * @param  string  $actual  The actual currency
     * @return self The exception instance
     */
    public static function currencyMismatch(string $expected, string $actual): self
    {
        return new self(
            "Currency mismatch: expected '{$expected}', but got '{$actual}'",
            1008
        );
    }
}
