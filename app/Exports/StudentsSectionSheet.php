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

class StudentsSectionSheet implements FromArray, WithTitle, ShouldAutoSize, WithEvents
{
    public function __construct(
        private readonly string $sectionName,
        private readonly Collection $students
    ) {
    }

    public function array(): array
    {
        $rows = [
            ['Student Profiling Report'],
            ['Section: ' . $this->sectionName],
            ['Generated on ' . now()->format('F d, Y h:i A')],
            ['Student No.', 'Full Name', 'Department', 'Email', 'Phone', 'Year Level', 'Section', 'Profile Completion', 'Skills Submitted', 'Activities Submitted', 'Affiliations', 'Violations', 'Status', 'Last Updated'],
        ];

        foreach ($this->students as $student) {
            $department = $student->curriculums->where('status', 'active')->first()?->department;

            $rows[] = [
                $student->student_id ?: $student->id,
                $student->full_name ?: $student->user?->name,
                $department?->name ?? '',
                $student->email ?: $student->user?->email,
                $student->phone ?? '',
                $student->year_level ?? '',
                $student->section ?: 'N/A',
                $student->profileCompletionPercentage() . '%',
                $student->skills_count ?? 0,
                $student->activities_count ?? 0,
                $student->affiliations_count ?? 0,
                $student->violations_count ?? 0,
                (string) ($student->status ?? ''),
                $student->updated_at?->format('Y-m-d') ?? '',
            ];
        }

        if ($this->students->isEmpty()) {
            $rows[] = ['No students found for this section.'];
        }

        return $rows;
    }

    public function title(): string
    {
        return str($this->sectionName)->replace(['\\', '/', '?', '*', '[', ']', ':'], '-')->limit(31, '')->toString() ?: 'Section';
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
                    'font' => ['bold' => true, 'size' => 20, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
                ]);

                $sheet->getStyle("A2:{$highestColumn}2")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1E3A8A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
                ]);

                $sheet->getStyle("A3:{$highestColumn}3")->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '334155']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
                ]);

                if ($highestRow >= 4) {
                    $sheet->getStyle("A4:{$highestColumn}4")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
                    ]);

                    $sheet->getStyle("A4:{$highestColumn}{$highestRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'CBD5E1'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_TOP,
                            'wrapText' => true,
                        ],
                    ]);

                    foreach (range(5, $highestRow) as $row) {
                        $fill = $row % 2 === 0 ? 'F8FAFC' : 'FFFFFF';
                        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $fill],
                            ],
                        ]);
                    }

                    $sheet->freezePane('A5');
                    $sheet->setAutoFilter("A4:{$highestColumn}{$highestRow}");
                }

                $sheet->getRowDimension(1)->setRowHeight(34);
                $sheet->getRowDimension(2)->setRowHeight(24);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(30);

                foreach (range(5, $highestRow) as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }
            },
        ];
    }
}
