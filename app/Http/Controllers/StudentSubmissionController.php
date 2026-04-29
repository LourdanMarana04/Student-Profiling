<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Student;
use App\Models\StudentActivity;
use App\Models\StudentSkill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentSubmissionController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()?->canManageStudents(), Response::HTTP_FORBIDDEN);

        $pendingSkills = StudentSkill::with('student.user')
            ->where('approval_status', StudentSkill::APPROVAL_PENDING)
            ->latest()
            ->get();

        $pendingActivities = StudentActivity::with('student.user')
            ->where('approval_status', StudentActivity::APPROVAL_PENDING)
            ->latest()
            ->get();

        return view('submissions.index', compact('pendingSkills', 'pendingActivities'));
    }

    public function storeSkill(Request $request): RedirectResponse
    {
        $student = $this->ensureAuthenticatedStudent($request);

        $validated = $request->validate([
            'skill_name' => ['required', 'string', 'max:255'],
            'proficiency_level' => ['required', 'in:beginner,intermediate,advanced,expert'],
            'evidence_link' => ['nullable', 'url', 'max:2048'],
            'evidence_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ]);

        $evidencePath = null;
        if ($request->hasFile('evidence_file')) {
            $evidencePath = $request->file('evidence_file')->store('student-evidence/skills', 'public');
        }

        StudentSkill::create([
            'student_id' => $student->id,
            'skill_name' => $validated['skill_name'],
            'evidence_link' => $validated['evidence_link'] ?? null,
            'evidence_path' => $evidencePath,
            'proficiency_level' => $validated['proficiency_level'],
            'approval_status' => StudentSkill::APPROVAL_PENDING,
        ]);

        return redirect()->route('students.show', $student)->with('success', 'Skill submitted for admin review.');
    }

    public function storeActivity(Request $request): RedirectResponse
    {
        $student = $this->ensureAuthenticatedStudent($request);

        $validated = $request->validate([
            'activity_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'status' => ['required', 'in:active,inactive,completed'],
            'evidence_link' => ['nullable', 'url', 'max:2048'],
            'evidence_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ]);

        $evidencePath = null;
        if ($request->hasFile('evidence_file')) {
            $evidencePath = $request->file('evidence_file')->store('student-evidence/activities', 'public');
        }

        StudentActivity::create([
            'student_id' => $student->id,
            'activity_name' => $validated['activity_name'],
            'evidence_link' => $validated['evidence_link'] ?? null,
            'evidence_path' => $evidencePath,
            'description' => $validated['description'],
            'date' => $validated['date'],
            'status' => $validated['status'],
            'approval_status' => StudentActivity::APPROVAL_PENDING,
        ]);

        return redirect()->route('students.show', $student)->with('success', 'Activity submitted for admin review.');
    }

    public function reviewSkill(Request $request, StudentSkill $skill): RedirectResponse
    {
        abort_unless(auth()->user()?->canManageStudents(), Response::HTTP_FORBIDDEN);

        $validated = $request->validate([
            'decision' => ['required', 'in:approved,rejected'],
            'review_notes' => ['nullable', 'string'],
        ]);

        $oldValues = [
            'approval_status' => $skill->approval_status,
            'review_notes' => $skill->review_notes,
        ];

        $skill->update([
            'approval_status' => $validated['decision'],
            'review_notes' => $validated['review_notes'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        AuditLog::recordIfAdmin(
            'skill_submission_reviewed',
            'student_skill',
            $skill->id,
            $skill->skill_name,
            "Reviewed skill submission '{$skill->skill_name}' for {$skill->student->full_name}.",
            $oldValues,
            [
                'approval_status' => $skill->approval_status,
                'review_notes' => $skill->review_notes,
            ]
        );

        return redirect()->route('submissions.index')->with('success', 'Skill submission reviewed successfully.');
    }

    public function reviewActivity(Request $request, StudentActivity $activity): RedirectResponse
    {
        abort_unless(auth()->user()?->canManageStudents(), Response::HTTP_FORBIDDEN);

        $validated = $request->validate([
            'decision' => ['required', 'in:approved,rejected'],
            'review_notes' => ['nullable', 'string'],
        ]);

        $oldValues = [
            'approval_status' => $activity->approval_status,
            'review_notes' => $activity->review_notes,
        ];

        $activity->update([
            'approval_status' => $validated['decision'],
            'review_notes' => $validated['review_notes'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        AuditLog::recordIfAdmin(
            'activity_submission_reviewed',
            'student_activity',
            $activity->id,
            $activity->activity_name,
            "Reviewed activity submission '{$activity->activity_name}' for {$activity->student->full_name}.",
            $oldValues,
            [
                'approval_status' => $activity->approval_status,
                'review_notes' => $activity->review_notes,
            ]
        );

        return redirect()->route('submissions.index')->with('success', 'Activity submission reviewed successfully.');
    }

    private function ensureAuthenticatedStudent(Request $request): Student
    {
        abort_unless($request->user()?->isStudent(), Response::HTTP_FORBIDDEN);

        $student = $request->user()->student;

        abort_if($student === null, Response::HTTP_NOT_FOUND, 'Student profile not found.');

        return $student;
    }
}
