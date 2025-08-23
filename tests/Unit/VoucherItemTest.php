<?php

namespace Turahe\Ledger\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Turahe\Ledger\Models\Voucher\Item;

class VoucherItemTest extends TestCase
{
    public function test_voucher_item_class_exists(): void
    {
        $this->assertTrue(class_exists(Item::class));
    }

    public function test_voucher_item_extends_model(): void
    {
        $this->assertTrue(is_subclass_of(Item::class, \Illuminate\Database\Eloquent\Model::class));
    }

    public function test_voucher_item_has_correct_table_name(): void
    {
        $reflection = new \ReflectionClass(Item::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);

        $this->assertEquals('voucher_items', $property->getDefaultValue());
    }

    public function test_voucher_item_has_expected_fillable_fields(): void
    {
        $reflection = new \ReflectionClass(Item::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);

        $expectedFillable = [
            'model_id',
            'model_type',
            'quantity',
            'unit_price',
            'total_price',
            'description',
            'metadata',
        ];

        $this->assertEquals($expectedFillable, $property->getDefaultValue());
    }

    public function test_voucher_item_has_voucher_relationship_method(): void
    {
        $this->assertTrue(method_exists(Item::class, 'voucher'));
    }

    public function test_voucher_item_has_get_subtotal_method(): void
    {
        $this->assertTrue(method_exists(Item::class, 'getSubtotal'));
    }

    public function test_voucher_item_has_get_total_price_method(): void
    {
        $this->assertTrue(method_exists(Item::class, 'getTotalPrice'));
    }

    public function test_voucher_item_uses_required_traits(): void
    {
        $traits = class_uses(Item::class);

        $this->assertTrue(in_array(
            'Turahe\Core\Concerns\HasConfigurablePrimaryKey',
            $traits
        ));

        $this->assertTrue(in_array(
            'Turahe\UserStamps\Concerns\HasUserStamps',
            $traits
        ));
    }
}
