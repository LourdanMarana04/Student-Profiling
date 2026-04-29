<?php

namespace App\Exports;

use App\Models\Faculty;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FacultyExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    public function collection()
    {
        $supportsSectionAssignments = Schema::hasTable('sections')
            && Schema::hasColumn('sections', 'faculty_id');

        $query = Faculty::with(['user', 'department', 'courses']);

        if ($supportsSectionAssignments) {
            $query->with('sections');
        }

        return $query->get()
            ->map(function ($faculty) use ($supportsSectionAssignments) {
                return [
                    'Faculty No.' => $faculty->faculty_id ?: $faculty->id,
                    'Full Name' => $faculty->full_name ?: $faculty->user->name,
                    'Department' => $faculty->department->name ?? '',
                    'Email' => $faculty->email ?: $faculty->user->email,
                    'Phone' => $faculty->phone ?? '',
                    'Office' => $faculty->office ?? '',
                    'Specialization' => $faculty->specialization ?? '',
                    'Courses' => $faculty->courses->pluck('course_name')->join(', '),
                    'Assigned Sections' => $supportsSectionAssignments
                        ? $faculty->sections->pluck('section_name')->join(', ')
                        : 'Not available',
                    'Status' => $faculty->status,
                    'Last Updated' => $faculty->updated_at?->format('Y-m-d') ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Faculty No.',
            'Full Name',
            'Department',
            'Email',
            'Phone',
            'Office',
            'Specialization',
            'Courses',
            'Assigned Sections',
            'Status',
            'Last Updated',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                $dataEndRow = $sheet->getHighestRow();

                // Keep a compact report header so the sheet opens cleanly in Excel.
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells("A1:{$highestColumn}1");
                $sheet->mergeCells("A2:{$highestColumn}2");

                $sheet->setCellValue('A1', 'Faculty Profiling Report');
                $sheet->setCellValue('A2', 'Generated on ' . now()->format('F d, Y h:i A'));

                $headerRow = 3;
                $firstDataRow = 4;
                $lastDataRow = $dataEndRow + 2;

                $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 18,
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

                $sheet->getStyle("A2:{$highestColumn}2")->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 10,
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

                $sheet->getStyle("A{$headerRow}:{$highestColumn}{$headerRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E40AF'],
                    ],
                ]);

                $sheet->getStyle("A{$headerRow}:{$highestColumn}{$lastDataRow}")->applyFromArray([
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

                foreach (range($firstDataRow, $lastDataRow) as $row) {
                    $fill = $row % 2 === 0 ? 'F8FAFC' : 'FFFFFF';
                    $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $fill],
                        ],
                    ]);
                }

                $sheet->freezePane("A{$firstDataRow}");
                $sheet->setAutoFilter("A{$headerRow}:{$highestColumn}{$lastDataRow}");
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension($headerRow)->setRowHeight(28);
                foreach (range($firstDataRow, $lastDataRow) as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }
                $sheet->getColumnDimension('A')->setWidth(16);
                $sheet->getColumnDimension('B')->setWidth(24);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(28);
                $sheet->getColumnDimension('E')->setWidth(16);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(30);
                $sheet->getColumnDimension('H')->setWidth(38);
                $sheet->getColumnDimension('I')->setWidth(28);
                $sheet->getColumnDimension('J')->setWidth(14);
                $sheet->getColumnDimension('K')->setWidth(14);
            },
        ];
    }
}
