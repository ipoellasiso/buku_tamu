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
       Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('institution')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('purpose');
            $table->string('host')->nullable(); // yang dituju
            $table->string('photo_path')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('qr_token')->unique();
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
