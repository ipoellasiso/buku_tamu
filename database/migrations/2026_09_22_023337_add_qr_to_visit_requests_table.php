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
        Schema::table('visit_requests', function (Blueprint $table) {
            $table->string('qr_token')->nullable()->unique()->after('scheduled_at');
            $table->string('qr_code_path')->nullable()->after('qr_token');
            $table->enum('status', ['pending', 'approved', 'confirmed', 'completed', 'rejected'])
                ->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_requests', function (Blueprint $table) {
            //
        });
    }
};
