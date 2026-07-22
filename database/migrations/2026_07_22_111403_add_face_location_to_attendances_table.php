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
            $table->string('photo_in')->nullable()->after('notes');
            $table->string('photo_out')->nullable()->after('photo_in');
            $table->decimal('lat_in', 10, 8)->nullable()->after('photo_out');
            $table->decimal('lng_in', 11, 8)->nullable()->after('lat_in');
            $table->decimal('lat_out', 10, 8)->nullable()->after('lng_in');
            $table->decimal('lng_out', 11, 8)->nullable()->after('lat_out');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['photo_in', 'photo_out', 'lat_in', 'lng_in', 'lat_out', 'lng_out']);
        });
    }
};
