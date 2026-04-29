<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Faculty;
use Illuminate\Support\Facades\DB;

$facultyId = $argv[1] ?? 1;
$faculty = Faculty::with(['courses', 'sections.students'])->find($facultyId);
if (! $faculty) {
    echo json_encode(['error' => "Faculty id $facultyId not found"], JSON_PRETTY_PRINT);
    exit(0);
}

$result = [];
$result['faculty'] = [
    'id' => $faculty->id,
    'full_name' => $faculty->full_name,
    'faculty_id' => $faculty->faculty_id,
];

$result['courses'] = $faculty->courses->map(function ($c) {
    return [
        'id' => $c->id,
        'course_code' => $c->course_code ?? null,
        'course_name' => $c->course_name ?? null,
        'pivot' => $c->pivot ? (array) $c->pivot->toArray() : null,
    ];
})->all();

$result['sections'] = $faculty->sections->map(function ($s) {
    return [
        'id' => $s->id,
        'section_name' => $s->section_name,
        'year_level' => $s->year_level,
        'semester' => $s->semester,
        'faculty_id' => $s->faculty_id,
        'students' => $s->students->pluck('id')->all(),
    ];
})->all();

// Inspect pivot table raw rows for course_faculty for this faculty
$result['course_faculty_rows'] = DB::table('course_faculty')->where('faculty_id', $faculty->id)->get();

// Count section_student rows for sections owned by this faculty
$sectionIds = collect($result['sections'])->pluck('id')->all();
$result['section_student_count'] = DB::table('section_student')->whereIn('section_id', $sectionIds)->count();

echo json_encode($result, JSON_PRETTY_PRINT);
