<?php

namespace App\Exports;

use App\Models\Course;
use App\Models\Faculty;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FacultyCourseRosterExport implements WithMultipleSheets
{
    public function __construct(
        private readonly Faculty $faculty,
        private readonly Course $course,
        private readonly string $academicYear,
        private readonly int $semester,
        private readonly Collection $groupedStudents
    ) {
    }

    public function sheets(): array
    {
        if ($this->groupedStudents->isEmpty()) {
            return [
                new FacultyCourseRosterSectionSheet(
                    $this->course->course_code,
                    $this->course->course_name,
                    'No Students',
                    $this->academicYear,
                    $this->semester,
                    collect()
                ),
            ];
        }

        return $this->groupedStudents
            ->map(fn ($students, $sectionName) => new FacultyCourseRosterSectionSheet(
                $this->course->course_code,
                $this->course->course_name,
                (string) $sectionName,
                $this->academicYear,
                $this->semester,
                $students
            ))
            ->values()
            ->all();
    }
}
