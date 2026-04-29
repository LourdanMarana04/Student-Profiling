<?php

namespace App\Http\Controllers;

use App\Models\{Faculty, FacultyAnalytics, Attendance, Assignment, GradeEntry, GradeComponent, Section};
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeachingAnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard(): View
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();

        $analyticsData = FacultyAnalytics::where('faculty_id', $faculty->id)
            ->with(['course', 'section'])
            ->latest()
            ->get();

        $summary = [
            'totalCourses' => $analyticsData->pluck('course_id')->unique()->count(),
            'avgAttendanceRate' => $analyticsData->avg('avg_attendance_rate'),
            'avgStudentGrade' => $analyticsData->avg('avg_class_grade'),
            'avgEngagement' => $analyticsData->avg('student_engagement_score'),
        ];

        return view('faculty.analytics.dashboard', compact('analyticsData', 'summary'));
    }

    public function coursePerformance(Request $request)
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();

        $courseId = $request->course_id;

        $analytics = FacultyAnalytics::where([
            'faculty_id' => $faculty->id,
            'course_id' => $courseId,
        ])->with('section.students')->get();

        $performanceMetrics = $analytics->map(function($record) {
            return [
                'section' => $record->section->section_name,
                'students' => $record->total_students,
                'attendance' => $record->avg_attendance_rate,
                'avgGrade' => $record->avg_class_grade,
                'engagement' => $record->student_engagement_score,
                'completionRate' => $record->assignment_completion_rate,
            ];
        });

        return view('faculty.analytics.course-performance', compact('performanceMetrics'));
    }

    public function studentEngagement(Request $request)
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();

        $sectionId = $request->section_id;
        $section = Section::findOrFail($sectionId);

        $this->authorize('viewSection', $section);

        $students = $section->students()->get();

        $engagementData = $students->map(function($student) use ($section) {
            $submissions = AssignmentSubmission::whereHas('assignment', function($q) use ($section) {
                $q->where('section_id', $section->id);
            })->where('student_id', $student->id)->get();

            $attendance = Attendance::whereHas('schedule', function($q) use ($section) {
                $q->where('section_id', $section->id);
            })->where('student_id', $student->id)->get();

            $score = (
                ($submissions->count() / max(1, $submissions->count())) * 40 +
                (($attendance->where('status', 'present')->count() / max(1, $attendance->count())) * 100) * 30 +
                (($submissions->where('status', 'graded')->avg('score') ?? 0) / 100 * 100) * 30
            );

            return [
                'name' => $student->full_name,
                'submissions' => $submissions->count(),
                'attendance' => $attendance->where('status', 'present')->count() . '/' . $attendance->count(),
                'engagement_score' => round($score, 2),
            ];
        });

        return view('faculty.analytics.student-engagement', compact('engagementData', 'section'));
    }

    public function generateInsights(Request $request)
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();

        $sectionId = $request->section_id;
        $analytics = FacultyAnalytics::where('section_id', $sectionId)->first();

        $insights = [
            'attendance' => $this->analyzeAttendance($analytics),
            'grades' => $this->analyzeGrades($analytics),
            'engagement' => $this->analyzeEngagement($analytics),
            'recommendations' => $this->generateRecommendations($analytics),
        ];

        return response()->json($insights);
    }

    private function analyzeAttendance($analytics)
    {
        if ($analytics->avg_attendance_rate < 75) {
            return "Attendance rate is below optimal. Consider implementing interventions or reaching out to absent students.";
        }
        return "Attendance rate is healthy at " . $analytics->avg_attendance_rate . "%.";
    }

    private function analyzeGrades($analytics)
    {
        if ($analytics->avg_class_grade < 2.0) {
            return "Average grades are low. Consider reviewing course difficulty, assessment methods, or providing additional support.";
        } else if ($analytics->avg_class_grade > 3.5) {
            return "Students are performing well. Consider challenging them with advanced material.";
        }
        return "Grade distribution appears normal.";
    }

    private function analyzeEngagement($analytics)
    {
        if ($analytics->student_engagement_score < 50) {
            return "Student engagement is low. Try interactive teaching methods or discussion forums.";
        }
        return "Student engagement is good.";
    }

    private function generateRecommendations($analytics)
    {
        $recommendations = [];

        if ($analytics->assignment_completion_rate < 80) {
            $recommendations[] = "Encourage students to complete assignments on time.";
        }

        if ($analytics->avg_attendance_rate < 80) {
            $recommendations[] = "Address attendance issues early in the semester.";
        }

        if ($analytics->avg_class_grade < 2.5) {
            $recommendations[] = "Consider offering tutoring or additional study sessions.";
        }

        return $recommendations;
    }

    public function curriculumInsights()
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();

        $courseAnalytics = FacultyAnalytics::where('faculty_id', $faculty->id)
            ->groupBy('course_id')
            ->selectRaw('course_id, AVG(avg_class_grade) as avg_grade, AVG(student_engagement_score) as avg_engagement')
            ->get();

        $insights = $courseAnalytics->map(function($record) {
            return [
                'course' => $record->course->course_name,
                'avg_grade' => $record->avg_grade,
                'engagement' => $record->avg_engagement,
                'suggestion' => $this->suggestCurriculumImprovement($record),
            ];
        });

        return view('faculty.analytics.curriculum-insights', compact('insights'));
    }

    private function suggestCurriculumImprovement($record)
    {
        if ($record->avg_grade < 2.0) {
            return "Consider revising course content or assessment difficulty.";
        } else if ($record->avg_engagement < 50) {
            return "Try incorporating more interactive or practical components.";
        }
        return "Course is performing well. Maintain current approach.";
    }
}
