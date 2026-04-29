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
        Schema::create('faculty_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained('faculty')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->string('academic_year');
            $table->integer('semester');
            $table->integer('total_students')->default(0);
            $table->integer('avg_attendance_count')->default(0);
            $table->decimal('avg_attendance_rate', 5, 2)->default(0); // percentage
            $table->decimal('avg_class_grade', 5, 2)->nullable();
            $table->decimal('assignment_completion_rate', 5, 2)->nullable();
            $table->decimal('student_engagement_score', 5, 2)->nullable();
            $table->text('insights')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_analytics');
    }
};
