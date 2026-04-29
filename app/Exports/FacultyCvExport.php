<?php

namespace App\Exports;

use App\Models\Faculty;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FacultyCvExport implements FromArray, ShouldAutoSize, WithEvents
{
    public function __construct(private readonly Faculty $faculty)
    {
    }

    public function array(): array
    {
        $supportsSectionAssignments = Schema::hasTable('sections')
            && Schema::hasColumn('sections', 'faculty_id')
            && Schema::hasTable('section_student');

        $relations = [
            'user',
            'department',
            'courses.department',
        ];

        if ($supportsSectionAssignments) {
            $relations[] = 'sections.course';
        }

        $faculty = $this->faculty->loadMissing($relations);

        [$academicYear, $semester] = $faculty->currentAcademicPeriod();

        $currentCourses = $faculty->courses()
            ->with('department')
            ->wherePivot('academic_year', $academicYear)
            ->wherePivot('semester', $semester)
            ->get();

        $sections = $supportsSectionAssignments
            ? $faculty->sections()
                ->with(['course', 'students'])
                ->where('semester', $semester)
                ->get()
            : collect();

        $courseList = $currentCourses->isNotEmpty()
            ? $currentCourses->map(fn ($course) => "{$course->course_code} - {$course->course_name}")->implode("\n")
            : 'No current course assignments';

        $sectionList = $sections->isNotEmpty()
            ? $sections->map(fn ($section) => "{$section->section_name} ({$section->students->count()} students)")->implode("\n")
            : ($supportsSectionAssignments ? 'No section assignments' : 'Section assignments are not enabled');

        return [
            ['FACULTY CURRICULUM VITAE'],
            ['Generated on ' . now()->format('F d, Y h:i A')],
            [''],
            ['PERSONAL INFORMATION'],
            ['Faculty ID', $faculty->faculty_id],
            ['Full Name', $faculty->full_name],
            ['Email Address', $faculty->email ?: $faculty->user?->email],
            ['Phone Number', $faculty->phone ?: 'N/A'],
            ['Department', $faculty->department?->name ?: 'N/A'],
            ['Office', $faculty->office ?: 'N/A'],
            ['Status', ucfirst((string) $faculty->status)],
            [''],
            ['PROFESSIONAL PROFILE'],
            ['Specialization', $faculty->specialization ?: 'N/A'],
            ['Current Academic Year', $academicYear],
            ['Current Semester', 'Semester ' . $semester],
            ['Assigned Courses', $courseList],
            ['Assigned Sections', $sectionList],
            ['Total Current Courses', $currentCourses->count()],
            ['Total Current Sections', $sections->count()],
            ['Total Assigned Students', $sections->sum(fn ($section) => $section->students->count())],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastDataRow = 21;

                $sheet->mergeCells('A1:B1');
                $sheet->mergeCells('A2:B2');
                $sheet->mergeCells('A3:B3');

                $sheet->getStyle('A1:B1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 20,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '0F172A'],
                    ],
                ]);

                $sheet->getStyle('A2:B2')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 11,
                        'color' => ['rgb' => '334155'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2E8F0'],
                    ],
                ]);

                $sheet->getStyle('A3:B3')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8FAFC'],
                    ],
                ]);

                foreach (['A4:B4', 'A13:B13'] as $range) {
                    $sheet->mergeCells($range);
                    $sheet->getStyle($range)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                            'color' => ['rgb' => 'FFFFFF'],
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '1E40AF'],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }

                $sheet->getStyle('A5:A11')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E0ECFF'],
                    ],
                ]);

                $sheet->getStyle('A14:A21')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E0ECFF'],
                    ],
                ]);

                $sheet->getStyle("A5:B{$lastDataRow}")->applyFromArray([
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CBD5E1'],
                        ],
                    ],
                ]);

                foreach (range(5, $lastDataRow) as $row) {
                    if (in_array($row, [4, 13], true)) {
                        continue;
                    }

                    if ($row % 2 === 0) {
                        $sheet->getStyle("B{$row}:B{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8FAFC'],
                            ],
                        ]);
                    }
                }

                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(4)->setRowHeight(24);
                $sheet->getRowDimension(13)->setRowHeight(24);

                foreach (range(5, $lastDataRow) as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }

                $sheet->getColumnDimension('A')->setWidth(34);
                $sheet->getColumnDimension('B')->setWidth(74);

                $sheet->freezePane('A5');
            },
        ];
    }
}
