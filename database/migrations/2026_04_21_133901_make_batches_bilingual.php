<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->renameColumn('title', 'title_old');
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->json('title')->nullable()->after('course_id');
            $table->json('description')->nullable()->after('title');
        });

        // Migrate data
        DB::table('batches')->get()->each(function ($batch) {
            DB::table('batches')->where('id', $batch->id)->update([
                'title' => json_encode(['en' => $batch->title_old, 'bn' => $batch->title_old])
            ]);
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn('title_old');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->string('title_old')->nullable()->after('course_id');
        });

        // Inverse migrate
        DB::table('batches')->get()->each(function ($batch) {
            $title = json_decode($batch->title, true);
            DB::table('batches')->where('id', $batch->id)->update([
                'title_old' => $title['en'] ?? ($title['bn'] ?? '')
            ]);
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
            $table->renameColumn('title_old', 'title');
        });
    }
};
