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
        if (!Schema::hasColumn('product_descriptions', 'is_expirable')) {
            Schema::table('product_descriptions', function (Blueprint $table) {
                $table->boolean('is_expirable')->default(false)->after('brands');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_descriptions', function (Blueprint $table) {
            $table->dropColumn('is_expirable');
        });
    }
};
