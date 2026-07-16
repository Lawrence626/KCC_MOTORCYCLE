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
        Schema::create('dss_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('recommendation_type'); // promotion, discount, bundle, relocate, featured_display, social_media, supplier_return
            $table->text('title');
            $table->text('description');
            $table->string('priority'); // Critical, High, Medium, Low
            $table->json('metadata')->nullable(); // Additional data like bundle_product_ids, discount_percentage, etc.
            $table->boolean('is_active')->default(true);
            $table->dateTime('generated_at');
            $table->dateTime('last_updated_at')->nullable();
            $table->dateTime('action_taken_at')->nullable();
            $table->text('action_notes')->nullable();
            $table->timestamps();

            $table->index('product_id');
            $table->index('recommendation_type');
            $table->index('priority');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dss_recommendations');
    }
};
