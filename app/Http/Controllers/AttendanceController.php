<?php

namespace App\Http\Controllers;

use App\Models\{Attendance, Schedule, Section, Faculty};
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function recordAttendance(Request $request, Schedule $schedule)
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();

        $this->authorize('recordAttendance', $schedule);

        $students = $schedule->section->students()->get();

        return view('faculty.attendance.record', compact('schedule', 'students'));
    }

    public function storeAttendance(Request $request, Schedule $schedule): \Illuminate\Http\RedirectResponse
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();

        $this->authorize('recordAttendance', $schedule);

        $validated = $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,late,excused',
            'date' => 'required|date',
        ]);

        $date = $validated['date'];

        foreach ($validated['attendance'] as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'schedule_id' => $schedule->id,
                    'student_id' => $studentId,
                    'attendance_date' => $date,
                ],
                ['status' => $status]
            );
        }

        return back()->with('success', 'Attendance recorded successfully.');
    }

    public function viewAttendance(Section $section)
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();

        $this->authorize('viewAttendance', $section);

        $students = $section->students()->get();
        $schedules = $section->schedules()->get();

        $attendance = [];
        foreach ($students as $student) {
            foreach ($schedules as $schedule) {
                $attendance[$student->id][$schedule->id] = Attendance::where([
                    'schedule_id' => $schedule->id,
                    'student_id' => $student->id,
                ])->get();
            }
        }

        return view('faculty.attendance.view', compact('section', 'students', 'schedules', 'attendance'));
    }
}
