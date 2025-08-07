<?php

namespace Turahe\Ledger\Enums;

enum RecordEntry: string
{
    case Credit = 'CREDIT';
    case Debit = 'DEBIT';
    case In = 'IN';
    case Out = 'OUT';

    /**
     * Get all record entries as an array of values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if the entry is a credit type
     */
    public function isCredit(): bool
    {
        return $this === self::Credit;
    }

    /**
     * Check if the entry is a debit type
     */
    public function isDebit(): bool
    {
        return $this === self::Debit;
    }

    /**
     * Check if the entry is an incoming type
     */
    public function isIncoming(): bool
    {
        return $this === self::In;
    }

    /**
     * Check if the entry is an outgoing type
     */
    public function isOutgoing(): bool
    {
        return $this === self::Out;
    }
}
