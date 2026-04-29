<?php

namespace App\Support;

use App\Models\RiskSetting;
use App\Models\Student;

class StudentRiskScorer
{
    /**
     * @return array{score:int, level:string, reasons:array<int,string>}
     */
    public static function score(Student $student, ?RiskSetting $settings = null): array
    {
        $settings = $settings ?? RiskSetting::current();

        $score = 0;
        $reasons = [];

        $attendanceTotal = $student->attendance_total ?? 0;
        $attendanceAbsent = $student->attendance_absent ?? 0;
        $absenceRate = $attendanceTotal > 0 ? ($attendanceAbsent / $attendanceTotal) * 100 : 0;
        if ($absenceRate >= 20) {
            $score += (int) $settings->attendance_weight;
            $reasons[] = 'High absentee rate';
        }

        if (($student->violations_count ?? $student->violations->count()) > 0) {
            $score += (int) $settings->violations_weight;
            $reasons[] = 'Has violation records';
        }
        $violationsCount = (int) ($student->violations_count ?? $student->violations->count());
        if ($violationsCount > 1) {
            // Add extra pressure for repeated violations (beyond the first one).
            $score += min(15, ($violationsCount - 1) * 5);
            $reasons[] = 'Multiple violation incidents';
        }

        $avgGrade = $student->academicHistories->avg('grade');
        if ($avgGrade !== null && $avgGrade < 80) {
            $score += (int) $settings->low_grades_weight;
            $reasons[] = 'Low grade trend';
        }

        if ($student->profileCompletionPercentage() < 70) {
            $score += (int) $settings->incomplete_profile_weight;
            $reasons[] = 'Incomplete profile';
        }

        $rejectedSubmissions = ($student->skills->where('approval_status', 'rejected')->count())
            + ($student->activities->where('approval_status', 'rejected')->count());
        if ($rejectedSubmissions > 0) {
            $score += (int) $settings->rejected_submissions_weight;
            $reasons[] = 'Rejected submissions need correction';
        }

        $interventions = $student->interventions ?? collect();
        $openInterventions = $interventions->whereIn('status', ['open', 'in_progress'])->count();
        $resolvedInterventions = $interventions->where('status', 'resolved')->count();

        if ($openInterventions > 0) {
            // Open/in-progress interventions indicate active student support risk.
            $score += min(20, $openInterventions * 10);
            $reasons[] = 'Has active intervention cases';
        } elseif ($resolvedInterventions > 0) {
            // Resolved history still contributes a small residual signal.
            $score += min(10, $resolvedInterventions * 3);
            $reasons[] = 'Has intervention history';
        }

        $score = min(100, $score);
        $level = 'low';

        if ($score >= (int) $settings->high_risk_threshold) {
            $level = 'high';
        } elseif ($score >= (int) $settings->medium_risk_threshold) {
            $level = 'medium';
        }

        return [
            'score' => $score,
            'level' => $level,
            'reasons' => $reasons,
        ];
    }
}
