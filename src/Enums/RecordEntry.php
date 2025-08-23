<?php

namespace Turahe\Ledger\Enums;

/**
 * Record Entry Type Enum
 *
 * Defines the types of ledger entries for double-entry bookkeeping and cash flow tracking.
 * Used to maintain accurate financial records and balance calculations.
 *
 * @package Turahe\Ledger\Enums
 * @author  Nur Wachid <wachid@outlook.com>
 * @since   1.0.0
 */
enum RecordEntry: string
{
    /**
     * Credit entry - increases liability, equity, or revenue accounts
     *
     * In double-entry bookkeeping, credits:
     * - Increase liability accounts (loans, accounts payable)
     * - Increase equity accounts (owner's equity, retained earnings)
     * - Increase revenue accounts (sales, service income)
     * - Decrease asset accounts (cash, inventory)
     * - Decrease expense accounts (returns, allowances)
     */
    case Credit = 'CREDIT';

    /**
     * Debit entry - increases asset or expense accounts
     *
     * In double-entry bookkeeping, debits:
     * - Increase asset accounts (cash, accounts receivable, inventory)
     * - Increase expense accounts (salaries, rent, utilities)
     * - Decrease liability accounts (loan payments)
     * - Decrease equity accounts (owner withdrawals)
     * - Decrease revenue accounts (sales returns)
     */
    case Debit = 'DEBIT';

    /**
     * Inflow entry - money or assets coming into the business
     *
     * Used for cash flow tracking:
     * - Customer payments received
     * - Investment income
     * - Loan proceeds
     * - Asset sales
     */
    case In = 'IN';

    /**
     * Outflow entry - money or assets going out of the business
     *
     * Used for cash flow tracking:
     * - Payments to suppliers
     * - Employee salaries
     * - Loan repayments
     * - Asset purchases
     */
    case Out = 'OUT';

    /**
     * Get a human-readable description of the record entry type
     *
     * @return string The record entry type description
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::Credit => 'Credit entry - increases liabilities, equity, or revenue',
            self::Debit => 'Debit entry - increases assets or expenses',
            self::In => 'Inflow - money or assets coming into the business',
            self::Out => 'Outflow - money or assets going out of the business',
        };
    }

    /**
     * Check if this is a double-entry bookkeeping type
     *
     * @return bool True if Credit or Debit, false for In/Out
     */
    public function isDoubleEntryType(): bool
    {
        return in_array($this, [self::Credit, self::Debit]);
    }

    /**
     * Check if this is a cash flow type
     *
     * @return bool True if In or Out, false for Credit/Debit
     */
    public function isCashFlowType(): bool
    {
        return in_array($this, [self::In, self::Out]);
    }

    /**
     * Get the opposite entry type for double-entry bookkeeping
     *
     * @return self|null The opposite entry type or null if not applicable
     */
    public function getOpposite(): ?self
    {
        return match ($this) {
            self::Credit => self::Debit,
            self::Debit => self::Credit,
            self::In => self::Out,
            self::Out => self::In,
        };
    }

    /**
     * Check if this entry type increases the account balance
     *
     * @param string $accountType The account type ('asset', 'liability', 'equity', 'revenue', 'expense')
     * @return bool True if the entry increases the account balance
     */
    public function increasesBalance(string $accountType): bool
    {
        return match ($this) {
            self::Debit => in_array($accountType, ['asset', 'expense']),
            self::Credit => in_array($accountType, ['liability', 'equity', 'revenue']),
            self::In => true,
            self::Out => false,
        };
    }
}
