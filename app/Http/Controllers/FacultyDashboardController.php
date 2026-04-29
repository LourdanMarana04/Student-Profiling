<?php

namespace App\Http\Controllers;

use App\Exports\FacultyCourseRosterExport;
use App\Models\{Faculty, Course, CourseCommunication, FacultyAlertSubscription, RiskSetting, StudentAcademicHistory, StudentIntervention};
use App\Support\StudentRiskScorer;
use Illuminate\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class FacultyDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();
        [$academicYear, $semester] = $this->resolveDashboardAcademicPeriod($faculty);
        $supportsSectionAssignments = Schema::hasColumn('sections', 'faculty_id') && Schema::hasTable('section_student');
        $supportsStudentSections = Schema::hasColumn('students', 'section');

        $currentCourses = $faculty->courses()
            ->wherePivot('academic_year', $academicYear)
            ->wherePivot('semester', $semester)
            ->with('department')
            ->get();

        $courseStats = collect($currentCourses)->map(function ($course) use ($faculty, $academicYear, $semester, $supportsStudentSections) {
            $rosterGroups = $this->groupStudentsForCourse($faculty, $course, $academicYear, $semester, $supportsStudentSections);

            return [
                'id' => $course->id,
                'code' => $course->course_code,
                'name' => $course->course_name,
                'department' => $course->department?->name,
                'sections' => $rosterGroups->count(),
                'sectionNames' => $rosterGroups->keys()->values(),
                'totalStudents' => $rosterGroups->flatten(1)->unique('id')->count(),
                'rosterGroups' => $rosterGroups,
            ];
        });

        $assignedSections = $supportsSectionAssignments
            ? $faculty->sections()
                ->with(['course', 'students', 'schedules'])
                ->where('semester', $semester)
                ->orderBy('section_name')
                ->get()
            : $courseStats
                ->flatMap(function ($stat) {
                    return collect($stat['rosterGroups'])->map(function ($students, $sectionName) use ($stat) {
                        return (object) [
                            'section_name' => $sectionName,
                            'year_level' => optional($students->first())->year_level,
                            'course' => (object) [
                                'course_code' => $stat['code'],
                                'course_name' => $stat['name'],
                            ],
                            'students_count' => $students->count(),
                            'students' => $students,
                        ];
                    });
                })
                ->values();

        $recentAnnouncements = CourseCommunication::where('faculty_id', $faculty->id)
            ->whereIn('course_id', $currentCourses->pluck('id'))
            ->latest()
            ->limit(5)
            ->get();

        $settings = RiskSetting::current();
        $sectionRiskQueue = $assignedSections->map(function ($section) use ($settings) {
            $students = collect($section->students ?? [])->map(function ($student) use ($settings) {
                $student->loadMissing(['violations', 'interventions', 'activities', 'skills', 'academicHistories']);
                $risk = StudentRiskScorer::score($student, $settings);
                $currentAvg = (float) ($student->academicHistories->avg('grade') ?? 0);
                $previousAvg = (float) ($student->academicHistories
                    ->sortByDesc(fn ($h) => $h->academic_year.'-'.$h->semester)
                    ->slice(1)
                    ->avg('grade') ?? 0);

                return [
                    'student' => $student,
                    'risk_score' => $risk['score'],
                    'risk_level' => $risk['level'],
                    'reasons' => $risk['reasons'],
                    'compare' => [
                        'grade_delta' => round($currentAvg - $previousAvg, 2),
                        'violations' => $student->violations->count(),
                        'completion' => $student->profileCompletionPercentage(),
                    ],
                ];
            })->sortByDesc('risk_score')->values();

            return [
                'section' => $section,
                'students' => $students,
            ];
        })->values();

        $subscription = FacultyAlertSubscription::firstOrCreate(
            ['faculty_id' => $faculty->id],
            ['notify_high_risk' => true, 'notify_medium_risk' => false, 'minimum_risk_score' => 70, 'is_enabled' => true]
        );

        $interventionStats = StudentIntervention::whereIn('student_id', $assignedSections->flatMap(fn ($s) => $s->students->pluck('id')))
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('faculty.dashboard', compact(
            'faculty', 'currentCourses',
            'courseStats', 'recentAnnouncements',
            'assignedSections', 'academicYear', 'semester',
            'sectionRiskQueue', 'subscription', 'interventionStats'
        ));
    }

    public function exportCourseRoster(Course $course)
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();
        [$academicYear, $semester] = $this->resolveDashboardAcademicPeriod($faculty);
        $supportsStudentSections = Schema::hasColumn('students', 'section');

        abort_unless(
            $faculty->courses()
                ->where('courses.id', $course->id)
                ->wherePivot('academic_year', $academicYear)
                ->wherePivot('semester', $semester)
                ->exists(),
            403
        );

        $groupedStudents = $this->groupStudentsForCourse($faculty, $course, $academicYear, $semester, $supportsStudentSections);

        return Excel::download(
            new FacultyCourseRosterExport($faculty, $course, $academicYear, $semester, $groupedStudents),
            'class-list-' . str($course->course_code)->slug() . '.xlsx'
        );
    }

    private function groupStudentsForCourse(Faculty $faculty, Course $course, string $academicYear, int $semester, bool $supportsStudentSections): Collection
    {
        $supportsSectionAssignments = Schema::hasColumn('sections', 'faculty_id') && Schema::hasTable('section_student');

        if ($supportsSectionAssignments) {
            $assignedSections = $faculty->sections()
                ->with('students.user')
                ->where('semester', $semester)
                ->where('course_id', $course->id)
                ->get();

            return $assignedSections
                ->mapWithKeys(function ($section) {
                    return [$section->section_name => $section->students->sortBy([
                        ['year_level', 'asc'],
                        ['section', 'asc'],
                        ['last_name', 'asc'],
                        ['first_name', 'asc'],
                    ])->values()];
                })
                ->sortKeys();
        }

        $histories = StudentAcademicHistory::query()
            ->with(['student.user'])
            ->where('course_id', $course->id)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->get();

        $students = $histories
            ->map(fn ($history) => $history->student)
            ->filter()
            ->unique('id')
            ->sortBy([
                ['year_level', 'asc'],
                ['section', 'asc'],
                ['last_name', 'asc'],
                ['first_name', 'asc'],
            ])
            ->values();

        if ($students->isEmpty()) {
            return collect();
        }

        return $students
            ->groupBy(function ($student) use ($supportsStudentSections) {
                if ($supportsStudentSections && filled($student->section)) {
                    return $student->section;
                }

                return 'Unassigned Section';
            })
            ->sortKeys();
    }

    private function resolveDashboardAcademicPeriod(Faculty $faculty): array
    {
        [$currentAcademicYear, $currentSemester] = $faculty->currentAcademicPeriod();

        $hasCurrentAssignments = $faculty->courses()
            ->wherePivot('academic_year', $currentAcademicYear)
            ->wherePivot('semester', $currentSemester)
            ->exists();

        if ($hasCurrentAssignments) {
            return [$currentAcademicYear, $currentSemester];
        }

        $latestAssignment = DB::table('course_faculty')
            ->where('faculty_id', $faculty->id)
            ->orderByDesc('academic_year')
            ->orderByDesc('semester')
            ->first(['academic_year', 'semester']);

        if ($latestAssignment !== null) {
            return [$latestAssignment->academic_year, (int) $latestAssignment->semester];
        }

        return [$currentAcademicYear, $currentSemester];
    }
}
