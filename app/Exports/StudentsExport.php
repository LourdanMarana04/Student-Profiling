<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StudentsExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $students = Student::with(['user', 'curriculums.department'])
            ->withCount(['skills', 'activities', 'affiliations', 'violations'])
            ->get();

        $groupedBySection = $students
            ->groupBy(function (Student $student) {
                return filled($student->section) ? $student->section : 'Unassigned Section';
            })
            ->sortKeys();

        if ($groupedBySection->isEmpty()) {
            return [
                new StudentsSectionSheet('No Students', collect()),
            ];
        }

        return $groupedBySection
            ->map(fn (Collection $sectionStudents, string $sectionName) => new StudentsSectionSheet($sectionName, $sectionStudents))
            ->values()
            ->all();
    }
}
