<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Section;

$facultyId = isset($argv[1]) ? (int)$argv[1] : 1;
$courseId = isset($argv[2]) ? (int)$argv[2] : 1;
$sectionName = isset($argv[3]) ? $argv[3] : 'BSIT-1A';
$yearLevel = isset($argv[4]) ? (int)$argv[4] : 1;
$semester = isset($argv[5]) ? (int)$argv[5] : 2;
$capacity = isset($argv[6]) ? (int)$argv[6] : 40;

$section = Section::create([
    'course_id' => $courseId,
    'faculty_id' => $facultyId,
    'section_name' => $sectionName,
    'year_level' => $yearLevel,
    'semester' => $semester,
    'capacity' => $capacity,
    'enrolled_count' => 0,
    'status' => 'active',
]);

if ($section) {
    echo "Created section id: {$section->id} - {$section->section_name}\n";
} else {
    echo "Failed to create section.\n";
}
