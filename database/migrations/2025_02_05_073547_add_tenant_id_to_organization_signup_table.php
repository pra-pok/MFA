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
        Schema::table('organization_signup', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('organization_role_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants');
            $table->foreign('organization_role_id')->references('id')->on('organization_roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_signup', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['organization_role_id']);
            $table->dropColumn('tenant_id');
            $table->dropColumn('organization_role_id');
        });
    }
};
