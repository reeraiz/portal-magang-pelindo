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
        Schema::table('users', function (Blueprint $table) {
            $table->date('internship_start_date')->nullable();
            $table->date('internship_end_date')->nullable();
            $table->foreignId('mentor_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['mentor_id']);
            $table->dropColumn(['internship_start_date', 'internship_end_date', 'mentor_id']);
        });
    }
};
