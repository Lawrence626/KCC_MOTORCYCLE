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
        Schema::create('dss_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        $this->insertDefaultSettings();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dss_settings');
    }

    private function insertDefaultSettings(): void
    {
        $settings = [
            [
                'key' => 'dead_stock_threshold_days',
                'value' => '90',
                'type' => 'integer',
                'description' => 'Number of days without sale to classify as dead stock (default: 90)',
            ],
            [
                'key' => 'slow_moving_threshold_days',
                'value' => '60',
                'type' => 'integer',
                'description' => 'Number of days without sale to classify as slow moving (default: 60)',
            ],
            [
                'key' => 'fast_moving_threshold_units',
                'value' => '50',
                'type' => 'integer',
                'description' => 'Minimum units sold in 30 days to classify as fast moving',
            ],
            [
                'key' => 'bundle_recommendation_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable bundle recommendations',
            ],
            [
                'key' => 'promotion_recommendation_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable promotion recommendations',
            ],
            [
                'key' => 'discount_recommendation_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable discount recommendations',
            ],
            [
                'key' => 'dss_analysis_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable automatic DSS analysis and recommendations',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('dss_settings')->insert([
                'key' => $setting['key'],
                'value' => $setting['value'],
                'type' => $setting['type'],
                'description' => $setting['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
