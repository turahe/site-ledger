<?php

namespace Turahe\Ledger\Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Turahe\Ledger\Database\Factories\InvoiceFactory;
use Turahe\Ledger\Models\Invoice;
use Turahe\Ledger\Models\Invoice\Item;
use Turahe\Ledger\Tests\Models\Product;
use Turahe\Ledger\Tests\Models\User;
use Turahe\Ledger\Tests\TestCase;

class InvoiceTest extends TestCase
{
    #[Test]
    public function it_can_create_the_invoice()
    {
        $user = User::factory()->create();
        $data = [
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
            'code' => '1234567890',
            'shipping_provider_id' => '01HXYZ123456789ABCDEFGHIK',
            'shipping_fee' => 10,
            'insurance_provider_id' => '01HXYZ123456789ABCDEFGHIK',
            'insurance_fee' => 10,
            'transaction_fee' => 10,
            'currency' => 'IDR',
            'discount_voucher' => 10,
            'discount_amount' => 10,
            'tax_amount' => 1,
            'service_amount' => 1,
            'mdr_fee' => 1,
            'total_amount' => 120,
            'total_invoice' => 120,
            'total_unpaid' => 100,
            'total_payment' => 20,
            'minimum_down_payment' => 100,
        ];

        $invoice = Invoice::create($data);

        $this->assertDatabaseHas('invoices', $data);
        $this->assertEquals($data['code'], $invoice->code);
        $this->assertEquals($data['shipping_provider_id'], $invoice->shipping_provider_id);
        $this->assertEquals($data['shipping_fee'], $invoice->shipping_fee);
        $this->assertEquals($data['insurance_provider_id'], $invoice->insurance_provider_id);
        $this->assertEquals($data['insurance_fee'], $invoice->insurance_fee);
        $this->assertEquals($data['transaction_fee'], $invoice->transaction_fee);
        $this->assertEquals($data['currency'], $invoice->currency);
        $this->assertEquals($data['discount_voucher'], $invoice->discount_voucher);
        $this->assertEquals($data['discount_amount'], $invoice->discount_amount);
        $this->assertEquals($data['tax_amount'], $invoice->tax_amount);
        $this->assertEquals($data['service_amount'], $invoice->service_amount);
        $this->assertEquals($data['mdr_fee'], $invoice->mdr_fee);
        $this->assertEquals($data['total_amount'], $invoice->total_amount);
        $this->assertEquals($data['total_invoice'], $invoice->total_invoice);
        $this->assertEquals($data['total_unpaid'], $invoice->total_unpaid);
        $this->assertEquals($data['minimum_down_payment'], $invoice->minimum_down_payment);
    }

    #[Test]
    public function it_can_delete_a_invoice()
    {
        $user = User::factory()->create();
        $invoice = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);

        $deleted = $invoice->delete();

        $this->assertTrue($deleted);
    }

    #[Test]
    public function it_errors_when_updating_the_invoice()
    {
        $user = User::factory()->create();
        $invoice = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);
        $this->expectException(QueryException::class);

        $invoice->update(['code' => null]);
    }

    #[Test]
    public function it_can_update_the_invoice()
    {
        $user = User::factory()->create();
        $invoice = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);

        $update = ['code' => 'code'];
        $updated = $invoice->update($update);

        $invoice = $invoice->where('code', $update['code'])->first();

        $this->assertTrue($updated);
        $this->assertEquals($update['code'], $invoice->code);
    }

    #[Test]
    public function it_can_find_the_invoice()
    {
        $user = User::factory()->create();
        $invoice = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);

        $found = Invoice::find($invoice->getKey());

        $this->assertInstanceOf(Invoice::class, $found);
        $this->assertEquals($invoice->code, $found->code);
    }

    #[Test]
    public function it_can_find_the_invoice_has_items()
    {
        $user = User::factory()->create();
        $invoice = InvoiceFactory::new()->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);

        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $items = [
            [
                'model_id' => $product1->getKey(),
                'model_type' => $product1->getMorphClass(),
                'quantity' => 1,
                'price_unit' => 100,
                'currency' => 'USD',
            ],
            [
                'model_id' => $product2->getKey(),
                'model_type' => $product2->getMorphClass(),
                'quantity' => 2,
                'price_unit' => 20,
                'currency' => 'USD',
            ],
        ];

        foreach ($items as $item) {
            $invoice->items()->create($item);
        }

        $found = Invoice::find($invoice->getKey());

        $this->assertInstanceOf(Invoice::class, $found);
        $this->assertInstanceOf(Collection::class, $found->items);
        $this->assertInstanceOf(Item::class, $found->items->first());
        $this->assertEquals($invoice->code, $found->code);
    }

    #[Test]
    public function it_can_list_all_invoices()
    {
        $user = User::factory()->create();
        $invoices = InvoiceFactory::new()->count(3)->create([
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ]);

        $this->assertInstanceOf(Collection::class, $invoices);
        $this->assertCount(3, $invoices->all());
    }
}
