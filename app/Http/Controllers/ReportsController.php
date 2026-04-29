<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Course;
use App\Models\Attendance;
use App\Models\RiskSetting;
use App\Models\Section;
use App\Models\StudentIntervention;
use App\Support\StudentRiskScorer;
use App\Exports\StudentsExport;
use App\Exports\FacultyExport;
use App\Exports\DepartmentsExport;
use App\Exports\FacultySectionProfilingExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;

class ReportsController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        return view('reports.index');
    }

    public function students()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $students = Student::with(['user', 'curriculums.department'])
            ->withCount(['skills', 'activities', 'affiliations', 'violations'])
            ->get();

        return view('reports.students', compact('students'));
    }

    public function exportStudents()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        return Excel::download(new StudentsExport, 'students.xlsx');
    }

    public function faculty()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $supportsSectionAssignments = Schema::hasTable('sections')
            && Schema::hasColumn('sections', 'faculty_id');

        $query = Faculty::with(['user', 'department', 'courses']);

        if ($supportsSectionAssignments) {
            $query->with('sections');
        }

        $faculty = $query->get();

        return view('reports.faculty', compact('faculty', 'supportsSectionAssignments'));
    }

    public function exportFaculty()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        return Excel::download(new FacultyExport, 'faculty.xlsx');
    }

    public function departments()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $departments = Department::with(['students', 'faculty'])->get();

        return view('reports.departments', compact('departments'));
    }

    public function exportDepartments()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        return Excel::download(new DepartmentsExport, 'departments.xlsx');
    }

    public function courses()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $courses = Course::with(['faculty', 'curricula'])->get();

        return view('reports.courses', compact('courses'));
    }

    public function atRisk()
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        return view('reports.profiling.at-risk', $this->buildProfilingReportData());
    }

    public function profileCompleteness()
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        return view('reports.profiling.profile-completeness', $this->buildProfilingReportData());
    }

    public function interventions()
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        return view('reports.profiling.interventions', $this->buildProfilingReportData());
    }

    public function studentTimeline()
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        return view('reports.profiling.student-timeline', $this->buildProfilingReportData());
    }

    public function facultyProfilingView()
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        return view('reports.profiling.faculty-profiling-view', $this->buildProfilingReportData());
    }

    public function facultyAtRisk()
    {
        abort_unless(auth()->user()->isFaculty(), 403);
        return view('faculty.reports.at-risk', $this->buildProfilingReportData($this->facultyScopedStudents()));
    }

    public function facultyProfileCompleteness()
    {
        abort_unless(auth()->user()->isFaculty(), 403);
        return view('faculty.reports.profile-completeness', $this->buildProfilingReportData($this->facultyScopedStudents()));
    }

    public function facultyInterventions()
    {
        abort_unless(auth()->user()->isFaculty(), 403);
        return view('faculty.reports.interventions', $this->buildProfilingReportData($this->facultyScopedStudents()));
    }

    public function facultyStudentTimeline()
    {
        abort_unless(auth()->user()->isFaculty(), 403);
        return view('faculty.reports.student-timeline', $this->buildProfilingReportData($this->facultyScopedStudents()));
    }

    public function facultyProfilingInsights()
    {
        abort_unless(auth()->user()->isFaculty(), 403);
        return view('faculty.reports.profiling-view', $this->buildProfilingReportData($this->facultyScopedStudents()));
    }

    public function facultySectionProfiling(Section $section)
    {
        abort_unless(auth()->user()->isFaculty(), 403);

        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();
        abort_unless((int) $section->faculty_id === (int) $faculty->id, 403);

        $students = $section->students()->with(['user', 'activities', 'skills', 'affiliations', 'violations', 'academicHistories'])->get();
        $data = $this->buildProfilingReportData($students);
        $data['section'] = $section->load('course');

        return view('faculty.reports.section-profiling', $data);
    }

    public function exportFacultySectionProfiling(Section $section)
    {
        abort_unless(auth()->user()->isFaculty(), 403);

        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();
        abort_unless((int) $section->faculty_id === (int) $faculty->id, 403);

        $students = $section->students()->with(['user', 'activities', 'skills', 'affiliations', 'violations', 'academicHistories'])->get();
        $data = $this->buildProfilingReportData($students);
        $section->load('course');

        return Excel::download(
            new FacultySectionProfilingExport($section, $data['completeness'], $data['interventionItems'], $data['interventions']),
            'section-report-' . str($section->section_name)->slug() . '.xlsx'
        );
    }

    public function adminControls()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $settings = RiskSetting::current();
        $pendingSkills = \App\Models\StudentSkill::where('approval_status', 'pending')->get();
        $pendingActivities = \App\Models\StudentActivity::where('approval_status', 'pending')->get();
        $unresolvedInterventions = StudentIntervention::whereIn('status', ['open', 'in_progress'])->count();
        $staleSubmissions = $pendingSkills->where('created_at', '<=', now()->subDays(3))->count()
            + $pendingActivities->where('created_at', '<=', now()->subDays(3))->count();
        $veryStaleSubmissions = $pendingSkills->where('created_at', '<=', now()->subDays(7))->count()
            + $pendingActivities->where('created_at', '<=', now()->subDays(7))->count();

        $duplicates = Student::select('student_id')
            ->whereNotNull('student_id')
            ->groupBy('student_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('student_id');
        $missingStudentIds = Student::whereNull('student_id')->orWhere('student_id', '')->count();
        $missingSections = Student::whereNull('section')->orWhere('section', '')->count();
        $invalidSections = Student::whereNotNull('section')
            ->where('section', '!=', '')
            ->whereNotIn('section', Section::pluck('section_name'))
            ->count();

        $resolvedInterventions = StudentIntervention::where('status', 'resolved')->count();

        return view('reports.profiling.admin-controls', compact(
            'settings',
            'staleSubmissions',
            'veryStaleSubmissions',
            'unresolvedInterventions',
            'duplicates',
            'missingStudentIds',
            'missingSections',
            'invalidSections',
            'resolvedInterventions'
        ));
    }

    public function updateAdminControls(\Illuminate\Http\Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $validated = $request->validate([
            'attendance_weight' => ['required', 'integer', 'min:0', 'max:100'],
            'violations_weight' => ['required', 'integer', 'min:0', 'max:100'],
            'low_grades_weight' => ['required', 'integer', 'min:0', 'max:100'],
            'incomplete_profile_weight' => ['required', 'integer', 'min:0', 'max:100'],
            'rejected_submissions_weight' => ['required', 'integer', 'min:0', 'max:100'],
            'high_risk_threshold' => ['required', 'integer', 'min:1', 'max:100'],
            'medium_risk_threshold' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $settings = RiskSetting::current();
        $settings->fill($validated);
        $settings->updated_by = auth()->id();
        $settings->save();

        return redirect()->route('reports.admin-controls')->with('success', 'Risk scoring settings updated.');
    }

    private function buildProfilingReportData(?Collection $students = null): array
    {
        $students = $students ?? Student::with(['user', 'activities', 'skills', 'affiliations', 'violations', 'interventions', 'academicHistories'])->get();

        $attendanceByStudent = Attendance::selectRaw('student_id, COUNT(*) as total, SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent_count')
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id');

        $settings = RiskSetting::current();

        $scored = $students->map(function (Student $student) use ($attendanceByStudent, $settings) {
            $attendance = $attendanceByStudent->get($student->id);
            $student->attendance_total = (int) ($attendance->total ?? 0);
            $student->attendance_absent = (int) ($attendance->absent_count ?? 0);
            $risk = StudentRiskScorer::score($student, $settings);

            return [
                'student' => $student,
                'risk_score' => $risk['score'],
                'risk_level' => $risk['level'],
                'risk_reasons' => $risk['reasons'],
            ];
        });

        $atRiskStudents = $scored
            ->filter(fn (array $item) => in_array($item['risk_level'], ['high', 'medium'], true))
            ->sortByDesc('risk_score')
            ->values();

        $completeness = $students->map(function (Student $student) {
            return [
                'student' => $student,
                'percentage' => $student->profileCompletionPercentage(),
                'missing' => $student->incompleteProfileItems(),
            ];
        })->sortBy('percentage')->values();

        $interventionItems = $scored->map(function (array $item) {
            return [
                'student' => $item['student'],
                'indicators' => $item['risk_reasons'],
                'recommended_action' => empty($item['risk_reasons']) ? 'No immediate intervention needed' : 'Adviser follow-up and documented support plan',
                'status' => empty($item['risk_reasons']) ? 'stable' : 'needs attention',
                'risk_score' => $item['risk_score'],
            ];
        })->filter(fn ($item) => ! empty($item['indicators']))->values();

        $interventions = StudentIntervention::with(['student.user', 'creator', 'assignee'])
            ->whereIn('student_id', $students->pluck('id'))
            ->latest()
            ->get();

        $timeline = $this->buildStudentTimeline($students);

        return compact('students', 'atRiskStudents', 'completeness', 'interventionItems', 'timeline', 'interventions');
    }

    private function facultyScopedStudents(): Collection
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();

        return Student::whereHas('sections', function ($query) use ($faculty) {
            $query->where('faculty_id', $faculty->id);
        })->with(['user', 'activities', 'skills', 'affiliations', 'violations', 'interventions', 'academicHistories'])->get();
    }

    private function buildStudentTimeline(Collection $students): Collection
    {
        $events = collect();

        foreach ($students as $student) {
            foreach ($student->activities as $activity) {
                $events->push([
                    'student' => $student,
                    'date' => $activity->date ?? $activity->created_at,
                    'type' => 'Activity',
                    'label' => $activity->activity_name,
                    'meta' => ucfirst((string) ($activity->approval_status ?? 'pending')),
                ]);
            }

            foreach ($student->skills as $skill) {
                $events->push([
                    'student' => $student,
                    'date' => $skill->reviewed_at ?? $skill->created_at,
                    'type' => 'Skill',
                    'label' => $skill->skill_name,
                    'meta' => ucfirst((string) ($skill->approval_status ?? 'pending')),
                ]);
            }

            foreach ($student->violations as $violation) {
                $events->push([
                    'student' => $student,
                    'date' => $violation->date ?? $violation->created_at,
                    'type' => 'Violation',
                    'label' => $violation->violation_type,
                    'meta' => ucfirst((string) ($violation->severity ?? 'n/a')),
                ]);
            }
        }

        return $events->filter(fn ($event) => ! empty($event['date']))
            ->sortByDesc('date')
            ->values();
    }
}
