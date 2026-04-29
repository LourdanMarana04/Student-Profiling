<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Curriculum;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\FacultyAlertSubscription;
use App\Models\RiskSetting;
use App\Models\Student;
use App\Models\StudentAcademicHistory;
use App\Models\StudentCorrectionRequest;
use App\Models\StudentCurriculum;
use App\Models\StudentIntervention;
use App\Models\StudentViolation;
use App\Models\User;
use App\Support\StudentRiskScorer;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->ensureCanManageStudents();

        $students = Student::with('user')->paginate(10);
        return view('students.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->ensureCanManageStudents();

        return view('students.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->ensureCanManageStudents();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'student_id' => 'required|string|max:255|unique:students',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'year_level' => 'required|integer|min:1|max:4',
            'section' => 'required|string|max:255',
        ]);

        // Create user first
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
        ]);

        // Create student profile
        $student = Student::create([
            'user_id' => $user->id,
            'student_id' => $request->student_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'year_level' => $request->year_level,
            'section' => $request->section,
        ]);

        AuditLog::recordIfAdmin(
            'student_created',
            'student',
            $student->id,
            $student->student_id,
            "Created student profile for {$student->full_name} ({$student->student_id}).",
            null,
            [
                'student_id' => $student->student_id,
                'full_name' => $student->full_name,
                'email' => $student->email,
                'year_level' => $student->year_level,
                'section' => $student->section,
                'status' => $student->status,
            ]
        );

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        $this->ensureCanViewStudent($student);

        $student->load([
            'user',
            'academicHistories.course.faculty',
            'activities',
            'violations',
            'skills',
            'affiliations',
            'curriculums.department',
            'interventions.creator',
            'interventions.assignee',
            'correctionRequests.requester',
            'correctionRequests.reviewer',
        ]);

        $departments = Department::orderBy('name')->get();
        $risk = StudentRiskScorer::score($student, RiskSetting::current());

        $roadmapActions = collect();
        if ($student->profileCompletionPercentage() < 100) {
            $roadmapActions->push('Complete missing profile fields');
        }
        if ($student->skills->where('approval_status', 'approved')->count() < 2) {
            $roadmapActions->push('Submit at least 2 approved skills');
        }
        if ($student->affiliations->count() < 1) {
            $roadmapActions->push('Add at least one active affiliation');
        }
        if ($student->phone === null || $student->address === null) {
            $roadmapActions->push('Add contact and emergency-ready details');
        }

        $insight = 'Profile strength improving';
        if ($risk['level'] === 'high') {
            $insight = 'Risk indicators detected. Adviser follow-up recommended.';
        } elseif ($risk['level'] === 'medium') {
            $insight = 'Moderate risk indicators detected. Keep profile updated.';
        }

        return view('students.show', compact('student', 'departments', 'risk', 'roadmapActions', 'insight'));
    }

    public function storeIntervention(Request $request, Student $student): RedirectResponse
    {
        abort_unless(auth()->user()?->isFaculty() || auth()->user()?->canManageStudents(), Response::HTTP_FORBIDDEN);

        if (auth()->user()->isFaculty()) {
            $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();
            abort_unless(
                $student->sections()->where('faculty_id', $faculty->id)->exists(),
                Response::HTTP_FORBIDDEN
            );
        }

        $validated = $request->validate([
            'action_type' => ['required', 'in:called_guardian,advised_student,referred_to_counselor,other'],
            'notes' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
        ]);

        StudentIntervention::create([
            'student_id' => $student->id,
            'created_by' => auth()->id(),
            'assigned_to' => auth()->id(),
            'action_type' => $validated['action_type'],
            'notes' => $validated['notes'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'status' => 'open',
        ]);

        return redirect()->route('students.show', $student)->with('success', 'Intervention logged successfully.');
    }

    public function storeViolation(Request $request, Student $student): RedirectResponse
    {
        $this->ensureCanManageStudents();

        $validated = $request->validate([
            'violation_type' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'severity' => ['required', 'in:minor,moderate,serious'],
            'status' => ['required', 'in:pending,resolved,dismissed'],
        ]);

        $student->violations()->create($validated);

        return redirect()->route('students.show', $student)->with('success', 'Violation added successfully.');
    }

    public function updateViolation(Request $request, Student $student, StudentViolation $violation): RedirectResponse
    {
        $this->ensureCanManageStudents();
        abort_unless((int) $violation->student_id === (int) $student->id, Response::HTTP_FORBIDDEN);

        $validated = $request->validate([
            'violation_type' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'severity' => ['required', 'in:minor,moderate,serious'],
            'status' => ['required', 'in:pending,resolved,dismissed'],
        ]);

        $violation->update($validated);

        return redirect()->route('students.show', $student)->with('success', 'Violation updated successfully.');
    }

    public function destroyViolation(Student $student, StudentViolation $violation): RedirectResponse
    {
        $this->ensureCanManageStudents();
        abort_unless((int) $violation->student_id === (int) $student->id, Response::HTTP_FORBIDDEN);

        $violation->delete();

        return redirect()->route('students.show', $student)->with('success', 'Violation deleted successfully.');
    }

    public function updateIntervention(Request $request, Student $student, StudentIntervention $intervention): RedirectResponse
    {
        abort_unless(auth()->user()?->isFaculty() || auth()->user()?->canManageStudents(), Response::HTTP_FORBIDDEN);
        abort_unless((int) $intervention->student_id === (int) $student->id, Response::HTTP_FORBIDDEN);

        $isAdmin = auth()->user()?->canManageStudents();

        $validated = $request->validate([
            'status' => ['required', 'in:open,in_progress,resolved'],
            'outcome' => ['nullable', 'string', 'max:255'],
            'action_type' => [$isAdmin ? 'required' : 'nullable', 'in:called_guardian,advised_student,referred_to_counselor,other'],
            'notes' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
        ]);

        $payload = [
            'status' => $validated['status'],
            'outcome' => $validated['outcome'] ?? $intervention->outcome,
            'resolved_at' => $validated['status'] === 'resolved' ? now()->toDateString() : null,
        ];

        if ($isAdmin) {
            $payload['action_type'] = $validated['action_type'];
            $payload['notes'] = $validated['notes'] ?? null;
            $payload['due_date'] = $validated['due_date'] ?? null;
        }

        $intervention->update($payload);

        return redirect()->route('students.show', $student)->with('success', 'Intervention status updated.');
    }

    public function destroyIntervention(Student $student, StudentIntervention $intervention): RedirectResponse
    {
        abort_unless((int) $intervention->student_id === (int) $student->id, Response::HTTP_FORBIDDEN);
        abort_unless(auth()->user()?->isFaculty() || auth()->user()?->canManageStudents(), Response::HTTP_FORBIDDEN);

        if (auth()->user()->isFaculty()) {
            $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();
            abort_unless(
                $student->sections()->where('faculty_id', $faculty->id)->exists(),
                Response::HTTP_FORBIDDEN
            );
        }

        $intervention->delete();

        return redirect()->route('students.show', $student)->with('success', 'Intervention deleted successfully.');
    }

    public function storeCorrectionRequest(Request $request, Student $student): RedirectResponse
    {
        abort_unless(auth()->user()?->isStudent() && $student->belongsToUser(auth()->user()), Response::HTTP_FORBIDDEN);

        $validated = $request->validate([
            'field_name' => ['required', 'in:first_name,last_name,date_of_birth,gender,address,phone,section'],
            'requested_value' => ['required', 'string', 'max:500'],
            'reason' => ['nullable', 'string'],
        ]);

        StudentCorrectionRequest::create([
            'student_id' => $student->id,
            'requested_by' => auth()->id(),
            'field_name' => $validated['field_name'],
            'current_value' => (string) ($student->{$validated['field_name']} ?? ''),
            'requested_value' => $validated['requested_value'],
            'reason' => $validated['reason'] ?? null,
            'status' => 'open',
        ]);

        return redirect()->route('students.show', $student)->with('success', 'Correction request submitted.');
    }

    public function updateAlertSubscription(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()?->isFaculty(), Response::HTTP_FORBIDDEN);
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();

        $validated = $request->validate([
            'notify_high_risk' => ['nullable', 'boolean'],
            'notify_medium_risk' => ['nullable', 'boolean'],
            'minimum_risk_score' => ['required', 'integer', 'min:0', 'max:100'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);

        FacultyAlertSubscription::updateOrCreate(
            ['faculty_id' => $faculty->id],
            [
                'notify_high_risk' => (bool) ($validated['notify_high_risk'] ?? false),
                'notify_medium_risk' => (bool) ($validated['notify_medium_risk'] ?? false),
                'minimum_risk_score' => (int) $validated['minimum_risk_score'],
                'is_enabled' => (bool) ($validated['is_enabled'] ?? false),
            ]
        );

        return redirect()->route('faculty.dashboard')->with('success', 'Alert subscription updated.');
    }

    /**
     * Assign a curriculum to the student.
     */
    public function assignCurriculum(Request $request, Student $student)
    {
        $this->ensureCanManageStudents();

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'year_level' => 'required|integer|min:1|max:10',
            'semester' => 'required|integer|min:1|max:3',
            'academic_year' => 'required|string|max:20',
        ]);

        $curriculumItems = Curriculum::with('course')
            ->active()
            ->where('department_id', $request->department_id)
            ->where('year_level', $request->year_level)
            ->where('semester', $request->semester)
            ->get();

        if ($curriculumItems->isEmpty()) {
            return redirect()->back()->with('error', 'No curriculum subjects found for the selected department, year level, and semester.');
        }

        StudentCurriculum::firstOrCreate([
            'student_id' => $student->id,
            'department_id' => $request->department_id,
            'year_level' => $request->year_level,
            'semester' => $request->semester,
            'academic_year' => $request->academic_year,
        ], ['status' => 'active']);

        foreach ($curriculumItems as $item) {
            StudentAcademicHistory::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'course_id' => $item->course_id,
                    'academic_year' => $request->academic_year,
                    'semester' => $request->semester,
                ],
                [
                    'units' => $item->course->credits,
                    'grade' => null,
                ]
            );
        }

        AuditLog::recordIfAdmin(
            'curriculum_assigned',
            'student',
            $student->id,
            $student->student_id,
            "Assigned curriculum batch to {$student->full_name} for AY {$request->academic_year}, semester {$request->semester}.",
            null,
            [
                'department_id' => (int) $request->department_id,
                'year_level' => (int) $request->year_level,
                'semester' => (int) $request->semester,
                'academic_year' => $request->academic_year,
                'subjects_added' => $curriculumItems->count(),
            ]
        );

        return redirect()->back()->with('success', 'Curriculum assigned successfully. All curriculum subjects are now included in the student academic history.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $this->ensureCanManageStudents();

        return view('students.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $this->ensureCanManageStudents();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($student->user_id)],
            'student_id' => ['required', 'string', 'max:255', Rule::unique('students')->ignore($student->id)],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'year_level' => 'required|integer|min:1|max:4',
            'section' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,graduated,suspended',
        ]);

        $oldValues = [
            'student_id' => $student->student_id,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'email' => $student->email,
            'phone' => $student->phone,
            'address' => $student->address,
            'year_level' => $student->year_level,
            'section' => $student->section,
            'status' => $student->status,
        ];

        // Update user
        $student->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update student
        $student->update([
            'student_id' => $request->student_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'year_level' => $request->year_level,
            'section' => $request->section,
            'status' => $request->status,
        ]);

        AuditLog::recordIfAdmin(
            'student_updated',
            'student',
            $student->id,
            $student->student_id,
            "Updated student profile for {$student->full_name} ({$student->student_id}).",
            $oldValues,
            [
                'student_id' => $student->student_id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'email' => $student->email,
                'phone' => $student->phone,
                'address' => $student->address,
                'year_level' => $student->year_level,
                'section' => $student->section,
                'status' => $student->status,
            ]
        );

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $this->ensureCanManageStudents();

        $oldValues = [
            'student_id' => $student->student_id,
            'full_name' => $student->full_name,
            'email' => $student->email,
            'status' => $student->status,
        ];

        $student->delete();
        $student->user->delete();

        AuditLog::recordIfAdmin(
            'student_deleted',
            'student',
            $student->id,
            $oldValues['student_id'],
            "Deleted student profile for {$oldValues['full_name']} ({$oldValues['student_id']}).",
            $oldValues,
            null
        );

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Redirect the authenticated student to their own profile.
     */
    public function myProfile(): RedirectResponse
    {
        $student = Student::where('user_id', auth()->id())->first();

        abort_if($student === null, Response::HTTP_NOT_FOUND, 'Student profile not found.');

        return redirect()->route('students.show', $student);
    }

    private function ensureCanManageStudents(): void
    {
        abort_unless(auth()->user()?->canManageStudents(), Response::HTTP_FORBIDDEN);
    }

    private function ensureCanViewStudent(Student $student): void
    {
        $user = auth()->user();

        abort_unless(
            $user?->canManageStudents()
                || $student->belongsToUser($user)
                || (
                    $user?->isFaculty()
                    && Faculty::where('user_id', $user->id)->whereHas('sections.students', function ($query) use ($student) {
                        $query->where('students.id', $student->id);
                    })->exists()
                ),
            Response::HTTP_FORBIDDEN
        );
    }
}
