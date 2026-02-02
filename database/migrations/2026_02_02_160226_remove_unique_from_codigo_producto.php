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
        try {
            Schema::table('products', function (Blueprint $table) {
                // MySQL crea el índice como products_sku_unique; hay que usar el nombre exacto
                $table->dropUnique('products_sku_unique');
            });
        } catch (\Throwable $e) {
            // Si el índice ya no existe (MySQL 1091), no fallar
            if (str_contains($e->getMessage(), '1091') || str_contains($e->getMessage(), 'column/key exists')) {
                return;
            }
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unique('sku', 'products_sku_unique');
        });
    }
};
