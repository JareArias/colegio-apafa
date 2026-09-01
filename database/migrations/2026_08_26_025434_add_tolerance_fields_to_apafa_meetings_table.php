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
        Schema::table('apafa_meetings', function (Blueprint $table) {
            $table->time('start_time')->nullable()->after('meeting_date');
            $table->integer('tolerance_minutes')->default(15)->after('start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apafa_meetings', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'tolerance_minutes']);
        });
    }
};
