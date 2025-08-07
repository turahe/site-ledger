<?php

namespace Turahe\Ledger\Enums;

enum Transaction: string
{
    case Deposit = 'DEPOSIT';
    case Withdraw = 'WITHDRAW';

    /**
     * Get all transaction types as an array of values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if the transaction is a deposit
     */
    public function isDeposit(): bool
    {
        return $this === self::Deposit;
    }

    /**
     * Check if the transaction is a withdrawal
     */
    public function isWithdraw(): bool
    {
        return $this === self::Withdraw;
    }
}
