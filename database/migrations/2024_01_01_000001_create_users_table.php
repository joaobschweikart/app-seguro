<?php
// database/migrations/2024_01_01_000001_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);

            $table->string('email', 254)->unique();

            $table->timestamp('email_verified_at')->nullable();

            $table->string('password', 255);

            $table->text('phone')->nullable()->comment('Armazenado criptografado com AES-256');
            $table->text('cpf')->nullable()->comment('Armazenado criptografado com AES-256');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
