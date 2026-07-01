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
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onUpdate('cascade')->onDelete('restrict');
            $table->integer('quantity')->unsigned();
            $table->decimal('unit_price', 10, 2);
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamp('created_at')->useCurrent();

            // Índices
            $table->index('sale_id', 'idx_sale_details_sale_id');
            $table->index('product_id', 'idx_sale_details_product_id');
            $table->index(['product_id', 'created_at'], 'idx_sale_details_product_date');
        });

        DB::statement('ALTER TABLE sale_details ADD CONSTRAINT chk_sale_details_quantity_positive CHECK (quantity > 0)');
        DB::statement('ALTER TABLE sale_details ADD CONSTRAINT chk_sale_details_unit_price_positive CHECK (unit_price > 0)');
        DB::statement('ALTER TABLE sale_details ADD CONSTRAINT chk_sale_details_unit_cost_positive CHECK (unit_cost > 0)');
        DB::statement('ALTER TABLE sale_details ADD CONSTRAINT chk_sale_details_subtotal_consistent CHECK (ABS(subtotal - (quantity * unit_price)) < 0.01)');
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_details');
    }
};
