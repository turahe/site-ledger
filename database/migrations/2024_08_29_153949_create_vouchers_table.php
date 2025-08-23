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
        Schema::create('vouchers', function (Blueprint $table) {
            if (config('userstamps.users_table_column_type') === 'ulid') {
                $table->ulid('id')->primary();
                $table->ulidMorphs('model');
            }
            if (config('userstamps.users_table_column_type') === 'bigIncrements') {
                $table->id();
                $table->morphs('model');
            }
            if (config('userstamps.users_table_column_type') === 'uuid') {
                $table->uuid('id')->primary();
                $table->uuidMorphs('model');
            }

            $table->string('code');
            $table->text('note')->nullable();
            $table->decimal('total_unit', 64, 4)->nullable();
            $table->decimal('total_value', 64, 4)->comment('amount is an decimal, it could be "dollars" or "cents"');
            $table->integer('due_date')->nullable();
            $table->string('status')->default('active');
            $table->string('currency')->default('IDR');

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

            $table->index('id', 'vouchers_id_idx', 'hash');
        });

        Schema::create('voucher_items', function (Blueprint $table) {
            if (config('userstamps.users_table_column_type') === 'ulid') {
                $table->ulid('id')->primary();
                $table->ulidMorphs('model');
                $table->ulid('voucher_id')->index();
            }
            if (config('userstamps.users_table_column_type') === 'bigIncrements') {
                $table->id();
                $table->morphs('model');
                $table->unsignedBigInteger('voucher_id')->index();
            }
            if (config('userstamps.users_table_column_type') === 'uuid') {
                $table->uuid('id')->primary();
                $table->uuidMorphs('model');
                $table->uuid('voucher_id')->index();
            }
            $table->decimal('quantity', 64)->default(1);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 64, 4);
            $table->decimal('total_price', 64, 4)->nullable();
            $table->text('description')->nullable();

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

            $table->index('id', 'voucher_items_id_idx', 'hash');
            $table->index('model_id', 'voucher_items_model_id_idx', 'hash');
            $table->index('model_type', 'voucher_items_model_type_idx', 'hash');

            // Add foreign key constraint for voucher_id
            if (config('userstamps.users_table_column_type') === 'ulid') {
                $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('cascade');
            }
            if (config('userstamps.users_table_column_type') === 'bigincrements') {
                $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('cascade');
            }
            if (config('userstamps.users_table_column_type') === 'uuid') {
                $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_items');
        Schema::dropIfExists('vouchers');
    }
};
