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
            $table->string('skripsi_file_path')->nullable();
            $table->enum('skripsi_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->text('skripsi_rejection_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['skripsi_file_path', 'skripsi_status', 'skripsi_rejection_reason']);
        });
    }
};
