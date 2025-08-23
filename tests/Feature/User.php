<?php

namespace Turahe\Ledger\Tests\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Turahe\Ledger\Models\Voucher;
use Turahe\Ledger\Tests\Factories\UserFactory;

class User extends \Illuminate\Foundation\Auth\User
{
    use HasFactory;
    use HasUlids;

    protected $table = 'users';

    /**
     * Create a new factory instance for the model.
     *
     * @return UserFactory
     */
    protected static function newFactory()
    {
        return new UserFactory;
    }

    public function vouchers()
    {
        return $this->morphMany(Voucher::class, 'model');
    }
}
