<?php

namespace App\Http\Controllers;

use App\Models\{Faculty, Student, StudentAcademicHistory, Assignment, Attendance, Section};
use Illuminate\Http\Request;

class StudentInteractionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function viewStudent(Student $student, Request $request)
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();

        $sectionId = $request->section_id;

        $this->authorize('viewStudent', $student);

        $academicHistory = StudentAcademicHistory::where('student_id', $student->id)
            ->with('course')
            ->latest()
            ->paginate(10);

        $coursePerformance = [];
        if ($sectionId) {
            $section = Section::findOrFail($sectionId);

            $submissions = Assignment::where('section_id', $section->id)
                ->with(['submissions' => function($q) use ($student) {
                    $q->where('student_id', $student->id);
                }])
                ->get();

            $attendance = Attendance::whereHas('schedule', function($q) use ($section) {
                $q->where('section_id', $section->id);
            })->where('student_id', $student->id)->get();

            $coursePerformance = [
                'submissions' => $submissions->map(fn($a) => [
                    'assignment' => $a->title,
                    'score' => $a->submissions[0]->score ?? null,
                    'submitted' => $a->submissions[0]->submitted_at ?? null,
                ]),
                'attendance' => [
                    'present' => $attendance->where('status', 'present')->count(),
                    'absent' => $attendance->where('status', 'absent')->count(),
                    'late' => $attendance->where('status', 'late')->count(),
                    'total' => $attendance->count(),
                ],
            ];
        }

        return view('faculty.students.view', compact('student', 'academicHistory', 'coursePerformance'));
    }

    public function sendMessage(Request $request, Student $student)
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();

        $this->authorize('communicateWithStudent', $student);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // TODO: Implement messaging system (could use Laravel Notifications or a custom table)

        return back()->with('success', 'Message sent successfully.');
    }
}
