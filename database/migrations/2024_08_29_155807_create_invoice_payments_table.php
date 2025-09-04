<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Turahe\Ledger\Enums\PaymentMethods;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_payments', function (Blueprint $table) {
            if (config('userstamps.users_table_column_type') === 'bigIncrements') {
                $table->id();
                $table->foreignId('invoice_id')->index();
                $table->foreignId('receipt_id')->index();
            }
            if (config('userstamps.users_table_column_type') === 'ulid') {
                $table->ulid('id')->primary();
                $table->ulid('invoice_id')->index();
                $table->ulid('receipt_id')->index();
            }

            if (config('userstamps.users_table_column_type') === 'uuid') {
                $table->uuid('id')->primary();
                $table->foreignUuidFor(\Turahe\Ledger\Models\Invoice::class, 'invoice_id')->index();
                $table->foreignUuidFor(\Turahe\Ledger\Models\Voucher::class, 'receipt_id')->index();
            }

            $table->string('currency')->default('IDR')->index();

            $table->decimal('amount');
            $table->string('payment_gateway')->nullable()->index();

            $table->enum('payment_method', array_column(PaymentMethods::cases(), 'value'))->default('CASH')->index();
            $table->string('payment_channel')->nullable();
            $table->float('payment_fee')->default(0);
            $table->string('payment_status_code')->nullable();
            $table->string('payment_status_message')->nullable();
            $table->integer('payment_issued_at')->nullable();
            $table->integer('payment_expires_at')->nullable();

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
                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
                $table->foreign('receipt_id')->references('id')->on('vouchers')->onDelete('cascade');
            }
            if (config('userstamps.users_table_column_type') === 'ulid') {
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
                $table->foreign('receipt_id')->references('id')->on('vouchers')->onDelete('cascade');
            }
            if (config('userstamps.users_table_column_type') === 'uuid') {
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
    }
};
