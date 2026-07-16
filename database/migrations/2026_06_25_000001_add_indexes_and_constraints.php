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
        Schema::table('attendances', function (Blueprint $table) {
            $table->index('status');
            $table->unique(['user_id', 'date']);
        });

        Schema::table('logbooks', function (Blueprint $table) {
            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropUnique(['user_id', 'date']);
        });

        Schema::table('logbooks', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'date']);
        });
    }
};
