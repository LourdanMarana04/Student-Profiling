<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_skills', function (Blueprint $table) {
            if (! Schema::hasColumn('student_skills', 'evidence_link')) {
                $table->string('evidence_link')->nullable()->after('skill_name');
            }

            if (! Schema::hasColumn('student_skills', 'evidence_path')) {
                $table->string('evidence_path')->nullable()->after('evidence_link');
            }
        });

        Schema::table('student_activities', function (Blueprint $table) {
            if (! Schema::hasColumn('student_activities', 'evidence_link')) {
                $table->string('evidence_link')->nullable()->after('activity_name');
            }

            if (! Schema::hasColumn('student_activities', 'evidence_path')) {
                $table->string('evidence_path')->nullable()->after('evidence_link');
            }
        });

        if (! Schema::hasTable('student_interventions')) {
            Schema::create('student_interventions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action_type');
                $table->text('notes')->nullable();
                $table->date('due_date')->nullable();
                $table->enum('status', ['open', 'in_progress', 'resolved'])->default('open');
                $table->date('resolved_at')->nullable();
                $table->string('outcome')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('student_correction_requests')) {
            Schema::create('student_correction_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
                $table->string('field_name');
                $table->text('current_value')->nullable();
                $table->text('requested_value');
                $table->text('reason')->nullable();
                $table->enum('status', ['open', 'in_progress', 'resolved', 'rejected'])->default('open');
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('review_notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('risk_settings')) {
            Schema::create('risk_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('attendance_weight')->default(25);
                $table->unsignedInteger('violations_weight')->default(25);
                $table->unsignedInteger('low_grades_weight')->default(20);
                $table->unsignedInteger('incomplete_profile_weight')->default(20);
                $table->unsignedInteger('rejected_submissions_weight')->default(10);
                $table->unsignedInteger('high_risk_threshold')->default(70);
                $table->unsignedInteger('medium_risk_threshold')->default(40);
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('faculty_alert_subscriptions')) {
            Schema::create('faculty_alert_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('faculty_id')->constrained('faculty')->cascadeOnDelete();
                $table->boolean('notify_high_risk')->default(true);
                $table->boolean('notify_medium_risk')->default(false);
                $table->unsignedInteger('minimum_risk_score')->default(70);
                $table->boolean('is_enabled')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('faculty_alert_subscriptions')) {
            Schema::drop('faculty_alert_subscriptions');
        }

        if (Schema::hasTable('risk_settings')) {
            Schema::drop('risk_settings');
        }

        if (Schema::hasTable('student_correction_requests')) {
            Schema::drop('student_correction_requests');
        }

        if (Schema::hasTable('student_interventions')) {
            Schema::drop('student_interventions');
        }

        Schema::table('student_activities', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('student_activities', 'evidence_link')) {
                $columns[] = 'evidence_link';
            }
            if (Schema::hasColumn('student_activities', 'evidence_path')) {
                $columns[] = 'evidence_path';
            }
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('student_skills', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('student_skills', 'evidence_link')) {
                $columns[] = 'evidence_link';
            }
            if (Schema::hasColumn('student_skills', 'evidence_path')) {
                $columns[] = 'evidence_path';
            }
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
