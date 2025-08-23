<?php

namespace Turahe\Ledger\Tests\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Turahe\Ledger\Tests\Factories\ProductFactory;

class Product extends Model
{
    use HasFactory;
    use HasUlids;

    protected $table = 'products';

    protected static function newFactory()
    {
        return ProductFactory::new();
    }
}
