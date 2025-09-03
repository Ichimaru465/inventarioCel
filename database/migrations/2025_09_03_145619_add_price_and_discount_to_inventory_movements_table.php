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
    Schema::table('inventory_movements', function (Blueprint $table) {
        // Precio unitario al que se vendió el producto
        $table->decimal('price', 10, 2)->nullable()->after('quantity');
        // Monto del descuento aplicado a esta línea de producto
        $table->decimal('discount_amount', 10, 2)->default(0)->after('price');
    });
}

public function down(): void
{
    Schema::table('inventory_movements', function (Blueprint $table) {
        $table->dropColumn(['price', 'discount_amount']);
    });
}
};
