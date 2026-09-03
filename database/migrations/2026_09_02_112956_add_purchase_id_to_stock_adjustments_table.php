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
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->foreignId('purchase_id')
                ->nullable()
                ->after('id')
                ->constrained('purchases')
                ->nullOnDelete();

            // Optional: Add cost_price if you want to track unit price adjustments
            $table->decimal('unit_cost', 10, 2)->nullable()->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropForeign(['purchase_id']);
            $table->dropColumn(['purchase_id', 'unit_cost']);
        });
    }
};
