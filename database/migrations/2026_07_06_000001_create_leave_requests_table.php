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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // 'izin', 'sakit', 'cuti'
            $table->date('start_date');
            $table->date('end_date');
            $table->text('notes');
            $table->string('attachment')->nullable();
            $table->string('status')->default('pending'); // 'pending', 'approved', 'rejected'
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
