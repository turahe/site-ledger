<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulidMorphs('model');
            $table->string('code')->unique()->index();
            $table->foreignIdFor(config('ledger.shipping_provider', \App\Models\Organization::class), 'shipping_provider_id')->index()->nullable();
            $table->float('shipping_fee')->default(0);
            $table->foreignIdFor(config('ledger.insurance_provider', \App\Models\Organization::class), 'insurance_provider_id')->index()->nullable();
            $table->float('insurance_fee')->default(0);
            $table->float('transaction_fee')->default(0);
            $table->string('discount_voucher')->nullable();
            $table->float('discount_amount')->default(0);

            $table->string('currency')->default('IDR')->index();
            $table->foreign('currency')
                ->references('iso_code')
                ->on('tm_currencies');

            $table->integer('issue_date')->nullable();
            $table->integer('due_date')->nullable();
            $table->decimal('tax_amount', 64, 4)->comment('amount is an decimal, it could be "dollars" or "cents"')->default(0);
            $table->decimal('service_amount', 64, 4)->comment('amount is an decimal, it could be "dollars" or "cents"')->default(0);
            $table->decimal('mdr_fee', 64, 4)->comment('amount is an decimal, it could be "dollars" or "cents"')->default(0);
            $table->decimal('total_amount', 64, 4)->comment('amount is an decimal, it could be "dollars" or "cents"')->default(0);
            $table->decimal('total_invoice', 64, 4)->comment('amount is an decimal, it could be "dollars" or "cents"')->default(0);
            $table->decimal('total_payment', 64, 4)->comment('amount is an decimal, it could be "dollars" or "cents"')->default(0);
            $table->decimal('total_unpaid', 64, 4)->comment('amount is an decimal, it could be "dollars" or "cents"')->default(0);
            $table->decimal('total_change', 64, 4)->comment('amount is an decimal, it could be "dollars" or "cents"')->default(0);
            $table->decimal('minimum_down_payment', 64, 4)->comment('amount is an decimal, it could be "dollars" or "cents"')->default(0);

            $table->foreignUlid('created_by')
                ->index()
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignUlid('updated_by')
                ->index()
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('id', 'invoice_id_idx', 'hash');

        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('invoice_id')->index()->constrained('invoices')->cascadeOnDelete();

            $table->ulidMorphs('model');
            $table->decimal('quantity', 64)->default(1);
            $table->string('unit')->nullable();
            $table->foreignIdFor(config('ledger.shipping_provider', \App\Models\Organization::class), 'shipping_provider_id')->index()->nullable();
            $table->float('shipping_fee')->default(0);
            $table->foreignIdFor(config('ledger.insurance_provider', \App\Models\Organization::class), 'insurance_provider_id')->index()->nullable();
            $table->float('insurance_fee')->default(0);
            $table->float('transaction_fee')->default(0);
            $table->string('discount_voucher')->nullable();
            $table->float('discount_amount')->default(0);
            $table->decimal('tax_amount', 64, 4)->comment('amount is an decimal, it could be "dollars" or "cents"')->default(0);
            $table->decimal('service_amount', 64, 4)->comment('amount is an decimal, it could be "dollars" or "cents"')->default(0);
            $table->decimal('mdr_fee', 64, 4)->comment('amount is an decimal, it could be "dollars" or "cents"')->default(0);

            $table->string('currency')->default('IDR')->index();
            $table->foreign('currency')
                ->references('iso_code')
                ->on('tm_currencies')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->float('price_unit');

            $table->foreignUlid('created_by')
                ->index()
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignUlid('updated_by')
                ->index()
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('id', 'invoice_items_id_idx', 'hash');
            $table->index('model_id', 'invoice_items_model_id_idx', 'hash');
            $table->index('model_type', 'invoice_items_model_type_idx', 'hash');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
