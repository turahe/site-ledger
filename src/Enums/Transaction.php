<?php

namespace Turahe\Ledger\Enums;

enum Transaction: string
{
    case Deposit = 'DEPOSIT';
    case Withdraw = 'WITHDRAW';
}
