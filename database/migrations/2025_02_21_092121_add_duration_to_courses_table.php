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
        Schema::table('courses', function (Blueprint $table) {
            $table->string('duration')->nullable();
            $table->decimal('min_range_fee', 10, 2)->nullable();
            $table->decimal('max_range_fee', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('duration');
            $table->dropColumn('min_range_fee');
            $table->dropColumn('max_range_fee');
        });
    }
};
