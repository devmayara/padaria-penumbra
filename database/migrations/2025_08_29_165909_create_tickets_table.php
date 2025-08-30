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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('ticket_number')->unique(); // Número único da ficha
            $table->string('qr_code_path')->nullable(); // Caminho do QRCode gerado
            $table->string('pdf_path')->nullable(); // Caminho do PDF gerado
            $table->enum('status', ['pendente', 'gerado', 'impresso'])->default('pendente');
            $table->timestamp('generated_at')->nullable(); // Quando foi gerada
            $table->timestamp('printed_at')->nullable(); // Quando foi impressa
            $table->text('notes')->nullable(); // Observações adicionais
            $table->timestamps();
            
            // Índices para performance
            $table->index(['order_id', 'status']);
            $table->index('ticket_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
