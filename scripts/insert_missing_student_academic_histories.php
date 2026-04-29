<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$courseId = 1; // target course for these seeded students
$academicYear = '2025-2026';
$semester = 2;
$now = date('Y-m-d H:i:s');

$students = DB::table('students')->where('student_id', 'like', 'S2026-%')->select('id')->pluck('id')->all();

if (empty($students)) {
    echo "No seeded students found (S2026-...).\n";
    exit(0);
}

$toInsert = [];
foreach ($students as $sid) {
    $exists = DB::table('student_academic_histories')
        ->where('student_id', $sid)
        ->where('course_id', $courseId)
        ->where('academic_year', $academicYear)
        ->where('semester', $semester)
        ->exists();
    if (! $exists) {
        $toInsert[] = [
            'student_id' => $sid,
            'course_id' => $courseId,
            'academic_year' => $academicYear,
            'semester' => $semester,
            'grade' => null,
            'units' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}

if (empty($toInsert)) {
    echo "No missing student_academic_histories to insert.\n";
    exit(0);
}

DB::table('student_academic_histories')->insert($toInsert);

echo "Inserted " . count($toInsert) . " student_academic_histories rows for course {$courseId}.\n";
