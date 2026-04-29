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
        Schema::table('sections', function (Blueprint $table) {
            if (! Schema::hasColumn('sections', 'faculty_id')) {
                $table->foreignId('faculty_id')
                    ->nullable()
                    ->after('course_id')
                    ->constrained('faculty')
                    ->nullOnDelete();
            }
        });

        if (! Schema::hasTable('section_student')) {
            Schema::create('section_student', function (Blueprint $table) {
                $table->id();
                $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->timestamps();
                $table->unique(['section_id', 'student_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_student');

        Schema::table('sections', function (Blueprint $table) {
            if (Schema::hasColumn('sections', 'faculty_id')) {
                $table->dropConstrainedForeignId('faculty_id');
            }
        });
    }
};
