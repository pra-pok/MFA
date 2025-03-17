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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('vendor')->nullable();
            $table->string('recipients')->nullable();  
            $table->text('message');
            $table->integer('organization_id')->nullable();
            $table->string('organization_name')->nullable();
            $table->string('sender_phone_number')->nullable();
            $table->string('status')->default('sent');  
            $table->foreignId('sms_api_token_id')->constrained('sms_api_tokens')->onDelete('cascade');
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
