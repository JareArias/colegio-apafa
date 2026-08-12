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
        Schema::create('apafa_meetings', function (Blueprint $table) {
            $table->id();

            $table->string('title');                  // Ej: "Asamblea General de Inicio de Año"
            $table->text('description')->nullable();
            $table->dateTime('meeting_date');         // Fecha y hora programada
            $table->string('qr_token')->unique()->nullable(); // Token único para el QR dinámico
            $table->boolean('is_active')->default(true);       // Si la reunión está abierta para marcar
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apafa_meetings');
    }
};
