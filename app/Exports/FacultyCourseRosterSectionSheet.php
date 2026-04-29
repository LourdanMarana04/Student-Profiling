<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FacultyCourseRosterSectionSheet implements FromArray, WithTitle, ShouldAutoSize, WithEvents
{
    public function __construct(
        private readonly string $courseCode,
        private readonly string $courseName,
        private readonly string $sectionName,
        private readonly string $academicYear,
        private readonly int $semester,
        private readonly Collection $students
    ) {
    }

    public function array(): array
    {
        $rows = [
            ['CLASS LIST'],
            [$this->courseCode . ' - ' . $this->courseName],
            ['Section: ' . $this->sectionName . ' | Semester ' . $this->semester . ' | AY ' . $this->academicYear],
            ['Student ID', 'Student Name', 'Year Level', 'Section', 'Email', 'Phone'],
        ];

        foreach ($this->students as $student) {
            $rows[] = [
                $student->student_id,
                $student->full_name,
                $student->year_level,
                $student->section ?: 'N/A',
                $student->email ?: $student->user?->email ?: 'N/A',
                $student->phone ?: 'N/A',
            ];
        }

        if ($this->students->isEmpty()) {
            $rows[] = ['No students found for this section.'];
        }

        return $rows;
    }

    public function title(): string
    {
        return str($this->sectionName)->limit(31, '')->toString() ?: 'Section';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                $sheet->mergeCells("A1:{$highestColumn}1");
                $sheet->mergeCells("A2:{$highestColumn}2");
                $sheet->mergeCells("A3:{$highestColumn}3");

                $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F766E']],
                ]);

                $sheet->getStyle("A2:{$highestColumn}2")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '134E4A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CCFBF1']],
                ]);

                $sheet->getStyle("A3:{$highestColumn}3")->applyFromArray([
                    'font' => ['italic' => true, 'color' => ['rgb' => '115E59']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                if ($highestRow >= 4) {
                    $sheet->getStyle("A4:{$highestColumn}4")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D9488']],
                    ]);

                    $sheet->getStyle("A4:{$highestColumn}{$highestRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '99F6E4'],
                            ],
                        ],
                    ]);

                    foreach (range(5, $highestRow) as $row) {
                        $fill = $row % 2 === 0 ? 'F0FDFA' : 'FFFFFF';
                        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fill]],
                        ]);
                    }

                    $sheet->freezePane('A5');
                }
            },
        ];
    }
}
