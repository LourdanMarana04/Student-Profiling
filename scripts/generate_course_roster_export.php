<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Exports\FacultyCourseRosterExport;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\StudentAcademicHistory;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

$courseId = 1;
$facultyId = 1;
$academicYear = '2025-2026';
$semester = 2;

$faculty = Faculty::find($facultyId);
$course = Course::find($courseId);
if (! $faculty || ! $course) {
    echo "Faculty or Course not found (ids: {$facultyId}, {$courseId}).\n";
    exit(1);
}

$histories = StudentAcademicHistory::with('student')
    ->where('course_id', $course->id)
    ->where('academic_year', $academicYear)
    ->where('semester', $semester)
    ->get();

$students = $histories->map(fn ($h) => $h->student)->filter()->unique('id')->sortBy([
    ['year_level', 'asc'],
    ['section', 'asc'],
    ['last_name', 'asc'],
    ['first_name', 'asc'],
])->values();

$grouped = $students->groupBy(fn ($student) => filled($student->section) ? $student->section : 'Unassigned Section')->sortKeys();

$export = new FacultyCourseRosterExport($faculty, $course, $academicYear, $semester, $grouped);

$path = 'exports/class-list-' . $course->course_code . '.xlsx';
Storage::makeDirectory('exports');
Excel::store($export, $path);

echo "Export saved to storage/app/{$path}\n";
