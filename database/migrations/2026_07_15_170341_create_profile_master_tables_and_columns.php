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
        // 1. Create internship_types table
        Schema::create('internship_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // 2. Create education_levels table
        Schema::create('education_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // 3. Create universities table
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // 4. Create genders table
        Schema::create('genders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // 5. Add columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('internship_type_id')->nullable()->constrained('internship_types')->nullOnDelete();
            $table->foreignId('education_level_id')->nullable()->constrained('education_levels')->nullOnDelete();
            $table->foreignId('university_id')->nullable()->constrained('universities')->nullOnDelete();
            $table->foreignId('gender_id')->nullable()->constrained('genders')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['internship_type_id']);
            $table->dropForeign(['education_level_id']);
            $table->dropForeign(['university_id']);
            $table->dropForeign(['gender_id']);
            $table->dropColumn(['internship_type_id', 'education_level_id', 'university_id', 'gender_id']);
        });

        Schema::dropIfExists('internship_types');
        Schema::dropIfExists('education_levels');
        Schema::dropIfExists('universities');
        Schema::dropIfExists('genders');
    }
};
