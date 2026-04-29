<?php

namespace App\Exports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DepartmentsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    public function collection()
    {
        return Department::query()
            ->withCount(['students', 'faculty', 'courses'])
            ->get()
            ->map(function (Department $department) {
                return [
                    'Department ID' => $department->id,
                    'Department Name' => $department->name,
                    'Code' => $department->code ?? 'N/A',
                    'Description' => $department->description ?? 'N/A',
                    'Status' => ucfirst((string) ($department->status ?? 'active')),
                    'Courses Count' => $department->courses_count,
                    'Students Count' => $department->students_count,
                    'Faculty Count' => $department->faculty_count,
                    'Last Updated' => $department->updated_at?->format('Y-m-d') ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Department ID',
            'Department Name',
            'Code',
            'Description',
            'Status',
            'Courses Count',
            'Students Count',
            'Faculty Count',
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

                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells("A1:{$highestColumn}1");
                $sheet->mergeCells("A2:{$highestColumn}2");

                $sheet->setCellValue('A1', 'Departments Report');
                $sheet->setCellValue('A2', 'Generated on ' . now()->format('F d, Y h:i A'));

                $headerRow = 3;
                $firstDataRow = 4;
                $lastDataRow = $dataEndRow + 2;

                $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
                ]);

                $sheet->getStyle("A2:{$highestColumn}2")->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '334155']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
                ]);

                $sheet->getStyle("A{$headerRow}:{$highestColumn}{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
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

                $sheet->getColumnDimension('A')->setWidth(14);
                $sheet->getColumnDimension('B')->setWidth(24);
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(42);
                $sheet->getColumnDimension('E')->setWidth(14);
                $sheet->getColumnDimension('F')->setWidth(14);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(14);
                $sheet->getColumnDimension('I')->setWidth(14);
            },
        ];
    }
}
