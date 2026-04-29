<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Section;

$now = date('Y-m-d H:i:s');
$academicYear = '2025-2026';
$semester = 2;

// Find students seeded by the script (S2026-...)
$students = DB::table('students')
    ->where('students.student_id', 'like', 'S2026-%')
    ->leftJoin('student_curricula', 'students.id', '=', 'student_curricula.student_id')
    ->whereNull('student_curricula.id')
    ->select('students.id', 'students.student_id', 'students.section', 'students.year_level')
    ->get();

if ($students->isEmpty()) {
    echo "No missing student_curricula rows found.\n";
    exit(0);
}

$inserts = [];
foreach ($students as $s) {
    $sectionName = $s->section;
    $departmentId = (strpos($sectionName, 'BSIT') === 0) ? 2 : 1;

    $inserts[] = [
        'student_id' => $s->id,
        'department_id' => $departmentId,
        'year_level' => $s->year_level,
        'semester' => $semester,
        'academic_year' => $academicYear,
        'status' => 'active',
        'created_at' => $now,
        'updated_at' => $now,
    ];
}

DB::table('student_curricula')->insert($inserts);

echo "Inserted " . count($inserts) . " missing student_curricula rows.\n";

// Update enrolled_count per section (safe to run)
$sections = DB::table('sections')->get();
foreach ($sections as $section) {
    $count = DB::table('section_student')->where('section_id', $section->id)->count();
    DB::table('sections')->where('id', $section->id)->update(['enrolled_count' => $count]);
}

echo "Updated section enrolled counts.\n";
