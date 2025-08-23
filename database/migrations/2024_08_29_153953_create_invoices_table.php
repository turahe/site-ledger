<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create Invoices and Invoice Items Tables Migration
 *
 * Creates the invoices and invoice_items tables for the ledger system.
 * Supports comprehensive billing with fees, discounts, taxes, and payment tracking.
 *
 * Tables Created:
 * - invoices: Main invoice table with fee structure, payment tracking, and status management
 * - invoice_items: Individual items within invoices with detailed pricing and fee breakdowns
 *
 * @package Turahe\Ledger\Database\Migrations
 * @author  Nur Wachid <wachid@outlook.com>
 * @since   1.0.0
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the invoices and invoice_items tables with proper structure,
     * indexes, foreign key constraints, and comprehensive fee tracking.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            if (config('userstamps.users_table_column_type') === 'ulid') {
                $table->ulid('id')->primary();
                $table->ulidMorphs('model');
                $table->ulid('shipping_provider_id')->index()->nullable();
                $table->ulid('insurance_provider_id')->index()->nullable();
            }
            if (config('userstamps.users_table_column_type') === 'bigIncrements') {
                $table->id();
                $table->morphs('model');
                $table->unsignedBigInteger('shipping_provider_id')->index()->nullable();
                $table->unsignedBigInteger('insurance_provider_id')->index()->nullable();
            }
            if (config('userstamps.users_table_column_type') === 'uuid') {
                $table->uuid('id')->primary();
                $table->uuidMorphs('model');
                $table->uuid('shipping_provider_id')->index()->nullable();
                $table->uuid('insurance_provider_id')->index()->nullable();
            }
            $table->string('code')->unique()->index();
            $table->float('shipping_fee')->default(0);
            $table->float('insurance_fee')->default(0);
            $table->float('transaction_fee')->default(0);
            $table->string('discount_voucher')->nullable();
            $table->float('discount_amount')->default(0);
            $table->string('currency')->default('IDR')->index();
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
            $table->string('status')->default('draft');

            // Create userstamp columns with correct data types
            if (config('userstamps.users_table_column_type') === 'bigincrements') {
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->unsignedBigInteger('updated_by')->nullable()->index();
                $table->unsignedBigInteger('deleted_by')->nullable()->index();
            }
            if (config('userstamps.users_table_column_type') === 'ulid') {
                $table->ulid('created_by')->nullable()->index();
                $table->ulid('updated_by')->nullable()->index();
                $table->ulid('deleted_by')->nullable()->index();
            }
            if (config('userstamps.users_table_column_type') === 'uuid') {
                $table->uuid('created_by')->nullable()->index();
                $table->uuid('updated_by')->nullable()->index();
                $table->uuid('deleted_by')->nullable()->index();
            }

            $table->timestamps();
            $table->softDeletes();

            // Add foreign key constraints for userstamps
            if (config('userstamps.users_table_column_type') === 'bigincrements') {
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            }
            if (config('userstamps.users_table_column_type') === 'ulid') {
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            }
            if (config('userstamps.users_table_column_type') === 'uuid') {
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            }

            $table->index('id', 'invoice_id_idx', 'hash');
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            if (config('userstamps.users_table_column_type') === 'ulid') {
                $table->ulid('id')->primary();
                $table->ulidMorphs('model');
                $table->ulid('invoice_id')->index();
                $table->ulid('shipping_provider_id')->index()->nullable();
                $table->ulid('insurance_provider_id')->index()->nullable();
            }
            if (config('userstamps.users_table_column_type') === 'bigIncrements') {
                $table->id();
                $table->morphs('model');
                $table->unsignedBigInteger('invoice_id')->index();
                $table->unsignedBigInteger('shipping_provider_id')->index()->nullable();
                $table->unsignedBigInteger('insurance_provider_id')->index()->nullable();
            }
            if (config('userstamps.users_table_column_type') === 'uuid') {
                $table->uuid('id')->primary();
                $table->uuidMorphs('model');
                $table->uuid('invoice_id')->index();
                $table->uuid('shipping_provider_id')->index()->nullable();
                $table->uuid('insurance_provider_id')->index()->nullable();
            }
            $table->decimal('quantity', 64)->default(1);
            $table->string('unit')->nullable();
            $table->float('shipping_fee')->default(0);
            $table->float('insurance_fee')->default(0);
            $table->float('transaction_fee')->default(0);
            $table->string('discount_voucher')->nullable();
            $table->float('discount_amount')->default(0);
            $table->decimal('tax_amount', 64, 4)->comment('amount is an decimal, it could be "dollars" or "cents"')->default(0);
            $table->decimal('service_amount', 64, 4)->comment('amount is an decimal, it could be "dollars" or "cents"')->default(0);
            $table->decimal('mdr_fee', 64, 4)->comment('amount is an decimal, it could be "dollars" or "cents"')->default(0);
            $table->string('currency')->default('IDR')->index();
            $table->float('price_unit');

            // Create userstamp columns with correct data types
            if (config('userstamps.users_table_column_type') === 'bigincrements') {
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->unsignedBigInteger('updated_by')->nullable()->index();
                $table->unsignedBigInteger('deleted_by')->nullable()->index();
            }
            if (config('userstamps.users_table_column_type') === 'ulid') {
                $table->ulid('created_by')->nullable()->index();
                $table->ulid('updated_by')->nullable()->index();
                $table->ulid('deleted_by')->nullable()->index();
            }
            if (config('userstamps.users_table_column_type') === 'uuid') {
                $table->uuid('created_by')->nullable()->index();
                $table->uuid('updated_by')->nullable()->index();
                $table->uuid('deleted_by')->nullable()->index();
            }

            $table->timestamps();
            $table->softDeletes();

            // Add foreign key constraints for userstamps
            if (config('userstamps.users_table_column_type') === 'bigincrements') {
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            }
            if (config('userstamps.users_table_column_type') === 'ulid') {
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            }
            if (config('userstamps.users_table_column_type') === 'uuid') {
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            }

            $table->index('id', 'invoice_items_id_idx', 'hash');
            $table->index('model_id', 'invoice_items_model_id_idx', 'hash');
            $table->index('model_type', 'invoice_items_model_type_idx', 'hash');

            // Add foreign key constraint for invoice_id
            if (config('userstamps.users_table_column_type') === 'ulid') {
                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            }
            if (config('userstamps.users_table_column_type') === 'bigincrements') {
                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            }
            if (config('userstamps.users_table_column_type') === 'uuid') {
                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            }
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
