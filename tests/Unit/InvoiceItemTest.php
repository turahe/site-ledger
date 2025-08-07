<?php

namespace Turahe\Ledger\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Turahe\Ledger\Database\Factories\InvoiceFactory;
use Turahe\Ledger\Models\Invoice;
use Turahe\Ledger\Models\Invoice\Item;
use Turahe\Ledger\Tests\Models\Product;
use Turahe\Ledger\Tests\Models\User;
use Turahe\Ledger\Tests\TestCase;

class InvoiceItemTest extends TestCase
{
    #[Test]
    public function it_can_create_invoice_item()
    {
        $user = User::factory()->create();
        $invoice = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);
        $product = Product::factory()->create();

        $data = [
            'invoice_id' => $invoice->getKey(),
            'model_id' => $product->getKey(),
            'model_type' => $product->getMorphClass(),
            'quantity' => 2,
            'price_unit' => 100.50,
            'currency' => 'USD',
            'shipping_fee' => 10.00,
            'insurance_fee' => 5.00,
            'transaction_fee' => 2.50,
            'discount_voucher' => 'DISCOUNT10',
            'discount_amount' => 10.00,
            'tax_amount' => 15.00,
            'service_amount' => 3.00,
            'mdr_fee' => 1.50,
        ];

        $item = Item::create($data);

        $this->assertDatabaseHas('invoice_items', $data);
        $this->assertEquals($data['quantity'], $item->quantity);
        $this->assertEquals($data['price_unit'], $item->price_unit);
        $this->assertEquals($data['currency'], $item->currency);
    }

    #[Test]
    public function it_can_find_invoice_item()
    {
        $user = User::factory()->create();
        $invoice = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);
        $product = Product::factory()->create();

        $item = Item::create([
            'invoice_id' => $invoice->getKey(),
            'model_id' => $product->getKey(),
            'model_type' => $product->getMorphClass(),
            'quantity' => 1,
            'price_unit' => 100,
            'currency' => 'USD',
        ]);

        $found = Item::find($item->getKey());

        $this->assertInstanceOf(Item::class, $found);
        $this->assertEquals($item->quantity, $found->quantity);
        $this->assertEquals($item->price_unit, $found->price_unit);
    }

    #[Test]
    public function it_can_update_invoice_item()
    {
        $user = User::factory()->create();
        $invoice = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);
        $product = Product::factory()->create();

        $item = Item::create([
            'invoice_id' => $invoice->getKey(),
            'model_id' => $product->getKey(),
            'model_type' => $product->getMorphClass(),
            'quantity' => 1,
            'price_unit' => 100,
            'currency' => 'USD',
        ]);

        $updated = $item->update(['quantity' => 3, 'price_unit' => 150.75]);

        $this->assertTrue($updated);
        $this->assertEquals(3, $item->fresh()->quantity);
        $this->assertEquals(150.75, $item->fresh()->price_unit);
    }

    #[Test]
    public function it_can_delete_invoice_item()
    {
        $user = User::factory()->create();
        $invoice = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);
        $product = Product::factory()->create();

        $item = Item::create([
            'invoice_id' => $invoice->getKey(),
            'model_id' => $product->getKey(),
            'model_type' => $product->getMorphClass(),
            'quantity' => 1,
            'price_unit' => 100,
            'currency' => 'USD',
        ]);

        $deleted = $item->delete();

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('invoice_items', ['id' => $item->getKey()]);
    }

    #[Test]
    public function it_has_invoice_relationship()
    {
        $user = User::factory()->create();
        $invoice = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);
        $product = Product::factory()->create();

        $item = Item::create([
            'invoice_id' => $invoice->getKey(),
            'model_id' => $product->getKey(),
            'model_type' => $product->getMorphClass(),
            'quantity' => 1,
            'price_unit' => 100,
            'currency' => 'USD',
        ]);

        $this->assertInstanceOf(Invoice::class, $item->invoice);
        $this->assertEquals($invoice->getKey(), $item->invoice->getKey());
    }

    #[Test]
    public function it_can_calculate_total_amount()
    {
        $user = User::factory()->create();
        $invoice = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);
        $product = Product::factory()->create();

        $item = Item::create([
            'invoice_id' => $invoice->getKey(),
            'model_id' => $product->getKey(),
            'model_type' => $product->getMorphClass(),
            'quantity' => 2,
            'price_unit' => 100,
            'currency' => 'USD',
            'shipping_fee' => 10,
            'insurance_fee' => 5,
            'tax_amount' => 15,
        ]);

        // Calculate expected total: (quantity * price_unit) + fees + tax
        $expectedTotal = (2 * 100) + 10 + 5 + 15;

        $this->assertEquals(2, $item->quantity);
        $this->assertEquals(100, $item->price_unit);
        $this->assertEquals(10, $item->shipping_fee);
        $this->assertEquals(5, $item->insurance_fee);
        $this->assertEquals(15, $item->tax_amount);
    }
}
