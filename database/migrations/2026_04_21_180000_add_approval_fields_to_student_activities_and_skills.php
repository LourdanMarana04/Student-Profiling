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
        Schema::table('student_activities', function (Blueprint $table) {
            if (! Schema::hasColumn('student_activities', 'approval_status')) {
                $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('approved')->after('status');
            }

            if (! Schema::hasColumn('student_activities', 'review_notes')) {
                $table->text('review_notes')->nullable()->after('approval_status');
            }

            if (! Schema::hasColumn('student_activities', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('review_notes')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('student_activities', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
        });

        Schema::table('student_skills', function (Blueprint $table) {
            if (! Schema::hasColumn('student_skills', 'approval_status')) {
                $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('approved')->after('proficiency_level');
            }

            if (! Schema::hasColumn('student_skills', 'review_notes')) {
                $table->text('review_notes')->nullable()->after('approval_status');
            }

            if (! Schema::hasColumn('student_skills', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('review_notes')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('student_skills', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_activities', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('student_activities', 'reviewed_at') ? 'reviewed_at' : null,
                Schema::hasColumn('student_activities', 'reviewed_by') ? 'reviewed_by' : null,
                Schema::hasColumn('student_activities', 'review_notes') ? 'review_notes' : null,
                Schema::hasColumn('student_activities', 'approval_status') ? 'approval_status' : null,
            ]);

            if (Schema::hasColumn('student_activities', 'reviewed_by')) {
                $table->dropConstrainedForeignId('reviewed_by');
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('student_skills', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('student_skills', 'reviewed_at') ? 'reviewed_at' : null,
                Schema::hasColumn('student_skills', 'reviewed_by') ? 'reviewed_by' : null,
                Schema::hasColumn('student_skills', 'review_notes') ? 'review_notes' : null,
                Schema::hasColumn('student_skills', 'approval_status') ? 'approval_status' : null,
            ]);

            if (Schema::hasColumn('student_skills', 'reviewed_by')) {
                $table->dropConstrainedForeignId('reviewed_by');
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
