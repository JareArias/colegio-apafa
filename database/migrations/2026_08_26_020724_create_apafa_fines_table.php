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
        Schema::create('apafa_fines', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Padre multado
            $table->foreignId('apafa_meeting_id')->constrained()->onDelete('cascade'); // Reunión en la que faltó
            $table->decimal('amount', 8, 2); // Monto de la multa (ej: 20.00)
            $table->enum('status', ['pending', 'paid'])->default('pending'); // Estado del pago
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apafa_fines');
    }
};
