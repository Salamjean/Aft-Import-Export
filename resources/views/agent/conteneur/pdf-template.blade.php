<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Conteneur {{ $conteneur->name_conteneur }} - AFT IMPORT EXPORT</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10px; 
            margin: 0;
            padding: 15px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #f79c14; 
            padding-bottom: 10px; 
        }
        .header h1 { 
            color: #f79c14; 
            margin: 0; 
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .info-card {
            text-align: center;
            padding: 8px;
        }
        .info-label {
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .info-value {
            font-weight: bold;
            color: #333;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 10px 0; 
        }
        table th { 
            background-color: #f79c14; 
            color: white; 
            padding: 8px; 
            text-align: center;
            font-size: 9px;
            border: 1px solid #e67e22;
        }
        table td { 
            padding: 6px; 
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #f0f0f0;
            font-size: 8px;
        }
        table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .total { 
            margin-top: 20px; 
            padding: 10px; 
            background: #f8f9fa; 
            border-radius: 5px; 
            font-weight: bold;
            text-align: center;
        }
        .badge { 
            padding: 3px 8px; 
            border-radius: 10px; 
            font-size: 8px; 
            font-weight: bold;
            display: inline-block;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .badge-primary { background: #cce7ff; color: #004085; }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-valide { background: #fff3cd; color: #856404; }
        .status-charge { background: #cce7ff; color: #004085; }
        .status-entrepot { background: #d1ecf1; color: #0c5460; }
        .status-decharge { background: #fff3cd; color: #856404; }
        .status-livre { background: #d4edda; color: #155724; }
        .status-annule { background: #f8d7da; color: #721c24; }
        
        .payment-badge {
            padding: 3px 6px;
            border-radius: 8px;
            font-size: 7px;
            font-weight: bold;
        }
        .payment-paid { background: #28a745; color: white; }
        .payment-partial { background: #ffc107; color: #212529; }
        .payment-unpaid { background: #dc3545; color: white; }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .mb-10 { margin-bottom: 10px; }
        .mt-10 { margin-top: 10px; }
        
        /* Barre de progression */
        .progress-container {
            width: 100%;
            background: #e9ecef;
            border-radius: 3px;
            height: 4px;
            margin: 2px 0;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            float: left;
        }
        .progress-valide { background: #6c757d; }
        .progress-charge { background: #ffc107; }
        .progress-entrepot { background: #17a2b8; }
        .progress-decharge { background: #007bff; }
        .progress-livre { background: #28a745; }
        .progress-annule { background: #dc3545; }
        
        .legend {
            font-size: 6px;
            margin-top: 1px;
        }
        .legend-item {
            display: inline-block;
            margin-right: 5px;
        }
        .legend-color {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 2px;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>AFT IMPORT EXPORT</h1>
        <p>RAPPORT DU CONTENEUR - {{ $conteneur->name_conteneur }}</p>
        <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <!-- Informations du conteneur -->
    <div class="info-container">
        <div class="info-card">
            <div class="info-label">Nom du Conteneur</div>
            <div class="info-value">{{ $conteneur->name_conteneur }}</div>
        </div>
        <div class="info-card">
            <div class="info-label">Type</div>
            <div class="info-value">
                <span class="badge badge-primary">{{ $conteneur->type_conteneur }}</span>
            </div>
        </div>
        <div class="info-card">
            <div class="info-label">Statut</div>
            <div class="info-value">
                <span class="badge badge-{{ $conteneur->statut == 'ouvert' ? 'success' : 'danger' }}">
                    {{ ucfirst($conteneur->statut) }}
                </span>
            </div>
        </div>
        <div class="info-card">
            <div class="info-label">Nombre de Colis</div>
            <div class="info-value">{{ $conteneur->colis->count() }}</div>
        </div>
        <div class="info-card">
            <div class="info-label">Date Création</div>
            <div class="info-value">{{ $conteneur->created_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <!-- Liste des colis -->
    <table>
        <thead>
            <tr>
                <th class="text-center">Référence</th>
                <th class="text-center">Produit(Qte)</th>
                <th class="text-center">Expéditeur</th>
                <th class="text-center">Destinataire</th>
                <th class="text-center">Mode Transit</th>
                <th class="text-center">Agences</th>
                <th class="text-center">Montant</th>
                <th class="text-center">Statut Colis</th>
                <th class="text-center">Statut Paiement</th>
                <th class="text-center">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($conteneur->colis as $item)
            <tr>
                <td class="text-center">
                    <strong>{{ $item->reference_colis }}</strong>
                </td>
                <td class="text-center">
                    <div class="produits-list">
                        @foreach($item->produits_groupes as $produit => $quantite)
                            <strong>{{ $produit }} ({{ $quantite }})</strong>
                        @endforeach
                    </div>
                </td>
                <td class="text-center">
                    {{ $item->name_expediteur }} {{ $item->prenom_expediteur ?? '' }}
                    <br><small>{{ $item->contact_expediteur }}</small>
                </td>
                <td class="text-center">
                    {{ $item->name_destinataire }} {{ $item->prenom_destinataire }}
                    <br><small>{{ $item->indicatif }}{{ $item->contact_destinataire }}</small>
                </td>
                <td class="text-center">
                    <span class="badge badge-info">{{ $item->mode_transit }}</span>
                </td>
                <td class="text-center">
                    <small>
                        <strong>Exp:</strong> {{ $item->agence_expedition }}<br>
                        <strong>Dest:</strong> {{ $item->agence_destination }}
                    </small>
                </td>
                <td class="text-center">
                    <strong>{{ number_format($item->montant_total, 0) }}</strong> {{ $item->devise }}
                    <br>
                    <small>Payé: {{ number_format($item->montant_paye, 0) }}</small>
                </td>
                <td class="text-center">
                    <span class="status-badge status-{{ $item->statut }}">
                        @if($item->statut == 'valide')
                            Valide
                        @elseif($item->statut == 'charge')
                            Chargé
                        @elseif($item->statut == 'entrepot')
                            Entrepôt
                        @elseif($item->statut == 'decharge')
                            Déchargé
                        @elseif($item->statut == 'livre')
                            Livré
                        @else
                            Annulé
                        @endif
                    </span>

                    @if($item->total_individuels > 0)
                    <div class="progress-container">
                        @php
                            $pourcentageValide = ($item->individuels_valides / $item->total_individuels) * 100;
                            $pourcentageCharge = ($item->individuels_charges / $item->total_individuels) * 100;
                            $pourcentageEntrepot = ($item->individuels_entrepot / $item->total_individuels) * 100;
                            $pourcentageDecharge = ($item->individuels_decharges / $item->total_individuels) * 100;
                            $pourcentageLivre = ($item->individuels_livres / $item->total_individuels) * 100;
                            $pourcentageAnnule = ($item->individuels_annules / $item->total_individuels) * 100;
                        @endphp
                        
                        @if($item->individuels_valides > 0)
                        <div class="progress-bar progress-valide" style="width: {{ $pourcentageValide }}%"></div>
                        @endif
                        
                        @if($item->individuels_charges > 0)
                        <div class="progress-bar progress-charge" style="width: {{ $pourcentageCharge }}%"></div>
                        @endif
                        
                        @if($item->individuels_entrepot > 0)
                        <div class="progress-bar progress-entrepot" style="width: {{ $pourcentageEntrepot }}%"></div>
                        @endif
                        
                        @if($item->individuels_decharges > 0)
                        <div class="progress-bar progress-decharge" style="width: {{ $pourcentageDecharge }}%"></div>
                        @endif
                        
                        @if($item->individuels_livres > 0)
                        <div class="progress-bar progress-livre" style="width: {{ $pourcentageLivre }}%"></div>
                        @endif
                        
                        @if($item->individuels_annules > 0)
                        <div class="progress-bar progress-annule" style="width: {{ $pourcentageAnnule }}%"></div>
                        @endif
                    </div>

                    <div class="legend">
                        @if($item->individuels_valides > 0)
                        <span class="legend-item">
                            <span class="legend-color" style="background-color: #6c757d;"></span>V:{{ $item->individuels_valides }}
                        </span>
                        @endif
                        @if($item->individuels_charges > 0)
                        <span class="legend-item">
                            <span class="legend-color" style="background-color: #ffc107;"></span>C:{{ $item->individuels_charges }}
                        </span>
                        @endif
                        @if($item->individuels_entrepot > 0)
                        <span class="legend-item">
                            <span class="legend-color" style="background-color: #17a2b8;"></span>E:{{ $item->individuels_entrepot }}
                        </span>
                        @endif
                        @if($item->individuels_decharges > 0)
                        <span class="legend-item">
                            <span class="legend-color" style="background-color: #007bff;"></span>D:{{ $item->individuels_decharges }}
                        </span>
                        @endif
                        @if($item->individuels_livres > 0)
                        <span class="legend-item">
                            <span class="legend-color" style="background-color: #28a745;"></span>L:{{ $item->individuels_livres }}
                        </span>
                        @endif
                        @if($item->individuels_annules > 0)
                        <span class="legend-item">
                            <span class="legend-color" style="background-color: #dc3545;"></span>A:{{ $item->individuels_annules }}
                        </span>
                        @endif
                    </div>
                    @endif
                </td>
                <td class="text-center">
                    @if($item->statut_paiement == 'non_paye')
                        <span class="payment-badge payment-unpaid">Non Payé</span>
                    @elseif($item->statut_paiement == 'partiellement_paye')
                        <span class="payment-badge payment-partial">Partiel</span>
                    @else
                        <span class="payment-badge payment-paid">Payé</span>
                    @endif
                </td>
                <td class="text-center">
                    {{ $item->created_at->format('d/m/Y') }}
                    <br>
                    <small>{{ $item->created_at->format('H:i') }}</small>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 20px;">
                    Aucun colis trouvé dans ce conteneur
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Statistiques -->
    <div class="total">
        @php
            $totalColis = $conteneur->colis->count();
            $totalMontant = $conteneur->colis->sum('montant_total');
            $colisPayes = $conteneur->colis->where('statut_paiement', 'totalement_paye')->count();
            $colisPartiels = $conteneur->colis->where('statut_paiement', 'partiellement_paye')->count();
            $colisNonPayes = $conteneur->colis->where('statut_paiement', 'non_paye')->count();
        @endphp
        
        Total: {{ $totalColis }} colis | 
        Montant total: {{ number_format($totalMontant, 0) }} FCFA |
        Payés: {{ $colisPayes }} | 
        Partiels: {{ $colisPartiels }} | 
        Non payés: {{ $colisNonPayes }}
    </div>

    <!-- Pied de page -->
    <div class="footer">
        Document généré automatiquement par le système AFT IMPORT EXPORT<br>
        © {{ date('Y') }} - Tous droits réservés
    </div>
</body>
</html>