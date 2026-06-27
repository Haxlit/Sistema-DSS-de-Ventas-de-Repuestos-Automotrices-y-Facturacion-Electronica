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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->enum('invoice_status', ['pending', 'issued', 'error'])->default('pending');
            $table->string('invoice_number', 50)->nullable()->default(null);
            $table->string('invoice_xml_hash', 64)->nullable()->default(null);
            $table->timestamp('invoice_issued_at')->nullable()->default(null);
            $table->timestamps();

            // Índices
            $table->index('created_at', 'idx_sales_created_at');
            $table->index('user_id', 'idx_sales_user_id');
            $table->index('invoice_status', 'idx_sales_invoice_status');
            $table->index(['created_at', 'invoice_status'], 'idx_sales_date_status');
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE sales ADD CONSTRAINT chk_sales_total_positive CHECK (total_amount >= 0)');
            DB::statement('ALTER TABLE sales ADD CONSTRAINT chk_sales_hash_length CHECK (invoice_xml_hash IS NULL OR CHAR_LENGTH(invoice_xml_hash) = 64)');
        }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
