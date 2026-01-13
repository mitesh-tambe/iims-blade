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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->integer('current_stock')->default(0);
            $table->integer('critical_level')->default(5);
            $table->string('rack_no')->nullable();

            $table->enum('status', ['IN_STOCK', 'CRITICAL', 'OUT_OF_STOCK'])
                ->default('IN_STOCK');

            $table->unique('product_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
