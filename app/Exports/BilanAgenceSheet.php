<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BilanAgenceSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $stats;

    public function __construct($stats)
    {
        $this->stats = $stats;
    }

    public function collection()
    {
        return collect($this->stats);
    }

    public function title(): string
    {
        return 'Encaissements par Agence';
    }

    public function headings(): array
    {
        return [
            'Agence',
            'Pays',
            'Nombre de Colis',
            'Total Encaissé',
            'Devise'
        ];
    }

    public function map($item): array
    {
        return [
            $item['agence']->name,
            $item['agence']->pays,
            $item['total_colis'],
            number_format($item['total_encaisse_agents'], 2, '.', ''),
            $item['agence']->devise ?? 'XOF'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEA219']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A1:E' . $sheet->getHighestRow())->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        return [];
    }
}
