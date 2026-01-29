<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name'); // snapshot del nombre al momento de la venta
            $table->string('product_sku')->nullable(); // snapshot del SKU
            $table->decimal('price', 10, 2); // precio unitario original
            $table->integer('quantity');
            $table->decimal('discount_amount', 10, 2)->default(0); // descuento total de la línea
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};

