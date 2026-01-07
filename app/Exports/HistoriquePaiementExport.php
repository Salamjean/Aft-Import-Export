<?php

namespace App\Exports;

use App\Models\Paiement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class HistoriquePaiementExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Paiement::with(['colis.agenceExpedition', 'agent'])->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Date & Heure',
            'Référence Colis',
            'Client',
            'Agent / Caissier',
            'Agence d\'Origine',
            'Méthode de Paiement',
            'Montant',
            'Devise'
        ];
    }

    public function map($paiement): array
    {
        return [
            $paiement->created_at->format('d/m/Y H:i'),
            $paiement->colis->reference_colis ?? 'N/A',
            $paiement->colis->name_expediteur ?? 'N/A',
            $paiement->agent->name ?? 'Admin',
            $paiement->colis->agence_expedition ?? 'N/A',
            ucfirst(str_replace('_', ' ', $paiement->methode_paiement)),
            number_format((float) $paiement->montant, 2, '.', ''),
            $paiement->colis->devise ?? 'XOF'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEA219']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A1:H' . $sheet->getHighestRow())->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Alignement des montants à droite
        $sheet->getStyle('G2:G' . $sheet->getHighestRow())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }
}
