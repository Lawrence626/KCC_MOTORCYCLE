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
        Schema::create('replacements', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no');
            $table->string('returned_item');
            $table->string('reason');
            $table->string('replacement_product');
            $table->integer('quantity')->default(1);
            $table->string('status')->default('pending');
            $table->timestamps();
            
            $table->index('receipt_no');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('replacements');
    }
};
