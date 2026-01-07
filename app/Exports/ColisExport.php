<?php

namespace App\Exports;

use App\Models\Colis;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ColisExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Colis::with(['agenceExpedition', 'agenceDestination', 'conteneur'])
            ->where('statut', 'valide')
            ->orderBy('created_at', 'desc');

        if ($this->request->has('search') && !empty($this->request->search)) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_colis', 'LIKE', "%{$search}%")
                    ->orWhere('name_expediteur', 'LIKE', "%{$search}%")
                    ->orWhere('name_destinataire', 'LIKE', "%{$search}%");
            });
        }

        if ($this->request->has('status') && !empty($this->request->status)) {
            $query->where('statut', $this->request->status);
        }

        if ($this->request->has('mode_transit') && !empty($this->request->mode_transit)) {
            $query->where('mode_transit', $this->request->mode_transit);
        }

        if ($this->request->has('paiement') && !empty($this->request->paiement)) {
            $query->where('statut_paiement', $this->request->paiement);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Mode Transit',
            'Expéditeur',
            'Contact Exp.',
            'Destinataire',
            'Contact Dest.',
            'Agence Exp.',
            'Agence Dest.',
            'Montant Total',
            'Montant Payé',
            'Reste à Payer',
            'Statut Paiement',
            'Date d\'Enregistrement'
        ];
    }

    public function map($colis): array
    {
        return [
            $colis->reference_colis,
            $colis->mode_transit,
            $colis->name_expediteur . ' ' . ($colis->prenom_expediteur ?? ''),
            $colis->contact_expediteur,
            $colis->name_destinataire . ' ' . ($colis->prenom_destinataire ?? ''),
            $colis->contact_destinataire,
            $colis->agence_expedition,
            $colis->agence_destination,
            number_format((float) $colis->montant_total, 2, '.', '') . ' ' . ($colis->devise ?? 'XOF'),
            number_format((float) $colis->montant_paye, 2, '.', '') . ' ' . ($colis->devise ?? 'XOF'),
            number_format((float) ($colis->reste_a_payer ?? ($colis->montant_total - $colis->montant_paye)), 2, '.', '') . ' ' . ($colis->devise ?? 'XOF'),
            ucfirst(str_replace('_', ' ', $colis->statut_paiement)),
            $colis->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style pour l'entête
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FEA219'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Hauteur de l'entête
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Bordures pour tout le tableau
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:M' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Alignement centré pour certaines colonnes
        $sheet->getStyle('A2:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G2:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I2:K' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('L2:M' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}
