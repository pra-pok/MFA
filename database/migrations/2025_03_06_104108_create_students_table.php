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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('temporary_address')->nullable();
            $table->foreignId('permanent_locality_id')->constrained('localities')->onDelete('cascade');
            $table->foreignId('temporary_locality_id')->contrained('localities')->onDelete('cascade');
            $table->foreignId('referral_source_id')->constrained('referral_sources')->onDelete('cascade');
            $table->foreignId('counselor_referred_id')->constrained('counselor_referrers')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
