<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentCurriculum;

$total = 1005;
$academicYear = '2025-2026';
$semester = 2;
$facultyId = 1; // assign sections to faculty 1
$courseId = 1; // use course 1 for all sections

$departments = [
    ['code' => 'BSIT', 'dept_id' => 2], // Information Technology
    ['code' => 'BSCS', 'dept_id' => 1], // Computer Science
];
$years = [1,2,3,4];
$letters = ['A','B','C','D'];

// Create sections if missing and collect section records
$sections = [];
foreach ($departments as $dept) {
    foreach ($years as $y) {
        foreach ($letters as $letter) {
            $name = "{$dept['code']}-{$y}{$letter}";
            $existing = Section::where('section_name', $name)->first();
            if (! $existing) {
                $existing = Section::create([
                    'course_id' => $courseId,
                    'faculty_id' => $facultyId,
                    'section_name' => $name,
                    'year_level' => $y,
                    'semester' => $semester,
                    'capacity' => 40,
                    'enrolled_count' => 0,
                    'status' => 'active',
                ]);
                echo "Created section: {$existing->section_name} (id {$existing->id})\n";
            }
            $sections[] = $existing;
        }
    }
}

$groups = count($sections); // 32
$base = intdiv($total, $groups); // 31
$remainder = $total - ($base * $groups); // 13

$seedRows = [];
$sectionStudentRows = [];
$studentCurriculumRows = [];

$counter = 1;
$now = date('Y-m-d H:i:s');

foreach ($sections as $i => $section) {
    $countForGroup = $base + ($i < $remainder ? 1 : 0);
    for ($k = 0; $k < $countForGroup; $k++) {
        $sid = sprintf('S2026-%04d', $counter);
        $first = 'Student' . $counter;
        $last = 'Lastname' . $counter;
        $email = strtolower($first) . '.' . strtolower($last) . $counter . '@example.com';
        $yearLevel = $section->year_level;
        $sectionName = $section->section_name;

        $seedRows[] = [
            'student_id' => $sid,
            'first_name' => $first,
            'last_name' => $last,
            'date_of_birth' => null,
            'gender' => null,
            'address' => null,
            'phone' => null,
            'email' => $email,
            'photo_path' => null,
            'year_level' => $yearLevel,
            'section' => $sectionName,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // we will insert students later and then build pivot rows
        $sectionStudentRows[] = [
            'section_id' => $section->id,
            'student_index' => count($seedRows) - 1, // temporary index to map later
        ];

        // student curriculum row to be inserted after student id known
        $studentCurriculumRows[] = [
            'student_index' => count($seedRows) - 1,
            'department_id' => (strpos($sectionName, 'BSIT') === 0) ? 2 : 1,
            'year_level' => $yearLevel,
            'semester' => $semester,
            'academic_year' => $academicYear,
            'status' => 'active',
        ];

        $counter++;
    }
}

echo "Prepared " . ($counter - 1) . " student rows. Determining missing inserts...\n";

// Map existing students by student_id to avoid duplicates
$studentIds = array_column($seedRows, 'student_id');
$studentIdMap = DB::table('students')->whereIn('student_id', $studentIds)->pluck('id', 'student_id')->all();

$toInsert = [];
$toInsertStudentIds = [];
foreach ($seedRows as $r) {
    if (! isset($studentIdMap[$r['student_id']])) {
        $toInsert[] = $r;
        $toInsertStudentIds[] = $r['student_id'];
    }
}

$batchSize = 250;
if (! empty($toInsert)) {
    for ($i = 0; $i < count($toInsert); $i += $batchSize) {
        $batch = array_slice($toInsert, $i, $batchSize);
        DB::table('students')->insert($batch);
        echo "Inserted batch starting at index {$i} (new students)\n";
    }
    // reload map for newly inserted students
    $newMap = DB::table('students')->whereIn('student_id', $toInsertStudentIds)->pluck('id', 'student_id')->all();
    $studentIdMap = array_merge($studentIdMap, $newMap);
} else {
    echo "No new students to insert.\n";
}

// Build ordered list of student numeric IDs corresponding to seedRows
$insertedIds = [];
foreach ($seedRows as $r) {
    $insertedIds[] = $studentIdMap[$r['student_id']];
}

// Build section_student inserts, skipping existing pairs
$sectionStudentInserts = [];
foreach ($sectionStudentRows as $sr) {
    $studentId = $insertedIds[$sr['student_index']];
    $exists = DB::table('section_student')
        ->where('section_id', $sr['section_id'])
        ->where('student_id', $studentId)
        ->exists();
    if (! $exists) {
        $sectionStudentInserts[] = [
            'section_id' => $sr['section_id'],
            'student_id' => $studentId,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
if (! empty($sectionStudentInserts)) {
    DB::table('section_student')->insert($sectionStudentInserts);
    echo "Inserted " . count($sectionStudentInserts) . " new section_student rows.\n";
} else {
    echo "No new section_student rows to insert.\n";
}

// Insert student_curricula rows only for students missing them
$studentCurriculumInserts = [];
foreach ($studentCurriculumRows as $scr) {
    $studentId = $insertedIds[$scr['student_index']];
    $exists = DB::table('student_curricula')
        ->where('student_id', $studentId)
        ->where('academic_year', $scr['academic_year'])
        ->where('semester', $scr['semester'])
        ->exists();
    if (! $exists) {
        $studentCurriculumInserts[] = [
            'student_id' => $studentId,
            'department_id' => $scr['department_id'],
            'year_level' => $scr['year_level'],
            'semester' => $scr['semester'],
            'academic_year' => $scr['academic_year'],
            'status' => $scr['status'],
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
if (! empty($studentCurriculumInserts)) {
    DB::table('student_curricula')->insert($studentCurriculumInserts);
    echo "Inserted " . count($studentCurriculumInserts) . " new student_curricula rows.\n";
} else {
    echo "No new student_curricula rows to insert.\n";
}

// Update enrolled_count per section
foreach ($sections as $section) {
    $count = DB::table('section_student')->where('section_id', $section->id)->count();
    Section::where('id', $section->id)->update(['enrolled_count' => $count]);
}

echo "Updated section enrolled counts.\n";

echo "Seeding (idempotent) complete.\n";
