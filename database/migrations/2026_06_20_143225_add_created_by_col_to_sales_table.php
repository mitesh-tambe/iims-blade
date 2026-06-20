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
        Schema::table('sales', function (Blueprint $table) {
            $table->string('created_by')->nullable();
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('mrp', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn('mrp');
        });
    }
};
