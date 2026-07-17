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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('recipient_name')->nullable();
            $table->string('recipient_contact')->nullable(); // Email address or WhatsApp phone number
            $table->string('channel')->default('whatsapp'); // 'whatsapp' or 'email'
            $table->string('category')->default('reminder'); // 'reminder_mentor', 'late_checkin', 'missing_logbook', etc.
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('status')->default('sent'); // 'sent', 'failed', 'pending'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
