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
        Schema::create('apafa_attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('apafa_meeting_id')->constrained()->onDelete('cascade'); // Reunión
            $table->foreignId('user_id')->constrained()->onDelete('cascade');          // Padre que asiste
            $table->foreignId('student_id')->nullable()->constrained()->onDelete('set null'); // Hijo representado
            $table->enum('status', ['presente', 'tarde', 'ausente', 'justificado'])->default('presente');
            $table->enum('registered_by', ['self_qr', 'manual_scanner', 'dni'])->default('manual_scanner');
            $table->timestamp('scanned_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apafa_attendances');
    }
};
