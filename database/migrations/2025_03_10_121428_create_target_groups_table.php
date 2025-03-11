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
        Schema::create('target_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('counselor_referrer_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('counselor_referrer_id')->references('id')->on('counselor_referrers')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_groups');
    }
};
