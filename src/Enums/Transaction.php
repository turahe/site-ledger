<?php

namespace Turahe\Ledger\Enums;

/**
 * Transaction Type Enum
 *
 * Defines the types of financial transactions that can be recorded in the ledger system.
 * Used for categorizing money movements and financial operations.
 *
 * @package Turahe\Ledger\Enums
 * @author  Nur Wachid <wachid@outlook.com>
 * @since   1.0.0
 */
enum Transaction: string
{
    /**
     * Deposit transaction - money coming into the account
     *
     * Represents an inflow of funds, such as:
     * - Customer payments
     * - Revenue deposits
     * - Refunds received
     * - Interest earned
     */
    case Deposit = 'DEPOSIT';

    /**
     * Withdrawal transaction - money going out of the account
     *
     * Represents an outflow of funds, such as:
     * - Expense payments
     * - Refunds issued
     * - Fee deductions
     * - Transfer to other accounts
     */
    case Withdraw = 'WITHDRAW';

    /**
     * Get a human-readable description of the transaction type
     *
     * @return string The transaction type description
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::Deposit => 'Money received or deposited into the account',
            self::Withdraw => 'Money withdrawn or paid out from the account',
        };
    }

    /**
     * Check if the transaction increases the account balance
     *
     * @return bool True if transaction increases balance, false otherwise
     */
    public function increasesBalance(): bool
    {
        return $this === self::Deposit;
    }

    /**
     * Get the opposite transaction type
     *
     * @return self The opposite transaction type
     */
    public function getOpposite(): self
    {
        return match ($this) {
            self::Deposit => self::Withdraw,
            self::Withdraw => self::Deposit,
        };
    }
}
