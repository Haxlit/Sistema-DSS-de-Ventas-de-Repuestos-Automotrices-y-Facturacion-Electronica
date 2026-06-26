<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('name', 200);
            $table->foreignId('brand_id')->constrained('brands')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('category_id')->nullable()->default(null)->constrained('product_categories')->onUpdate('cascade')->onDelete('set null');
            $table->json('compatibility')->nullable()->default(null);
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2);
            $table->integer('stock')->unsigned()->default(0);
            $table->integer('stock_min')->unsigned()->default(5);
            $table->tinyInteger('estado')->default(1)->comment('Product status: 1 = Active, 0 = Discontinued');
            $table->timestamps();

            // Índices
            $table->index('sku', 'idx_products_sku');
            $table->index('brand_id', 'idx_products_brand');
            $table->index('category_id', 'idx_products_category');
            $table->index('estado', 'idx_products_estado');
            $table->index(['estado', 'stock', 'stock_min'], 'idx_products_stock_alert');
        });

        // Restricciones CHECK nativas de MariaDB agregadas mediante Raw SQL
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE products ADD CONSTRAINT chk_products_price_positive CHECK (price > 0)');
            DB::statement('ALTER TABLE products ADD CONSTRAINT chk_products_cost_positive CHECK (cost > 0)');
            DB::statement('ALTER TABLE products ADD CONSTRAINT chk_products_margin_valid CHECK (price >= cost)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
