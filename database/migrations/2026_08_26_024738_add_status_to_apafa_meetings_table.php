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
        // 1. Agregar campo status a la tabla de reuniones
        Schema::table('apafa_meetings', function (Blueprint $table) {
            if (!Schema::hasColumn('apafa_meetings', 'status')) {
                $table->string('status')->default('pending')->after('is_active');
            }
        });

        // 2. Agregar campos a la tabla de asistencias
        Schema::table('apafa_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('apafa_attendances', 'status')) {
                $table->enum('status', ['present', 'late'])->default('present')->after('user_id');
            }
            if (!Schema::hasColumn('apafa_attendances', 'registered_at')) {
                $table->time('registered_at')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('apafa_meetings', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('apafa_attendances', function (Blueprint $table) {
            $table->dropColumn(['status', 'registered_at']);
        });
    }
};
