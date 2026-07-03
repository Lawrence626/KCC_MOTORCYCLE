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
        Schema::create('synchronization_history', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->dateTime('export_date')->nullable();
            $table->dateTime('import_date')->nullable();
            $table->unsignedBigInteger('exported_by')->nullable();
            $table->unsignedBigInteger('imported_by')->nullable();
            $table->integer('total_records')->default(0);
            $table->integer('imported_records')->default(0);
            $table->integer('duplicate_records')->default(0);
            $table->integer('failed_records')->default(0);
            $table->integer('skipped_records')->default(0);
            $table->enum('synchronization_status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('exported_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('imported_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('synchronization_history');
    }
};
