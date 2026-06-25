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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->comment('Category name');
            $table->string('description', 255)->nullable()->default(null)->comment('Optional description');
            $table->tinyInteger('estado')->default(1)->comment('Category status: 1 = Active, 0 = Inactive');
            $table->timestamps();

            $table->index('estado', 'idx_product_categories_estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
