<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Faculty;
use Illuminate\Support\Facades\DB;

$facultyId = isset($argv[1]) ? (int)$argv[1] : null;
$courseId = isset($argv[2]) ? (int)$argv[2] : null;

if (! $facultyId || ! $courseId) {
    echo "Usage: php assign_course_semester.php <faculty_id> <course_id>\n";
    exit(1);
}

$faculty = Faculty::find($facultyId);
if (! $faculty) {
    echo "Faculty id {$facultyId} not found\n";
    exit(1);
}

[$academicYear, $semester] = $faculty->currentAcademicPeriod();

$exists = DB::table('course_faculty')
    ->where('faculty_id', $facultyId)
    ->where('course_id', $courseId)
    ->where('academic_year', $academicYear)
    ->where('semester', $semester)
    ->exists();

if ($exists) {
    echo "Assignment already exists for faculty {$facultyId}, course {$courseId} for {$academicYear} semester {$semester}\n";
    exit(0);
}

DB::table('course_faculty')->insert([
    'course_id' => $courseId,
    'faculty_id' => $facultyId,
    'academic_year' => $academicYear,
    'semester' => $semester,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "Inserted assignment: faculty {$facultyId}, course {$courseId}, AY {$academicYear}, semester {$semester}\n";
