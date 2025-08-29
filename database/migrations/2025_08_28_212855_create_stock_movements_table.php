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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['entrada', 'saida', 'ajuste']);
            $table->integer('quantity');
            $table->decimal('unit_price', 8, 2)->nullable(); // Preço unitário no momento da movimentação
            $table->text('reason')->nullable(); // Motivo da movimentação
            $table->text('notes')->nullable(); // Observações adicionais
            $table->timestamps();
            
            // Índices para melhor performance
            $table->index(['product_id', 'created_at']);
            $table->index(['type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
