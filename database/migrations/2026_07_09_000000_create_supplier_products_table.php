<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['supplier_id', 'product_id']);
        });

        // Seed pivot from existing products.supplier_name -> suppliers.name mapping
        DB::table('products')
            ->whereNotNull('supplier_name')
            ->join('suppliers', 'suppliers.name', '=', 'products.supplier_name')
            ->select('suppliers.id as supplier_id', 'products.id as product_id')
            ->get()
            ->each(function ($row) {
                DB::table('supplier_products')->insertOrIgnore([
                    'supplier_id' => $row->supplier_id,
                    'product_id'  => $row->product_id,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            });

        // Also seed from supplier_price_histories (tracks actual deliveries)
        DB::table('supplier_price_histories')
            ->select('supplier_id', 'product_id')
            ->whereNotNull('supplier_id')
            ->distinct()
            ->get()
            ->each(function ($row) {
                DB::table('supplier_products')->insertOrIgnore([
                    'supplier_id' => $row->supplier_id,
                    'product_id'  => $row->product_id,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_products');
    }
};
