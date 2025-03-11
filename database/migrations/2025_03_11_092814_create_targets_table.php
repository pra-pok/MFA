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
        Schema::create('targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('target_group_id');
            $table->integer('min_target')->nullable();
            $table->integer('max_target')->nullable();
            $table->string('amount_percentage')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
            $table->foreign('target_group_id')->references('id')->on('target_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
