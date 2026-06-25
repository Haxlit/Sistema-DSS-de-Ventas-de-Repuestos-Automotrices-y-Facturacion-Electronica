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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('Full name of the system user');
            $table->string('email', 150)->unique()->comment('Email address — unique login credential');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255)->comment('Bcrypt hash of the password');
            $table->enum('role', ['admin', 'vendedor'])->default('vendedor')->comment('RBAC Role');
            $table->tinyInteger('estado')->default(1)->comment('User status: 1 = Active, 0 = Inactive');
            $table->rememberToken();
            $table->timestamps();

            // Índices adicionales sugeridos por el DSS
            $table->index('role', 'idx_users_role');
            $table->index('email', 'idx_users_email');
            $table->index('estado', 'idx_users_estado');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};