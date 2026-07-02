<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('warehouse_shelves', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('warehouse_index');
            $table->unsignedSmallInteger('slot_index');
            $table->string('name');
            $table->json('products')->nullable();
            $table->boolean('archived')->default(false);
            $table->timestamps();

            $table->unique(['warehouse_index', 'slot_index']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouse_shelves');
    }
};
