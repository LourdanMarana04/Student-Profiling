<?php

namespace App\Exports;

use App\Models\Section;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FacultySectionProfilingExport implements FromArray, ShouldAutoSize, WithEvents
{
    public function __construct(
        private readonly Section $section,
        private readonly Collection $completeness,
        private readonly Collection $interventionItems,
        private readonly Collection $interventions
    ) {
    }

    public function array(): array
    {
        $rows = [
            ['Section Profiling Report'],
            ['Section: ' . $this->section->section_name],
            ['Course: ' . ($this->section->course?->course_code ?? 'N/A') . ' - ' . ($this->section->course?->course_name ?? 'N/A')],
            ['Generated on ' . now()->format('F d, Y h:i A')],
            ['Student', 'Completion', 'Intervention Status', 'Indicators', 'Recommended Action', 'Intervention Outcome'],
        ];

        $interventionsByStudentId = $this->interventionItems->keyBy(fn (array $item) => (int) $item['student']->id);
        $latestOutcomesByStudentId = $this->interventions
            ->sortByDesc('created_at')
            ->groupBy('student_id')
            ->map(fn (Collection $items) => $items->first()?->outcome ?: ($items->first()?->status ?? 'N/A'));

        foreach ($this->completeness as $row) {
            $student = $row['student'];
            $intervention = $interventionsByStudentId->get((int) $student->id);

            $rows[] = [
                $student->full_name ?: $student->user?->name ?: 'N/A',
                ($row['percentage'] ?? 0) . '%',
                $intervention ? 'Needs Attention' : 'Stable',
                $intervention ? implode(', ', $intervention['indicators']) : 'None',
                $intervention['recommended_action'] ?? 'No immediate intervention needed',
                $latestOutcomesByStudentId->get((int) $student->id, 'N/A'),
            ];
        }

        if ($this->completeness->isEmpty()) {
            $rows[] = ['No students found for this section.'];
        }

        return $rows;
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
                $sheet->mergeCells("A4:{$highestColumn}4");

                $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F766E']],
                ]);

                $sheet->getStyle("A2:{$highestColumn}4")->applyFromArray([
                    'font' => ['size' => 11, 'color' => ['rgb' => '134E4A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CCFBF1']],
                ]);

                if ($highestRow >= 5) {
                    $sheet->getStyle("A5:{$highestColumn}5")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D9488']],
                    ]);

                    $sheet->getStyle("A5:{$highestColumn}{$highestRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '99F6E4'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_TOP,
                            'wrapText' => true,
                        ],
                    ]);

                    foreach (range(6, $highestRow) as $row) {
                        $fill = $row % 2 === 0 ? 'F0FDFA' : 'FFFFFF';
                        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fill]],
                        ]);
                    }

                    $sheet->freezePane('A6');
                    $sheet->setAutoFilter("A5:{$highestColumn}{$highestRow}");
                }

                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(44);
                $sheet->getColumnDimension('E')->setWidth(48);
                $sheet->getColumnDimension('F')->setWidth(28);
            },
        ];
    }
}
