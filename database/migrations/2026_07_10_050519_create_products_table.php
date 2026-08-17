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
        Schema::create('product_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('brand');
            if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
                $table->foreignId('category_id')->constrained('product_categories')->onDelete('cascade');
            } else {
                $table->unsignedBigInteger('category_id')->nullable();
            }
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();

            // Indexes
            $table->index('product_name');
            $table->index('brand');
            $table->index('category_id');
            $table->index('sku');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_catalog');
    }
};
