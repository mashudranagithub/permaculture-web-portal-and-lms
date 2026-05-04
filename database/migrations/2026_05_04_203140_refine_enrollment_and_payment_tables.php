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
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('organization_id')->after('id')->nullable()->constrained()->nullOnDelete();
            $table->string('enrollment_no')->after('organization_id')->nullable()->unique();
            $table->decimal('price_at_enrollment', 10, 2)->after('batch_id')->nullable();
            $table->string('payment_status')->after('status')->default('pending'); // pending, partially_paid, paid, refunded
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->softDeletes();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('organization_id')->after('id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('enrollment_id')->after('batch_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('currency', 3)->after('amount')->default('BDT');
            $table->json('payment_details')->after('status')->nullable(); // For gateway response
            $table->text('notes')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn(['organization_id', 'enrollment_no', 'price_at_enrollment', 'payment_status', 'enrolled_at', 'expires_at', 'deleted_at']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['organization_id', 'enrollment_id']);
            $table->dropColumn(['organization_id', 'enrollment_id', 'currency', 'payment_details', 'notes', 'deleted_at']);
        });
    }
};
