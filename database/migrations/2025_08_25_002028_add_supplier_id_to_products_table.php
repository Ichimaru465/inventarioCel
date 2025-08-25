<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Añadimos la clave foránea para el proveedor.
            // Es nullable() porque un producto podría no tener proveedor.
            // onDelete('set null') para que si se borra un proveedor, el producto no se borre, solo se quede sin proveedor.
            $table->foreignId('supplier_id')->nullable()->after('brand_id')->constrained('suppliers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Esto es para poder revertir la migración si es necesario
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });
    }
};
