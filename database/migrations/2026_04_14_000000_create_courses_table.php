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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->json('title'); // Stores {"en": "...", "bn": "..."}
            $table->json('description');
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('level')->default('Foundation'); // Foundation, Intermediate, Advanced
            $table->boolean('is_online')->default(true);
            $table->string('duration')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
