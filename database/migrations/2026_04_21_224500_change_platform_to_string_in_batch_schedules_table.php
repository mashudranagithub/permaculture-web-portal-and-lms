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
        Schema::table('batch_schedules', function (Blueprint $table) {
            $table->string('platform')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_schedules', function (Blueprint $table) {
            $table->enum('platform', ['zoom', 'google_meet', 'ms_teams', 'other'])->default('other')->change();
        });
    }
};
