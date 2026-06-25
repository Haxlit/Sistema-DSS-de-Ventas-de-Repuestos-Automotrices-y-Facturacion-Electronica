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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->comment('Brand name');
            $table->string('country', 80)->nullable()->default(null)->comment('Country of origin');
            $table->tinyInteger('estado')->default(1)->comment('Brand status: 1 = Active, 0 = Inactive');
            $table->timestamps();

            $table->index('estado', 'idx_brands_estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
