<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Colis - AFT IMPORT EXPORT</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #fea219; padding-bottom: 10px; }
        .header h1 { color: #fea219; margin: 0; }
        .filters { margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table th { background-color: #fea219; color: white; padding: 8px; text-align: left; }
        table td { padding: 6px; border-bottom: 1px solid #ddd; }
        .total { margin-top: 20px; padding: 10px; background: #f8f9fa; border-radius: 5px; font-weight: bold; }
        .badge { padding: 2px 6px; border-radius: 10px; font-size: 8px; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h1>AFT IMPORT EXPORT</h1>
        <p>Liste des Colis - Généré le {{ $dateExport }}</p>
    </div>

    @if(!empty(array_filter($filters)))
    <div class="filters">
        <strong>Filtres appliqués:</strong>
        @if($filters['search']) Recherche: "{{ $filters['search'] }}" @endif
        @if($filters['status']) | Statut: {{ $filters['status'] }} @endif
        @if($filters['mode_transit']) | Mode: {{ $filters['mode_transit'] }} @endif
        @if($filters['paiement']) | Paiement: {{ $filters['paiement'] }} @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th class="text-center">Référence</th>
                <th class="text-center">Expéditeur</th>
                <th class="text-center">Destinataire</th>
                <th class="text-center">Mode</th>
                <th class="text-center">Agences</th>
                <th class="text-center">Montant</th>
                <th class="text-center">Statut</th>
                <th class="text-center">Paiement</th>
                <th class="text-center">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($colis as $item)
            <tr>
                <td class="text-center">{{ $item->reference_colis }}</td>
                <td class="text-center">{{ $item->name_expediteur }} {{ $item->prenom_expediteur }}</td>
                <td class="text-center">{{ $item->name_destinataire }} {{ $item->prenom_destinataire }}</td>
                <td class="text-center">{{ $item->mode_transit }}</td>
                <td class="text-center">{{ $item->agence_expedition }} > {{ $item->agence_destination }}</td>
                <td class="text-center">{{ number_format($item->montant_total, 0) }} {{ $item->devise }}</td>
                <td class="text-center">
                    <span class="badge badge-{{ $item->statut == 'traite' ? 'success' : ($item->statut == 'en_attente' ? 'warning' : 'danger') }}">
                        {{ $item->statut }}
                    </span>
                </td>
                <td class="text-center">
                    <span class="badge badge-{{ $item->statut_paiement == 'totalement_paye' ? 'success' : ($item->statut_paiement == 'partiellement_paye' ? 'warning' : 'danger') }}">
                        {{ $item->statut_paiement }}
                    </span>
                </td>
                <td class="text-center">{{ $item->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total: {{ $totalColis }} colis | Montant total: {{ number_format($totalMontant, 0) }} FCFA
    </div>
</body>
</html>