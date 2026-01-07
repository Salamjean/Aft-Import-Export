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

class BilanGlobalSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $stats;

    public function __construct($stats)
    {
        $this->stats = $stats;
    }


    public function title(): string
    {
        return 'Bilan Global';
    }

    public function headings(): array
    {
        return [
            'Devise',
            'Total Colis',
            'Valeur Totale',
            'Montant Encaissé',
            'Reste à Recouvrer',
            'Taux Recouvrement (%)'
        ];
    }

    public function map($item): array
    {
        // $item is an array from the statsGlobales loop
        // In BilanFinancierController, getStatsGlobales returns an array indexed by devise
        // When we collect it, each item is the stats array. We need the keys too?
        // Wait, collect($this->stats) will lose the keys (devise) if they are string keys.
        // Actually, let's fix the mapping in collection()
        return [
            $item['devise'],
            $item['total_colis'],
            number_format($item['montant_total'], 2, '.', '') . ' ' . $item['devise'],
            number_format($item['montant_paye'], 2, '.', '') . ' ' . $item['devise'],
            number_format($item['montant_impaye'], 2, '.', '') . ' ' . $item['devise'],
            $item['taux_recouvrement'] . '%'
        ];
    }

    // Fix collection to include devise in items
    public function collection()
    {
        $data = [];
        foreach ($this->stats as $devise => $s) {
            $s['devise'] = $devise;
            $data[] = $s;
        }
        return collect($data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEA219']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A1:F' . $sheet->getHighestRow())->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        return [];
    }
}
