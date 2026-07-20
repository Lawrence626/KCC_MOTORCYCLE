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
        Schema::table('product_catalog', function (Blueprint $table) {
            // Add product_description column
            $table->string('product_description')->after('brand')->nullable();
            
            // Drop category_id foreign key and column
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_catalog', function (Blueprint $table) {
            // Add back category_id
            $table->unsignedBigInteger('category_id')->nullable()->after('brand');
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade');
            
            // Drop product_description column
            $table->dropColumn('product_description');
        });
    }
};
