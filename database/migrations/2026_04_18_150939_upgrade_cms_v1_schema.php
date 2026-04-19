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
        // 1. Upgrade Courses Table
        Schema::table('courses', function (Blueprint $table) {
            $table->string('slug')->unique()->after('title');
            $table->json('short_description')->after('description');
            $table->string('banner_image')->nullable()->after('image');
            $table->enum('delivery_mode', ['online', 'offline', 'hybrid'])->default('online')->after('duration');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->after('delivery_mode');
            $table->boolean('is_featured')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->softDeletes();
        });

        // 2. Upgrade Batches Table
        Schema::table('batches', function (Blueprint $table) {
            $table->date('enrollment_deadline')->nullable()->after('end_date');
            $table->decimal('discount_amount', 10, 2)->nullable()->after('price');
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable()->after('discount_amount');
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->default('upcoming')->after('discount_type');
            $table->boolean('is_enrollment_open')->default(true);
            $table->softDeletes();
        });

        // 3. Upgrade Topics Table
        Schema::table('topics', function (Blueprint $table) {
            // Drop existing title string and recreate as JSON (sqlite workaround not needed for MySQL)
            $table->json('title')->change();
            // content -> content_body (json)
            $table->renameColumn('content', 'content_body');
        });

        Schema::table('topics', function (Blueprint $table) {
            $table->json('content_body')->nullable()->change();
            $table->json('description')->nullable()->after('content_body');
            $table->enum('topic_type', ['content', 'pdf', 'online_class', 'assignment', 'quiz'])->default('content')->after('order_index');
            $table->string('pdf_file_en')->nullable();
            $table->string('pdf_file_bn')->nullable();
            $table->string('estimated_duration')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_free_preview')->default(false);
            $table->softDeletes();
        });

        // 4. Upgrade Enrollments Table
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('payment_id')->nullable()->after('batch_id')->constrained('payments')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->string('status')->default('active')->change(); // Requirement: Add more statuses
        });

        // 5. Upgrade Payments Table
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('discount_applied', 10, 2)->nullable();
            $table->decimal('net_amount', 10, 2)->after('amount');
            $table->json('gateway_response')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->text('verification_notes')->nullable();
            $table->string('invoice_number')->unique()->nullable();
        });

        // 6. New Tables for CMS Module
        
        // Class Sessions
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes');
            $table->string('meeting_link');
            $table->string('meeting_password')->nullable();
            $table->enum('platform', ['zoom', 'google_meet', 'ms_teams', 'other'])->default('other');
            $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled');
            $table->string('youtube_video_url')->nullable();
            $table->timestamp('video_uploaded_at')->nullable();
            $table->string('instructor_name')->nullable();
            $table->json('notes')->nullable();
            $table->timestamps();
        });

        // Batch Schedules (Routine)
        Schema::create('batch_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->enum('day_of_week', ['sat', 'sun', 'mon', 'tue', 'wed', 'thu', 'fri']);
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('platform', ['zoom', 'google_meet', 'ms_teams', 'other'])->default('other');
            $table->boolean('is_recurring')->default(true);
            $table->timestamps();
        });

        // Topic Progress
        Schema::create('topic_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_spent_seconds')->default(0);
            $table->unique(['user_id', 'topic_id']);
            $table->timestamps();
        });

        // Topic Attachments
        Schema::create('topic_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_attachments');
        Schema::dropIfExists('topic_progress');
        Schema::dropIfExists('batch_schedules');
        Schema::dropIfExists('class_sessions');

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['discount_applied', 'net_amount', 'gateway_response', 'payment_date', 'verified_by', 'verification_notes', 'invoice_number']);
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_id');
            $table->dropColumn(['completed_at', 'last_accessed_at', 'progress_percentage']);
        });

        Schema::table('topics', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['is_free_preview', 'is_published', 'estimated_duration', 'pdf_file_bn', 'pdf_file_en', 'topic_type', 'description']);
            $table->renameColumn('content_body', 'content');
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['is_enrollment_open', 'status', 'discount_type', 'discount_amount', 'enrollment_deadline']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn(['meta_description', 'meta_title', 'is_featured', 'status', 'delivery_mode', 'banner_image', 'short_description', 'slug']);
        });
    }
};
