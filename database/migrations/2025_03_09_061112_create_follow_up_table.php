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
        Schema::create('follow_up', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('via');
            $table->text('note');
            $table->boolean('status')->default(true);
            $table->boolean('is_current_status')->default(true);
            $table->dateTime('next_date_time');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_up');
    }
};
