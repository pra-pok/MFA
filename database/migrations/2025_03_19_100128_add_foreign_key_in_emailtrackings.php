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
        Schema::table('emailtrackings', function (Blueprint $table) {
            $table->foreignId('organization_signup_id')->constrained('organization_signup')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emailtrackings', function (Blueprint $table) {
            //
        });
    }
};
