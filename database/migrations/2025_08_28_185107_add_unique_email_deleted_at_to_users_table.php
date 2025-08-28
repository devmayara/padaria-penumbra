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
        Schema::table('users', function (Blueprint $table) {
            // Remove o índice único simples do email (se existir)
            $table->dropUnique(['email']);
            
            // Adiciona índice único composto: email + deleted_at
            // Isso permite ter o mesmo email se um dos registros estiver deletado
            $table->unique(['email', 'deleted_at'], 'users_email_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove o índice único composto
            $table->dropUnique('users_email_deleted_at_unique');
            
            // Restaura o índice único simples do email
            $table->unique(['email']);
        });
    }
};
