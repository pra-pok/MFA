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
        Schema::table('organization_emailconfigs', function (Blueprint $table) {
            // $table->foreign('created_by')->nullable()->references('id')->on('users')->onDelete('set null');
            // $table->foreign('updated_by')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_emailconfigs', function (Blueprint $table) {
            //
        });
    }
};
