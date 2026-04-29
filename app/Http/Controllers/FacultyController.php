<?php

namespace App\Http\Controllers;

use App\Exports\FacultyCvExport;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class FacultyController extends Controller
{
    /**
     * Display a listing of the faculty.
     */
    public function index()
    {
        $this->ensureCanManageFaculty();

        $faculty = Faculty::with('user', 'department')->paginate(10);
        return view('faculty.index', compact('faculty'));
    }

    /**
     * Show the form for creating a new faculty member.
     */
    public function create()
    {
        $this->ensureCanManageFaculty();

        [$academicYear, $semester] = (new Faculty())->currentAcademicPeriod();
        $departments = Department::where('status', 'active')->get();
        $courses = Course::where('status', 'active')->with('department')->orderBy('course_code')->get();
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $supportsSectionAssignments = $this->supportsSectionAssignments();
        $sections = $supportsSectionAssignments
            ? Section::with('course.department')
                ->where('status', 'active')
                ->where('semester', $semester)
                ->orderBy('section_name')
                ->get()
            : collect();
        $courseStudentAssignments = [];

        return view('faculty.create', compact(
            'departments',
            'courses',
            'sections',
            'students',
            'academicYear',
            'semester',
            'supportsSectionAssignments',
            'courseStudentAssignments'
        ));
    }

    /**
     * Store a newly created faculty member in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->ensureCanManageFaculty();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'faculty_id' => 'required|string|max:255|unique:faculty',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'office' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'academic_year' => 'required|string|max:20',
            'semester' => 'required|integer|min:1|max:2',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
            'course_students' => 'nullable|array',
            'course_students.*' => 'nullable|array',
            'course_students.*.*' => 'exists:students,id',
            'section_ids' => 'nullable|array',
            'section_ids.*' => 'exists:sections,id',
            'section_students' => 'nullable|array',
            'section_students.*' => 'nullable|array',
            'section_students.*.*' => 'exists:students,id',
        ]);

        // Create user first
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'faculty',
        ]);

        // Create faculty profile
        $faculty = Faculty::create([
            'user_id' => $user->id,
            'faculty_id' => $request->faculty_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'office' => $request->office,
            'specialization' => $request->specialization,
            'department_id' => $request->department_id,
            'status' => 'active',
        ]);

        $this->syncFacultyAssignments($faculty, $request);

        AuditLog::recordIfAdmin(
            'faculty_created',
            'faculty',
            $faculty->id,
            $faculty->faculty_id,
            "Created faculty profile for {$faculty->full_name} ({$faculty->faculty_id}).",
            null,
            [
                'faculty_id' => $faculty->faculty_id,
                'full_name' => $faculty->full_name,
                'email' => $faculty->email,
                'department_id' => $faculty->department_id,
                'status' => $faculty->status,
                'academic_year' => $request->academic_year,
                'semester' => (int) $request->semester,
                'course_ids' => collect($request->input('course_ids', []))->map(fn ($id) => (int) $id)->values()->all(),
                'section_ids' => collect($request->input('section_ids', []))->map(fn ($id) => (int) $id)->values()->all(),
            ]
        );

        return redirect()->route('faculty.show', $faculty)
            ->with('success', 'Faculty member created successfully.');
    }

    /**
     * Display the specified faculty member.
     */
    public function show(Faculty $faculty)
    {
        $this->ensureCanViewFaculty($faculty);

        $faculty->load([
            'user',
            'department',
            'courses.department',
        ]);

        $supportsSectionAssignments = $this->supportsSectionAssignments();

        if ($supportsSectionAssignments) {
            $faculty->load([
                'sections.course.department',
                'sections.students',
            ]);
        }

        [$academicYear, $semester] = $faculty->currentAcademicPeriod();

        $teachingCourses = $faculty->courses()
            ->with('department')
            ->wherePivot('academic_year', $academicYear)
            ->wherePivot('semester', $semester)
            ->get();

        // If there are no assignments for the current academic period, fall back to any assigned courses
        $teachingFallback = false;
        if ($teachingCourses->isEmpty()) {
            $teachingCourses = $faculty->courses()->with('department')->get();
            $teachingFallback = true;
        }

        $allAssignedCourses = $faculty->courses()
            ->withPivot('academic_year', 'semester')
            ->get()
            ->groupBy(['pivot.academic_year', 'pivot.semester']);

        $assignedSections = $supportsSectionAssignments
            ? $faculty->sections()
                ->with(['course.department', 'students'])
                ->where('semester', $semester)
                ->orderBy('section_name')
                ->get()
            : collect();

        // If there are no sections for the current semester, show any sections assigned to the faculty
        $sectionsFallback = false;
        if ($supportsSectionAssignments && $assignedSections->isEmpty()) {
            $assignedSections = $faculty->sections()
                ->with(['course.department', 'students'])
                ->orderBy('section_name')
                ->get();
            $sectionsFallback = true;
        }


        $assignedStudents = $supportsSectionAssignments
            ? $faculty->assignedStudents()
                ->with('sections.course')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get()
            : collect();

        // If no students are returned for the current semester, fetch any students assigned to faculty sections
        $studentsFallback = false;
        if ($supportsSectionAssignments && $assignedStudents->isEmpty()) {
            $assignedStudents = Student::whereHas('sections', function ($q) use ($faculty) {
                $q->where('faculty_id', $faculty->id);
            })->with('sections.course')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();
            $studentsFallback = true;
        }

        return view('faculty.show', compact(
            'faculty',
            'teachingCourses',
            'academicYear',
            'semester',
            'allAssignedCourses',
            'assignedSections',
            'assignedStudents',
            'teachingFallback',
            'sectionsFallback',
            'studentsFallback'
        ));
    }

    public function exportCv(Faculty $faculty)
    {
        $this->ensureCanViewFaculty($faculty);

        return Excel::download(
            new FacultyCvExport($faculty),
            'faculty-cv-' . str($faculty->full_name)->slug() . '.xlsx'
        );
    }

    /**
     * Show the form for editing the specified faculty member.
     */
    public function edit(Faculty $faculty)
    {
        $this->ensureCanManageFaculty();

        $departments = Department::where('status', 'active')->get();
        $courses = Course::where('status', 'active')->with('department')->get();
        [$academicYear, $semester] = $faculty->currentAcademicPeriod();
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $supportsSectionAssignments = $this->supportsSectionAssignments();

        $sections = $supportsSectionAssignments
            ? Section::with(['course.department', 'students'])
                ->where('status', 'active')
                ->where('semester', $semester)
                ->orderBy('section_name')
                ->get()
            : collect();

        $assignedCourses = $faculty->courses()
            ->wherePivot('academic_year', $academicYear)
            ->wherePivot('semester', $semester)
            ->get()
            ->pluck('id')
            ->all();

        $assignedSections = $supportsSectionAssignments
            ? $faculty->sections()
                ->where('semester', $semester)
                ->pluck('id')
                ->all()
            : [];

        $sectionStudentAssignments = $supportsSectionAssignments
            ? $faculty->sections()
                ->with('students')
                ->where('semester', $semester)
                ->get()
                ->mapWithKeys(fn ($section) => [$section->id => $section->students->pluck('id')->all()])
                ->all()
            : [];

        $courseStudentAssignments = StudentAcademicHistory::query()
            ->whereIn('course_id', $assignedCourses)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->get()
            ->groupBy('course_id')
            ->map(fn ($records) => $records->pluck('student_id')->unique()->values()->all())
            ->all();

        return view('faculty.edit', compact(
            'faculty',
            'departments',
            'courses',
            'sections',
            'students',
            'academicYear',
            'semester',
            'assignedCourses',
            'assignedSections',
            'sectionStudentAssignments',
            'supportsSectionAssignments',
            'courseStudentAssignments'
        ));
    }

    /**
     * Update the specified faculty member in storage.
     */
    public function update(Request $request, Faculty $faculty): RedirectResponse
    {
        $this->ensureCanManageFaculty();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($faculty->user_id)],
            'faculty_id' => ['required', 'string', 'max:255', Rule::unique('faculty')->ignore($faculty->id)],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'office' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'status' => 'required|in:active,inactive,on_leave',
            'academic_year' => 'required|string|max:20',
            'semester' => 'required|integer|min:1|max:2',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
            'course_students' => 'nullable|array',
            'course_students.*' => 'nullable|array',
            'course_students.*.*' => 'exists:students,id',
            'section_ids' => 'nullable|array',
            'section_ids.*' => 'exists:sections,id',
            'section_students' => 'nullable|array',
            'section_students.*' => 'nullable|array',
            'section_students.*.*' => 'exists:students,id',
        ]);

        $oldValues = [
            'faculty_id' => $faculty->faculty_id,
            'first_name' => $faculty->first_name,
            'last_name' => $faculty->last_name,
            'email' => $faculty->email,
            'phone' => $faculty->phone,
            'office' => $faculty->office,
            'specialization' => $faculty->specialization,
            'department_id' => $faculty->department_id,
            'status' => $faculty->status,
        ];

        // Update user
        $faculty->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update faculty
        $faculty->update([
            'faculty_id' => $request->faculty_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'office' => $request->office,
            'specialization' => $request->specialization,
            'department_id' => $request->department_id,
            'status' => $request->status,
        ]);

        $this->syncFacultyAssignments($faculty, $request);

        AuditLog::recordIfAdmin(
            'faculty_updated',
            'faculty',
            $faculty->id,
            $faculty->faculty_id,
            "Updated faculty profile for {$faculty->full_name} ({$faculty->faculty_id}).",
            $oldValues,
            [
                'faculty_id' => $faculty->faculty_id,
                'first_name' => $faculty->first_name,
                'last_name' => $faculty->last_name,
                'email' => $faculty->email,
                'phone' => $faculty->phone,
                'office' => $faculty->office,
                'specialization' => $faculty->specialization,
                'department_id' => $faculty->department_id,
                'status' => $faculty->status,
                'academic_year' => $request->academic_year,
                'semester' => (int) $request->semester,
                'course_ids' => collect($request->input('course_ids', []))->map(fn ($id) => (int) $id)->values()->all(),
                'section_ids' => collect($request->input('section_ids', []))->map(fn ($id) => (int) $id)->values()->all(),
            ]
        );

        return redirect()->route('faculty.show', $faculty)
            ->with('success', 'Faculty member updated successfully.');
    }

    /**
     * Assign a course to the faculty member.
     */
    public function assignCourse(Request $request, Faculty $faculty)
    {
        $this->ensureCanManageFaculty();

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_year' => 'required|string',
            'semester' => 'required|integer|min:1|max:2',
        ]);

        // Check if course is already assigned for this academic year and semester
        $existingAssignment = $faculty->courses()
            ->wherePivot('course_id', $request->course_id)
            ->wherePivot('academic_year', $request->academic_year)
            ->wherePivot('semester', $request->semester)
            ->exists();

        if ($existingAssignment) {
            return response()->json(['error' => 'Course is already assigned for this academic year and semester'], 422);
        }

        $faculty->courses()->attach($request->course_id, [
            'academic_year' => $request->academic_year,
            'semester' => $request->semester,
        ]);

        AuditLog::recordIfAdmin(
            'faculty_course_assigned',
            'faculty',
            $faculty->id,
            $faculty->faculty_id,
            "Assigned course {$request->course_id} to faculty {$faculty->full_name}.",
            null,
            [
                'course_id' => (int) $request->course_id,
                'academic_year' => $request->academic_year,
                'semester' => (int) $request->semester,
            ]
        );

        return response()->json(['success' => 'Course assigned successfully']);
    }

    /**
     * Synchronize faculty course, section, and section student assignments.
     */
    private function syncFacultyAssignments(Faculty $faculty, Request $request): void
    {
        $academicYear = $request->string('academic_year')->toString();
        $semester = (int) $request->input('semester');
        $supportsSectionAssignments = $this->supportsSectionAssignments();
        $validSectionIds = collect();

        if ($supportsSectionAssignments) {
            $sectionIds = collect($request->input('section_ids', []))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $validSectionIds = Section::whereIn('id', $sectionIds->all())
                ->where('semester', $semester)
                ->pluck('id');
        }

        $courseIds = collect($request->input('course_ids', []))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($supportsSectionAssignments) {
            $courseIds = $courseIds
                ->merge(Section::whereIn('id', $validSectionIds->all())->pluck('course_id'))
                ->unique()
                ->values();

            $currentlyAssignedSectionIds = $faculty->sections()
                ->where('semester', $semester)
                ->pluck('id');

            Section::whereIn('id', $currentlyAssignedSectionIds->diff($validSectionIds)->all())
                ->update(['faculty_id' => null]);

            if ($validSectionIds->isNotEmpty()) {
                Section::whereIn('id', $validSectionIds->all())
                    ->update(['faculty_id' => $faculty->id]);
            }

            $selectedSectionIds = $faculty->sections()
                ->where('semester', $semester)
                ->pluck('id');

            foreach ($selectedSectionIds as $sectionId) {
                $section = Section::with('students')->find($sectionId);

                if ($section === null) {
                    continue;
                }

                // Keep section membership authoritative: selecting a section assigns all of its students.
                $studentIds = $section->students->pluck('id')->unique()->values()->all();
                $section->students()->sync($studentIds);
                $section->update(['enrolled_count' => count($studentIds)]);
            }
        }

        $existingCourseIds = $faculty->courses()
            ->wherePivot('academic_year', $academicYear)
            ->wherePivot('semester', $semester)
            ->pluck('courses.id');

        $courseIdsToDetach = $existingCourseIds->diff($courseIds)->all();
        if (! empty($courseIdsToDetach)) {
            $faculty->courses()
                ->wherePivot('academic_year', $academicYear)
                ->wherePivot('semester', $semester)
                ->detach($courseIdsToDetach);
        }

        $courseIdsToAttach = $courseIds->diff($existingCourseIds)->all();
        foreach ($courseIdsToAttach as $courseId) {
            $faculty->courses()->attach($courseId, [
                'academic_year' => $academicYear,
                'semester' => $semester,
            ]);
        }

        $this->syncCourseStudentAssignments($request, $courseIds, $academicYear, $semester);
    }

    private function syncCourseStudentAssignments(Request $request, $courseIds, string $academicYear, int $semester): void
    {
        $selectedCourseIds = collect($courseIds)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        foreach ($selectedCourseIds as $courseId) {
            $studentIds = collect($request->input("course_students.$courseId", []))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $existingStudentIds = StudentAcademicHistory::query()
                ->where('course_id', $courseId)
                ->where('academic_year', $academicYear)
                ->where('semester', $semester)
                ->pluck('student_id');

            $studentsToRemove = $existingStudentIds->diff($studentIds)->all();

            if (! empty($studentsToRemove)) {
                StudentAcademicHistory::query()
                    ->where('course_id', $courseId)
                    ->where('academic_year', $academicYear)
                    ->where('semester', $semester)
                    ->whereIn('student_id', $studentsToRemove)
                    ->delete();
            }

            $course = Course::find($courseId);

            if ($course === null) {
                continue;
            }

            foreach ($studentIds as $studentId) {
                StudentAcademicHistory::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'course_id' => $courseId,
                        'academic_year' => $academicYear,
                        'semester' => $semester,
                    ],
                    [
                        'units' => $course->credits,
                    ]
                );
            }
        }
    }

    /**
     * Remove a course assignment from the faculty member.
     */
    public function removeCourse(Request $request, Faculty $faculty)
    {
        $this->ensureCanManageFaculty();

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_year' => 'required|string',
            'semester' => 'required|integer|min:1|max:2',
        ]);

        $faculty->courses()->wherePivot('course_id', $request->course_id)
            ->wherePivot('academic_year', $request->academic_year)
            ->wherePivot('semester', $request->semester)
            ->detach();

        AuditLog::recordIfAdmin(
            'faculty_course_removed',
            'faculty',
            $faculty->id,
            $faculty->faculty_id,
            "Removed course {$request->course_id} from faculty {$faculty->full_name}.",
            [
                'course_id' => (int) $request->course_id,
                'academic_year' => $request->academic_year,
                'semester' => (int) $request->semester,
            ],
            null
        );

        return response()->json(['success' => 'Course assignment removed successfully']);
    }

    /**
     * Ensure the user can manage faculty records.
     */
    private function ensureCanManageFaculty(): void
    {
        abort_unless(auth()->user()?->canManageFaculty(), Response::HTTP_FORBIDDEN);
    }

    /**
     * Ensure the user can view the faculty profile.
     */
    private function ensureCanViewFaculty(Faculty $faculty): void
    {
        // Allow if authenticated user is the faculty member themselves, or if user is admin/staff
        $canView = auth()->user() && (
            ($faculty->user_id === auth()->id()) ||
            auth()->user()->canManageFaculty()
        );

        abort_unless($canView, Response::HTTP_FORBIDDEN);
    }

    private function supportsSectionAssignments(): bool
    {
        return Schema::hasColumn('sections', 'faculty_id') && Schema::hasTable('section_student');
    }
}
