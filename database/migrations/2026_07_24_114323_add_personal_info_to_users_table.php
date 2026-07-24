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
            $table->string('birth_place')->nullable()->after('phone');
            $table->date('birth_date')->nullable()->after('birth_place');
            $table->text('address')->nullable()->after('birth_date');
            $table->string('religion')->nullable()->after('address');
            $table->string('citizenship')->nullable()->after('religion');
            $table->string('education_start_year', 4)->nullable()->after('study_program');
            $table->string('education_end_year', 4)->nullable()->after('education_start_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'birth_place', 
                'birth_date', 
                'address', 
                'religion', 
                'citizenship', 
                'education_start_year', 
                'education_end_year'
            ]);
        });
    }
};
