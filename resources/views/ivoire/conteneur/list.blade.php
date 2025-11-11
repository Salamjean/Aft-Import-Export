@extends('ivoire.layouts.template')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card modern-card">
                <div class="card-header modern-header">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="header-text">
                            <h3 class="card-title">Colis du Conteneur: {{ $conteneur->name_conteneur }}</h3>
                            <p class="card-subtitle">Liste de tous les colis associés à ce conteneur</p>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('agent.cote.conteneur.history') }}" class="btn modern-btn text-white" style="background-color:#6c757d">
                            <i class="fas fa-arrow-left"></i>
                            Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informations du conteneur -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-card">
                                <h6>Nom du Conteneur</h6>
                                <p>{{ $conteneur->name_conteneur }}</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="info-card">
                                <h6>Type</h6>
                                <span class="badge type-badge type-{{ strtolower($conteneur->type_conteneur) }}">
                                    {{ $conteneur->type_conteneur }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="info-card">
                                <h6>Statut</h6>
                                <span class="status-badge status-{{ $conteneur->statut }}">
                                    <i class="fas {{ $conteneur->statut == 'ouvert' ? 'fa-unlock' : 'fa-lock' }}"></i>
                                    {{ ucfirst($conteneur->statut) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="info-card">
                                <h6>Nombre de Colis</h6>
                                <span class="badge badge-primary">{{ $conteneur->colis->count() }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-card">
                                <h6>Date de création</h6>
                                <p>{{ $conteneur->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des colis avec le style de la deuxième vue -->
                    <div class="table-responsive">
                        <table class="table modern-table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="font-size:12px">St. Paiement</th>
                                    <th class="text-center" style="font-size:12px">Référence</th>
                                    <th class="text-center" style="font-size:12px">Expéditeur</th>
                                    <th class="text-center" style="font-size:12px">Destinataire</th>
                                    <th class="text-center" style="font-size:12px">Mode Transit</th>
                                    <th class="text-center" style="font-size:12px">Agences</th>
                                    <th class="text-center" style="font-size:12px">Montant Total</th>
                                    <th class="text-center" style="font-size:12px">St. Colis</th>
                                    <th class="text-center" style="font-size:12px">Date</th>
                                    <th class="text-center" style="font-size:12px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($conteneur->colis as $item)
                                <tr>
                                    <td class="text-center">
                                        @if($item->statut_paiement == 'non_paye')
                                            <i class="fas fa-times-circle text-danger" title="Non payé" style="font-size: 18px;"></i>
                                        @elseif($item->statut_paiement == 'partiellement_paye')
                                            <i class="fas fa-exclamation-circle text-warning" title="Partiellement payé" style="font-size: 18px;"></i>
                                        @else
                                            <i class="fas fa-check-circle text-success" title="Totalement payé" style="font-size: 18px;"></i>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ $item->reference_colis }}</strong>
                                        <br>
                                    </td>
                                    <td class="text-center">
                                        <div>{{ $item->name_expediteur }} {{ $item->prenom_expediteur ?? '' }}</div>
                                        <small class="text-muted">{{ $item->contact_expediteur }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div>{{ $item->name_destinataire }} {{ $item->prenom_destinataire }}</div>
                                        <small class="text-muted">{{ $item->contact_destinataire }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge mode-{{ strtolower($item->mode_transit) }}">
                                            {{ $item->mode_transit }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div><strong>Exp:</strong> {{ $item->agence_expedition }}</div>
                                        <div><strong>Dest:</strong> {{ $item->agence_destination }}</div>
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
                                        <br>
                                        
                                        <!-- Barre de progression multi-couleurs -->
                                        @if($item->total_individuels > 0)
                                        <div class="progress-multi mt-1">
                                            @php
                                                $pourcentageValide = ($item->individuels_valides / $item->total_individuels) * 100;
                                                $pourcentageCharge = ($item->individuels_charges / $item->total_individuels) * 100;
                                                $pourcentageEntrepot = ($item->individuels_entrepot / $item->total_individuels) * 100;
                                                $pourcentageDecharge = ($item->individuels_decharges / $item->total_individuels) * 100;
                                                $pourcentageLivre = ($item->individuels_livres / $item->total_individuels) * 100;
                                                $pourcentageAnnule = ($item->individuels_annules / $item->total_individuels) * 100;
                                            @endphp
                                            
                                            @if($item->individuels_valides > 0)
                                            <div class="progress-segment progress-valide" style="width: {{ $pourcentageValide }}%"></div>
                                            @endif
                                            
                                            @if($item->individuels_charges > 0)
                                            <div class="progress-segment progress-charge" style="width: {{ $pourcentageCharge }}%"></div>
                                            @endif
                                            
                                            @if($item->individuels_entrepot > 0)
                                            <div class="progress-segment progress-entrepot" style="width: {{ $pourcentageEntrepot }}%"></div>
                                            @endif
                                            
                                            @if($item->individuels_decharges > 0)
                                            <div class="progress-segment progress-decharge" style="width: {{ $pourcentageDecharge }}%"></div>
                                            @endif
                                            
                                            @if($item->individuels_livres > 0)
                                            <div class="progress-segment progress-livre" style="width: {{ $pourcentageLivre }}%"></div>
                                            @endif
                                            
                                            @if($item->individuels_annules > 0)
                                            <div class="progress-segment progress-annule" style="width: {{ $pourcentageAnnule }}%"></div>
                                            @endif
                                        </div>

                                        <!-- Légende -->
                                        <div class="statut-legend">
                                            @if($item->individuels_valides > 0)
                                            <span class="statut-legend-item">
                                                <span class="statut-color" style="background-color: #6c757d;"></span>V:{{ $item->individuels_valides }}
                                            </span>
                                            @endif
                                            @if($item->individuels_charges > 0)
                                            <span class="statut-legend-item">
                                                <span class="statut-color" style="background-color: #ffc107;"></span>C:{{ $item->individuels_charges }}
                                            </span>
                                            @endif
                                            @if($item->individuels_entrepot > 0)
                                            <span class="statut-legend-item">
                                                <span class="statut-color" style="background-color: #17a2b8;"></span>E:{{ $item->individuels_entrepot }}
                                            </span>
                                            @endif
                                            @if($item->individuels_decharges > 0)
                                            <span class="statut-legend-item">
                                                <span class="statut-color" style="background-color: #007bff;"></span>D:{{ $item->individuels_decharges }}
                                            </span>
                                            @endif
                                            @if($item->individuels_livres > 0)
                                            <span class="statut-legend-item">
                                                <span class="statut-color" style="background-color: #28a745;"></span>L:{{ $item->individuels_livres }}
                                            </span>
                                            @endif
                                            @if($item->individuels_annules > 0)
                                            <span class="statut-legend-item">
                                                <span class="statut-color" style="background-color: #dc3545;"></span>A:{{ $item->individuels_annules }}
                                            </span>
                                            @endif
                                        </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $item->created_at->format('d/m/Y') }}
                                        <br>
                                        <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons">
                                            <button class="btn-action btn-view" onclick="showColisDetails({{ $item->id }})" title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <!-- Bouton de paiement - grisé si totalement payé -->
                                            <button class="btn-action {{ $item->statut_paiement == 'totalement_paye' ? 'btn-payment-disabled' : 'btn-payment' }}" 
                                                    @if($item->statut_paiement != 'totalement_paye')
                                                    onclick="showPaymentForm({{ $item->id }}, '{{ $item->reference_colis }}', {{ $item->montant_total }}, {{ $item->montant_paye }}, {{ $item->reste_a_payer ?? 0 }}, '{{ $item->devise }}')"
                                                    @else
                                                    disabled
                                                    @endif
                                                    title="{{ $item->statut_paiement == 'totalement_paye' ? 'Paiement complet' : 'Enregistrer un paiement' }}">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </button>
                                            
                                            <!-- Nouveau bouton pour les documents -->
                                            <button class="btn-action btn-labels" 
                                                    onclick="showDocumentsOptions({{ $item->id }})" 
                                                    title="Télécharger les documents">
                                                <i class="fas fa-file-alt"></i>
                                            </button>
                                            
                                            {{-- <a href="{{ route('colis.edit', $item->id) }}" class="btn-action btn-edit" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a> --}}
                                            {{-- <button class="btn-action btn-delete" 
                                                    onclick="confirmDelete({{ $item->id }}, '{{ $item->reference_colis }}')" 
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button> --}}
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">
                                        <div class="no-data-content py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <h4>Aucun colis trouvé</h4>
                                            <p class="text-muted">Ce conteneur ne contient aucun colis pour le moment</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
}

.info-card h6 {
    color: #6c757d;
    font-size: 0.8rem;
    margin-bottom: 5px;
    text-transform: uppercase;
}

.info-card p {
    margin: 0;
    font-weight: 600;
    color: #333;
}

.colis-statut {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: bold;
}

.statut-valide { background: #d4edda; color: #155724; }
.statut-charge { background: #cce7ff; color: #004085; }
.statut-decharge { background: #fff3cd; color: #856404; }
.statut-livre { background: #d1ecf1; color: #0c5460; }
.statut-annule { background: #f8d7da; color: #721c24; }

/* Reprendre les styles CSS de la vue précédente */
:root {
    --primary-color: #fea219;
    --primary-dark: #e8910c;
    --white: #ffffff;
    --light-gray: #f8f9fa;
    --medium-gray: #e9ecef;
    --dark-gray: #6c757d;
    --text-color: #333333;
    --border-radius: 12px;
    --box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}

.modern-card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
    margin-top: 30px;
}

.modern-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: var(--white);
    border: none;
    padding: 25px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 15px;
}

.modern-header .header-icon {
    font-size: 2rem;
}

.modern-header .card-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.modern-header .card-subtitle {
    opacity: 0.9;
    font-size: 1rem;
    margin: 0;
}

.card-body {
    padding: 30px;
}

.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.modern-table thead th {
    background-color: var(--light-gray);
    color: var(--text-color);
    font-weight: 600;
    padding: 10px;
    border-bottom: 2px solid var(--medium-gray);
    text-transform: uppercase;
    font-size: 10px;
    letter-spacing: 0.5px;
}

.modern-table tbody tr {
    transition: var(--transition);
}

.modern-table tbody tr:hover {
    background-color: rgba(254, 162, 25, 0.05);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.modern-table tbody td {
    padding: 12px 15px;
    border-bottom: 1px solid var(--medium-gray);
    vertical-align: middle;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.type-badge {
    background-color: var(--light-gray);
    color: var(--text-color);
}

.type-conteneur {
    background-color: #e3f2fd;
    color: #1976d2;
}

.type-ballon {
    background-color: #f3e5f5;
    color: #7b1fa2;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.status-ouvert {
    background-color: #e8f5e8;
    color: #2e7d32;
}

.status-fermer {
    background-color: #ffebee;
    color: #c62828;
}

.badge-primary {
    background-color: #007bff;
    color: white;
}

/* Styles pour les badges de statuts */
.status-valide {
    background-color: #fff3cd;
    color: #856404;
}

.status-traite {
    background-color: #d1edff;
    color: #0c63e4;
}

.status-annule {
    background-color: #f8d7da;
    color: #721c24;
}

.mode-maritime {
    background-color: #e3f2fd;
    color: #1976d2;
}

.mode-aerien {
    background-color: #f3e5f5;
    color: #7b1fa2;
}

/* Actions */
.action-buttons {
    display: flex;
    gap: 5px;
}

.btn-action {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    cursor: pointer;
    font-size: 0.8rem;
}

.btn-view {
    background-color: #e8f5e8;
    color: #2e7d32;
}

.btn-view:hover {
    background-color: #2e7d32;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(46, 125, 50, 0.3);
}

.btn-edit {
    background-color: #e3f2fd;
    color: #1976d2;
}

.btn-edit:hover {
    background-color: #1976d2;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(25, 118, 210, 0.3);
}

.btn-delete {
    background-color: #ffebee;
    color: #c62828;
}

.btn-delete:hover {
    background-color: #c62828;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(198, 40, 40, 0.3);
}

.btn-payment {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.btn-payment:hover {
    background-color: #2e7d32;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(46, 125, 50, 0.3);
}

.btn-labels {
    background-color: #e3f2fd;
    color: #1976d2;
}

.btn-labels:hover {
    background-color: #1976d2;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(25, 118, 210, 0.3);
}

.no-data {
    padding: 60px 20px;
}

.no-data-content {
    text-align: center;
    color: var(--dark-gray);
}

.no-data-content .fas {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

.no-data-content h4 {
    margin-bottom: 10px;
    color: var(--text-color);
}

.no-data-content p {
    margin-bottom: 25px;
}

.modern-btn {
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 600;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.modern-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Barre de progression multi-couleurs */
.progress-multi {
    height: 8px;
    background-color: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.progress-segment {
    height: 100%;
    display: inline-block;
}

.progress-valide { background-color: #6c757d; }
.progress-charge { background-color: #ffc107; }
.progress-entrepot { background-color: #17a2b8; }
.progress-decharge { background-color: #007bff; }
.progress-livre { background-color: #28a745; }
.progress-annule { background-color: #dc3545; }

/* Légende des statuts */
.statut-legend {
    font-size: 9px;
    margin-top: 2px;
}

.statut-legend-item {
    display: inline-flex;
    align-items: center;
    margin-right: 8px;
}

.statut-color {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 3px;
}
</style>

<!-- Inclure SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Fonction pour afficher les détails d'un colis
function showColisDetails(colisId) {
    // Afficher un loader pendant le chargement
    Swal.fire({
        title: 'Chargement...',
        text: 'Récupération des détails du colis',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Appel AJAX pour récupérer les détails du colis
    fetch(`/agent/container/${colisId}/details`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            Swal.close();
            
            // Construire le HTML pour les détails du colis
            let detailsHTML = `
                <div class="colis-details">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h6>Informations du Colis</h6>
                                <div class="detail-item">
                                    <strong>Référence:</strong> ${data.reference_colis || 'N/A'}
                                </div>
                                <div class="detail-item">
                                    <strong>Statut:</strong> 
                                    <span class="colis-statut statut-${data.statut}">
                                        ${data.statut}
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <strong>Mode de transit:</strong> ${data.mode_transit}
                                </div>
                                <div class="detail-item">
                                    <strong>Devise:</strong> ${data.devise}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h6>Informations Financières</h6>
                                <div class="detail-item">
                                    <strong>Montant total:</strong> ${data.montant_total ? Number(data.montant_total).toLocaleString('fr-FR', {minimumFractionDigits: 0}) : '0.00'} ${data.devise}
                                </div>
                                <div class="detail-item">
                                    <strong>Montant payé:</strong> ${data.montant_paye ? Number(data.montant_paye).toLocaleString('fr-FR', {minimumFractionDigits: 0}) : '0.00'} ${data.devise}
                                </div>
                                <div class="detail-item">
                                    <strong>Reste à payer:</strong> ${data.reste_a_payer ? Number(data.reste_a_payer).toLocaleString('fr-FR', {minimumFractionDigits: 0}) : '0.00'} ${data.devise}
                                </div>
                                <div class="detail-item">
                                    <strong>Statut paiement:</strong> 
                                    <span class="badge ${getPaymentStatusBadge(data.statut_paiement)}">
                                        ${getPaymentStatusText(data.statut_paiement)}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h6>Expéditeur</h6>
                                <div class="detail-item">
                                    <strong>Nom:</strong> ${data.name_expediteur} ${data.prenom_expediteur || ''}
                                </div>
                                <div class="detail-item">
                                    <strong>Email:</strong> ${data.email_expediteur}
                                </div>
                                <div class="detail-item">
                                    <strong>Contact:</strong> ${data.contact_expediteur}
                                </div>
                                <div class="detail-item">
                                    <strong>Adresse:</strong> ${data.adresse_expediteur}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h6>Destinataire</h6>
                                <div class="detail-item">
                                    <strong>Nom:</strong> ${data.name_destinataire} ${data.prenom_destinataire}
                                </div>
                                <div class="detail-item">
                                    <strong>Email:</strong> ${data.email_destinataire}
                                </div>
                                <div class="detail-item">
                                    <strong>Contact:</strong> ${data.indicatif} ${data.contact_destinataire}
                                </div>
                                <div class="detail-item">
                                    <strong>Adresse:</strong> ${data.adresse_destinataire}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h6>Agences</h6>
                                <div class="detail-item">
                                    <strong>Agence expédition:</strong> ${data.agence_expedition}
                                </div>
                                <div class="detail-item">
                                    <strong>Agence destination:</strong> ${data.agence_destination}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h6>Informations Supplémentaires</h6>
                                <div class="detail-item">
                                    <strong>Date création:</strong> ${new Date(data.created_at).toLocaleDateString('fr-FR')}
                                </div>
                                <div class="detail-item">
                                    <strong>Dernière modification:</strong> ${new Date(data.updated_at).toLocaleDateString('fr-FR')}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            Swal.fire({
                title: 'Détails du Colis',
                html: detailsHTML,
                width: '800px',
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'colis-details-popup'
                }
            });
        })
        .catch(error => {
            console.error('Erreur:', error);
            Swal.fire({
                title: 'Erreur',
                text: 'Impossible de charger les détails du colis',
                icon: 'error',
                confirmButtonColor: '#fea219'
            });
        });
}

// Fonctions utilitaires pour le statut de paiement
function getPaymentStatusBadge(status) {
    switch(status) {
        case 'totalement_paye':
            return 'badge-success';
        case 'partiellement_paye':
            return 'badge-warning';
        case 'non_paye':
        default:
            return 'badge-danger';
    }
}

function getPaymentStatusText(status) {
    switch(status) {
        case 'totalement_paye':
            return 'Totalement payé';
        case 'partiellement_paye':
            return 'Partiellement payé';
        case 'non_paye':
        default:
            return 'Non payé';
    }
}

// Fonction de confirmation de suppression avec SweetAlert2
function confirmDelete(colisId, reference) {
    Swal.fire({
        title: 'Êtes-vous sûr ?',
        html: `
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                <p>Vous êtes sur le point de supprimer le colis :</p>
                <p><strong>"${reference}"</strong></p>
                <p class="text-danger">Cette action est irréversible !</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Oui, supprimer !',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Afficher un indicateur de chargement
            Swal.fire({
                title: 'Suppression en cours...',
                text: 'Veuillez patienter',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Envoyer la requête de suppression
            fetch(`/agent/colis/${colisId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors de la suppression');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Supprimé !',
                        text: 'Le colis a été supprimé avec succès',
                        icon: 'success',
                        confirmButtonColor: '#fea219',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Recharger la page
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Erreur lors de la suppression');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                Swal.fire({
                    title: 'Erreur !',
                    text: 'Une erreur est survenue lors de la suppression',
                    icon: 'error',
                    confirmButtonColor: '#fea219'
                });
            });
        }
    });
}

// Fonction pour afficher le formulaire de paiement
function showPaymentForm(colisId, reference, montantTotal, montantPaye, resteAPayer, devise) {
    const montantRestant = parseFloat(resteAPayer) || (parseFloat(montantTotal) - parseFloat(montantPaye));
    
    Swal.fire({
        title: `Enregistrer un Paiement`,
        html: `
            <div class="text-start">
                <div class="alert alert-info">
                    <strong>Référence:</strong> ${reference}<br>
                    <strong>Montant Total:</strong> ${parseFloat(montantTotal).toFixed(0)} ${devise}<br>
                    <strong>Déjà Payé:</strong> ${parseFloat(montantPaye).toFixed(0)} ${devise}<br>
                    <strong>Reste à Payer:</strong> <span class="text-success fw-bold">${montantRestant.toFixed(0)} ${devise}</span>
                </div>
                
                <form id="paymentForm">
                    <div class="mb-3">
                        <label for="montant" class="form-label"><strong>Montant du Paiement *</strong></label>
                        <input type="number" class="form-control" id="montant" 
                               min="0.01" max="${montantRestant}" step="0.01"
                               placeholder="Entrez le montant payé" required>
                        <div class="form-text">Maximum: ${montantRestant.toFixed(0)} ${devise}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="methode_paiement" class="form-label"><strong>Méthode de Paiement *</strong></label>
                        <select class="form-control" id="methode_paiement" required>
                            <option value="">Sélectionnez une méthode</option>
                            <option value="espece">Espèce</option>
                            <option value="virement_bancaire">Virement Bancaire</option>
                            <option value="cheque">Chèque</option>
                            <option value="mobile_money">Mobile Money</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="banque_fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="nom_banque" class="form-label">Nom de la Banque</label>
                                <input type="text" class="form-control" id="nom_banque" placeholder="Nom de la banque">
                            </div>
                            <div class="col-md-6">
                                <label for="numero_compte" class="form-label">Numéro de Compte</label>
                                <input type="text" class="form-control" id="numero_compte" placeholder="Numéro de compte">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3" id="mobile_fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="operateur" class="form-label">Opérateur</label>
                                <select class="form-control" id="operateur">
                                    <option value="">Sélectionnez un opérateur</option>
                                    <option value="WAVE">WAVE</option>
                                    <option value="ORANGE">ORANGE</option>
                                    <option value="MOOV">MOOV</option>
                                    <option value="MTN">MTN</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="numero_mobile" class="form-label">Numéro</label>
                                <input type="text" class="form-control" id="numero_mobile" placeholder="Numéro de téléphone">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optionnel)</label>
                        <textarea class="form-control" id="notes" rows="2" placeholder="Notes supplémentaires..."></textarea>
                    </div>
                </form>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Enregistrer le Paiement',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#fea219',
        cancelButtonColor: '#6c757d',
        didOpen: () => {
            // Gestion de l'affichage des champs conditionnels
            const methodeSelect = document.getElementById('methode_paiement');
            const banqueFields = document.getElementById('banque_fields');
            const mobileFields = document.getElementById('mobile_fields');
            
            methodeSelect.addEventListener('change', function() {
                if (this.value === 'virement_bancaire') {
                    banqueFields.style.display = 'block';
                    mobileFields.style.display = 'none';
                } else if (this.value === 'mobile_money') {
                    banqueFields.style.display = 'none';
                    mobileFields.style.display = 'block';
                } else {
                    banqueFields.style.display = 'none';
                    mobileFields.style.display = 'none';
                }
            });
        },
        preConfirm: () => {
            const montant = parseFloat(document.getElementById('montant').value);
            const methode = document.getElementById('methode_paiement').value;
            
            if (!montant || montant <= 0) {
                Swal.showValidationMessage('Veuillez entrer un montant valide');
                return false;
            }
            
            if (montant > montantRestant) {
                Swal.showValidationMessage(`Le montant ne peut pas dépasser ${montantRestant.toFixed(0)} ${devise}`);
                return false;
            }
            
            if (!methode) {
                Swal.showValidationMessage('Veuillez sélectionner une méthode de paiement');
                return false;
            }
            
            return {
                montant: montant,
                methode_paiement: methode,
                nom_banque: document.getElementById('nom_banque').value,
                numero_compte: document.getElementById('numero_compte').value,
                operateur: document.getElementById('operateur').value,
                numero_mobile: document.getElementById('numero_mobile').value,
                notes: document.getElementById('notes').value
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Envoyer les données du paiement
            processPayment(colisId, result.value, reference);
        }
    });
}

// Fonction pour traiter le paiement
function processPayment(colisId, paymentData, reference) {
    Swal.fire({
        title: 'Traitement en cours...',
        text: 'Enregistrement du paiement',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(`/agent/parcel/${colisId}/paiement`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(paymentData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur lors de l\'enregistrement du paiement');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Paiement Enregistré !',
                text: `Le paiement de ${parseFloat(paymentData.montant).toFixed(0)} a été enregistré avec succès`,
                icon: 'success',
                confirmButtonColor: '#fea219',
                timer: 3000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Erreur lors de l\'enregistrement');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        Swal.fire({
            title: 'Erreur !',
            text: 'Une erreur est survenue lors de l\'enregistrement du paiement',
            icon: 'error',
            confirmButtonColor: '#fea219'
        });
    });
}

// Fonction pour afficher les options de documents
function showDocumentsOptions(colisId) {
    Swal.fire({
        title: 'Options de Documents',
        html: `
            <div class="text-center">
                <i class="fas fa-file-alt text-primary" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <p class="mb-4">Choisissez un document à générer</p>
                
                <div class="row g-3">
                    <div class="col-12">
                        <button class="btn btn-primary w-100 py-3" onclick="previewDocument(${colisId}, 'etiquette')">
                            <i class="fas fa-tags me-2"></i>
                            Télécharger Étiquette
                        </button>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-success w-100 py-3" onclick="previewDocument(${colisId}, 'facture')">
                            <i class="fas fa-file-invoice me-2"></i>
                            Télécharger Facture
                        </button>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-info w-100 py-3" onclick="previewDocument(${colisId}, 'bon_livraison')">
                            <i class="fas fa-truck me-2"></i>
                            Télécharger Bon de Livraison
                        </button>
                    </div>
                </div>
            </div>
        `,
        showConfirmButton: false,
        showCloseButton: true,
        width: 500
    });
}

// Fonction pour prévisualiser avant téléchargement
function previewDocument(colisId, type) {
    let title = '';
    let icon = '';
    
    switch(type) {
        case 'etiquette':
            title = 'Étiquette du Colis';
            icon = 'fas fa-tags';
            break;
        case 'facture':
            title = 'Facture';
            icon = 'fas fa-file-invoice';
            break;
        case 'bon_livraison':
            title = 'Bon de Livraison';
            icon = 'fas fa-truck';
            break;
    }
    
    Swal.fire({
        title: `Aperçu - ${title}`,
        html: `
            <div class="text-center">
                <i class="${icon} text-primary" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                <p class="mb-3">Voulez-vous voir l'aperçu avant de télécharger ?</p>
                <div class="row g-2">
                    <div class="col-12">
                        <button class="btn btn-primary w-100" onclick="generateDocument(${colisId}, '${type}', 'download')">
                            <i class="fas fa-download me-2"></i>
                            Télécharger
                        </button>
                    </div>
                </div>
            </div>
        `,
        showConfirmButton: false,
        showCloseButton: true,
        width: 500
    });
}

// Fonction pour générer les documents
function generateDocument(colisId, type, action) {
    let url = '';
    
    switch(type) {
        case 'etiquette':
            url = `/agent/parcel/${colisId}/etiquettes?action=${action}`;
            break;
        case 'facture':
            url = `/agent/parcel/${colisId}/facture?action=${action}`;
            break;
        case 'bon_livraison':
            url = `/agent/parcel/${colisId}/bon-livraison?action=${action}`;
            break;
    }
    
    // Redirection vers la route spécifique
    window.location.href = url;
}
</script>

<style>
/* Styles supplémentaires pour le modal de détails */
.colis-details-popup .swal2-popup {
    max-width: 800px;
}

.detail-section {
    margin-bottom: 15px;
}

.detail-section h6 {
    color: #fea219;
    border-bottom: 2px solid #fea219;
    padding-bottom: 5px;
    margin-bottom: 10px;
    font-weight: 600;
}

.detail-item {
    margin-bottom: 8px;
    padding: 5px 0;
    border-bottom: 1px solid #f0f0f0;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-item strong {
    color: #333;
    min-width: 120px;
    display: inline-block;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}
</style>
@endsection