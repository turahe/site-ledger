<?php

namespace Turahe\Ledger\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Turahe\Ledger\Database\Factories\VoucherFactory;
use Turahe\Ledger\Models\Voucher;
use Turahe\Ledger\Models\Voucher\Item;
use Turahe\Ledger\Tests\Models\Product;
use Turahe\Ledger\Tests\Models\User;
use Turahe\Ledger\Tests\TestCase;

class VoucherItemTest extends TestCase
{
    #[Test]
    public function it_can_create_voucher_item()
    {
        $user = User::factory()->create();
        $voucher = VoucherFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);
        $product = Product::factory()->create();

        $data = [
            'voucher_id' => $voucher->getKey(),
            'model_id' => $product->getKey(),
            'model_type' => $product->getMorphClass(),
            'quantity' => 2,
            'value' => 100.50,
        ];

        $item = Item::create($data);

        $this->assertDatabaseHas('voucher_items', $data);
        $this->assertEquals($voucher->getKey(), $item->voucher_id);
        $this->assertEquals($product->getKey(), $item->model_id);
        $this->assertEquals($product->getMorphClass(), $item->model_type);
        $this->assertEquals($data['quantity'], $item->quantity);
        $this->assertEquals($data['value'], $item->value);
    }

    #[Test]
    public function it_can_find_voucher_item()
    {
        $user = User::factory()->create();
        $voucher = VoucherFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);
        $product = Product::factory()->create();

        $item = Item::create([
            'voucher_id' => $voucher->getKey(),
            'model_id' => $product->getKey(),
            'model_type' => $product->getMorphClass(),
            'quantity' => 1,
            'value' => 100,
        ]);

        $found = Item::find($item->getKey());

        $this->assertInstanceOf(Item::class, $found);
        $this->assertEquals($item->voucher_id, $found->voucher_id);
        $this->assertEquals($item->model_id, $found->model_id);
    }

    #[Test]
    public function it_can_delete_voucher_item()
    {
        $user = User::factory()->create();
        $voucher = VoucherFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);
        $product = Product::factory()->create();

        $item = Item::create([
            'voucher_id' => $voucher->getKey(),
            'model_id' => $product->getKey(),
            'model_type' => $product->getMorphClass(),
            'quantity' => 1,
            'value' => 100,
        ]);

        $deleted = $item->delete();

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('voucher_items', ['id' => $item->getKey()]);
    }

    #[Test]
    public function it_has_voucher_relationship()
    {
        $user = User::factory()->create();
        $voucher = VoucherFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);
        $product = Product::factory()->create();

        $item = Item::create([
            'voucher_id' => $voucher->getKey(),
            'model_id' => $product->getKey(),
            'model_type' => $product->getMorphClass(),
            'quantity' => 1,
            'value' => 100,
        ]);

        $this->assertInstanceOf(Voucher::class, $item->voucher);
        $this->assertEquals($voucher->getKey(), $item->voucher->getKey());
    }

    #[Test]
    public function it_can_create_multiple_items_for_voucher()
    {
        $user = User::factory()->create();
        $voucher = VoucherFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $item1 = Item::create([
            'voucher_id' => $voucher->getKey(),
            'model_id' => $product1->getKey(),
            'model_type' => $product1->getMorphClass(),
            'quantity' => 1,
            'value' => 100,
        ]);

        $item2 = Item::create([
            'voucher_id' => $voucher->getKey(),
            'model_id' => $product2->getKey(),
            'model_type' => $product2->getMorphClass(),
            'quantity' => 2,
            'value' => 200,
        ]);

        $this->assertDatabaseHas('voucher_items', ['id' => $item1->getKey()]);
        $this->assertDatabaseHas('voucher_items', ['id' => $item2->getKey()]);
        $this->assertEquals($voucher->getKey(), $item1->voucher_id);
        $this->assertEquals($voucher->getKey(), $item2->voucher_id);
    }

    #[Test]
    public function it_uses_ulid_as_primary_key()
    {
        $user = User::factory()->create();
        $voucher = VoucherFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);
        $product = Product::factory()->create();

        $item = Item::create([
            'voucher_id' => $voucher->getKey(),
            'model_id' => $product->getKey(),
            'model_type' => $product->getMorphClass(),
            'quantity' => 1,
            'value' => 100,
        ]);

        $this->assertIsString($item->getKey());
        $this->assertEquals(26, strlen($item->getKey())); // ULID length
    }
}
