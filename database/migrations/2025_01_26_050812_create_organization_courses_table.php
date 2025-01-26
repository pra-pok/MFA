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
        Schema::create('organization_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('course_id');
            $table->decimal('start_fee', 10, 2)->nullable();
            $table->decimal('end_fee', 10, 2)->nullable();
            $table->longText('description')->nullable();
            $table->boolean('status')->default('1');
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_courses');
    }
};
