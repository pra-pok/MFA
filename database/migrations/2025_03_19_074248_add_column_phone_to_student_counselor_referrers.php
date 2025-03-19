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
        Schema::table('student_counselor_referrers', function (Blueprint $table) {
            $table->string('counselor_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_counselor_referrers', function (Blueprint $table) {
            $table->dropColumn('counselor_phone');
        });
    }
};
