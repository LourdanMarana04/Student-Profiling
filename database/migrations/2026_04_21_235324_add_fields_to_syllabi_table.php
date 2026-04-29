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
        Schema::table('syllabi', function (Blueprint $table) {
            $table->string('academic_year')->after('faculty_id')->default('2026');
            $table->integer('semester')->after('academic_year')->default(1);
            $table->string('grading_scale')->nullable();
            $table->text('course_policies')->nullable();
            $table->text('accommodation_statement')->nullable();
            $table->unique(['course_id', 'faculty_id', 'academic_year', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::table('syllabi', function (Blueprint $table) {
            $table->dropUnique(['course_id', 'faculty_id', 'academic_year', 'semester']);
            $table->dropColumn(['academic_year', 'semester', 'grading_scale', 'course_policies', 'accommodation_statement']);
        });
    }
};
