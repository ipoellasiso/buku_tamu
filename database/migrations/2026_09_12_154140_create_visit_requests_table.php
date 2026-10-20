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
        // create_visit_requests_table
        Schema::create('visit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
            $table->string('purpose');
            $table->text('notes')->nullable();
            $table->dateTime('requested_at');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('employee_notes')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->foreignId('guest_id')->nullable()->constrained('guests')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_requests');
    }
};
