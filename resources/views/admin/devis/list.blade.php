@extends('admin.layouts.template')
@section('content')

<!-- Inclure SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
/* Variables de couleurs */
:root {
    --primary-color: #fea219;
    --secondary-color: #0e914b;
    --primary-light: #ffeacc;
    --secondary-light: #e6f4ee;
}

/* Styles personnalisés */
.empty-state {
    padding: 60px 0;
    text-align: center;
    background: #f8f9fa;
    border-radius: 15px;
    margin: 20px 0;
}

.empty-state i {
    opacity: 0.7;
    margin-bottom: 20px;
}

.badge {
    font-size: 0.75em;
    padding: 0.5em 0.8em;
    border-radius: 12px;
    font-weight: 600;
}

.table th {
    border-top: none;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color) 100%);
    color: white;
    padding: 15px 12px;
    font-size: 0.9em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    vertical-align: middle;
    padding: 15px 12px;
    border-bottom: 1px solid #f0f0f0;
}

.card {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, var(--secondary-color) 0%, var(--secondary-color) 100%);
    border-bottom: none;
    padding: 1.5rem 2rem;
    color: white;
}

.card-title {
    color: white;
    font-weight: 700;
    font-size: 1.5rem;
    margin: 0;
}

.btn-primary {
    background: linear-gradient(135deg, var(--secondary-color) 0%, var(--secondary-color) 100%);
    border: none;
    border-radius: 8px;
    padding: 10px 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(254, 162, 25, 0.4);
}

.btn-success {
    background: var(--secondary-color);
    border: none;
    border-radius: 8px;
    padding: 8px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background: #0a7a3d;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(14, 145, 75, 0.4);
}

.btn-group .btn {
    margin-right: 8px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    padding: 8px 20px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

/* Styles pour les badges de statut */
.badge-warning {
    background: linear-gradient(135deg, var(--primary-color) 0%, #e69500 100%);
    color: #fff;
}

.badge-success {
    background: linear-gradient(135deg, var(--secondary-color) 0%, #0a7a3d 100%);
    color: #fff;
}

.badge-danger {
    background: linear-gradient(135deg, #e74a3b 0%, #d52a1a 100%);
    color: #fff;
}

.badge-info {
    background: linear-gradient(135deg, #258391 0%, #258391 100%);
    color: #fff;
}

.badge-primary {
    background: linear-gradient(135deg, var(--secondary-color) 0%, var(--secondary-color) 100%);
    color: #fff;
}

.badge-light {
    background: #f8f9fc;
    color: #3a3b45;
    border: 1px solid #e3e6f0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

/* Styles pour les formulaires */
.form-control {
    border-radius: 8px;
    border: 1px solid #e3e6f0;
    padding: 10px 15px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(254, 162, 25, 0.25);
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

/* Styles pour les alertes */
.alert {
    border: none;
    border-radius: 10px;
    border-left: 5px solid;
    padding: 1.25rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.alert-success {
    background-color: var(--secondary-light);
    border-left-color: var(--secondary-color);
    color: #0a7a3d;
}

.alert-info {
    background-color: #f0f7ff;
    border-left-color: #36b9cc;
    color: #0c5460;
}

.alert-warning {
    background-color: var(--primary-light);
    border-left-color: var(--primary-color);
    color: #856404;
}

/* Styles pour la pagination */
.pagination {
    margin-bottom: 0;
}

.pagination .page-link {
    color: var(--secondary-color);
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    margin: 0 3px;
    padding: 8px 15px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, var(--secondary-color) 0%, var(--secondary-color) 100%);
    border-color: var(--secondary-color);
}

.pagination .page-link:hover {
    background-color: #eaecf4;
    border-color: #e3e6f0;
    transform: translateY(-1px);
}

/* Effets de hover sur les lignes du tableau */
.table-hover tbody tr {
    transition: all 0.3s ease;
    border-radius: 8px;
}

.table-hover tbody tr:hover {
    background: linear-gradient(135deg, var(--primary-light) 0%, var(--secondary-light) 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Styles pour les boutons d'action */
.btn-outline-primary {
    color: var(--secondary-color);
    border-color: var(--secondary-color);
    border-radius: 6px;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: linear-gradient(135deg, var(--secondary-color) 0%, var(--secondary-color) 100%);
    border-color: var(--secondary-color);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(14, 145, 75, 0.3);
}

.btn-outline-success {
    color: var(--secondary-color);
    border-color: var(--secondary-color);
    border-radius: 6px;
    transition: all 0.3s ease;
}

.btn-outline-success:hover {
    background: var(--secondary-color);
    border-color: var(--secondary-color);
    color: white;
    transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 768px) {
    .table-responsive {
        border: 1px solid #e3e6f0;
        border-radius: 10px;
    }
    
    .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .btn-group .btn {
        flex: 1;
        min-width: 140px;
        margin-right: 0;
    }
    
    .card-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .modal-dialog {
        margin: 20px;
    }
}

/* Animation pour les modaux */
.modal.fade .modal-dialog {
    transform: translate(0, -30px) scale(0.95);
    transition: all 0.3s ease;
}

.modal.show .modal-dialog {
    transform: translate(0, 0) scale(1);
}

/* Styles pour les textes */
.text-success {
    color: var(--secondary-color) !important;
    font-weight: 600;
}

.text-muted {
    color: #6c757d !important;
}

/* Section informations */
.info-section {
    background: var(--secondary-light);
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    border-left: 4px solid var(--secondary-color);
}

.info-section h6 {
    color: var(--secondary-color);
    font-weight: 700;
    margin-bottom: 15px;
}

/* Statut badges */
.status-badge {
    font-size: 0.9em;
    padding: 0.6em 1em;
    border-radius: 20px;
}

/* Loading animation */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-right: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #e69500 0%, #0a7a3d 100%);
}

/* SweetAlert2 personnalisation */
.swal2-popup {
    border-radius: 15px !important;
}

.swal2-confirm {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
    border: none !important;
    border-radius: 8px !important;
}

/* Highlight pour les nouvelles demandes */
.new-request {
    background: linear-gradient(135deg, #fff9e6 0%, #f0f9f4 100%) !important;
    border-left: 4px solid var(--primary-color);
}

/* Formulaire de devis */
.devis-form {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #e3e6f0;
}
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-file-invoice me-2"></i>
                        Demandes de Devis en Attente
                    </h4>
                    <div class="d-flex align-items-center">
                        <span class="badge badge-warning me-3">
                            <i class="fas fa-clock me-1"></i>
                            {{ $devisEnAttente->total() }} demande(s) en attente
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Statistiques -->
                    {{-- <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $totalDevis }}</h4>
                                            <p class="mb-0">Total Devis</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-file-invoice fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $devisEnAttente->total() }}</h4>
                                            <p class="mb-0">En Attente</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $devisTraites }}</h4>
                                            <p class="mb-0">Traités</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $devisAnnules }}</h4>
                                            <p class="mb-0">Annulés</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-times fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    @if($devisEnAttente->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center text-white">Date Demande</th>
                                        <th class="text-center text-white">Client</th>
                                        <th class="text-center text-white">Mode Transit</th>
                                        <th class="text-center text-white">Pays Expédition</th>
                                        <th class="text-center text-white">Agence Destination</th>
                                        <th class="text-center text-white">Nombre Colis</th>
                                        <th class="text-center text-white">Valeur Totale</th>
                                        <th class="text-center text-white">Statut</th>
                                        <th class="text-center text-white">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($devisEnAttente as $devi)
                                        <tr class="{{ $devi->created_at->diffInHours(now()) < 24 ? 'new-request' : '' }}">
                                            <td class="text-center">
                                                <div class="d-flex flex-column">
                                                    <span class="font-weight-bold">{{ $devi->created_at->format('d/m/Y') }}</span>
                                                    <small class="text-muted">{{ $devi->created_at->format('H:i') }}</small>
                                                    @if($devi->created_at->diffInHours(now()) < 24)
                                                        <small class="text-warning"><i class="fas fa-star me-1"></i>Nouveau</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex flex-column">
                                                    <strong>{{ $devi->name_client }} {{ $devi->prenom_client }}</strong>
                                                    <small class="text-muted">{{ $devi->email_client }}</small>
                                                    <small class="text-muted">{{ $devi->contact_client }}</small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($devi->mode_transit == 'Maritime')
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-ship me-1"></i>Maritime
                                                    </span>
                                                @elseif($devi->mode_transit == 'Aerien')
                                                    <span class="badge badge-primary">
                                                        <i class="fas fa-plane me-1"></i>Aérien
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $devi->mode_transit }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $devi->pays_expedition }}</td>
                                            <td class="text-center">{{ $devi->agence_destination }}</td>
                                            <td class="text-center">
                                                @php
                                                    $nombreColis = is_array($devi->colis) ? count($devi->colis) : 0;
                                                    $valeurTotale = 0;
                                                    if (is_array($devi->colis)) {
                                                        foreach ($devi->colis as $colis) {
                                                            $valeurTotale += floatval($colis['valeur'] ?? 0);
                                                        }
                                                    }
                                                @endphp
                                                <span class="badge badge-light">
                                                    <i class="fas fa-box me-1"></i>{{ $nombreColis }} colis
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <strong class="text-success">
                                                    {{ number_format($valeurTotale, 0, ',', ' ') }} {{ $devi->devise }}
                                                </strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-warning status-badge">
                                                    <i class="fas fa-clock me-1"></i>En attente
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="showDevisDetails({{ $devi->id }})">
                                                        <i class="fas fa-eye me-1"></i> Voir
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-success"
                                                            onclick="showDevisForm({{ $devi->id }}, '{{ $devi->devise }}')">
                                                        <i class="fas fa-euro-sign me-1"></i> Devis
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                       @if($devisEnAttente->hasPages())
                            <div class="pagination-container">
                                {{ $devisEnAttente->links('pagination.modern') }}
                            </div>
                        @endif

                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                <h3 class="text-success mb-3">Aucune demande en attente</h3>
                                <p class="text-muted mb-4">
                                    Toutes les demandes de devis ont été traitées.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inclure SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Fonction pour afficher les détails d'un devis
function showDevisDetails(devisId) {
    fetch(`/admin/quote/${devisId}/details`)
        .then(response => response.json())
        .then(devis => {
            let colisHtml = '';
            let totalValeur = 0;
            
            if (devis.colis && Array.isArray(devis.colis)) {
                devis.colis.forEach((colis, index) => {
                    const valeur = parseFloat(colis.valeur || 0);
                    totalValeur += valeur;
                    
                    colisHtml += `
                        <tr>
                            <td class="text-center">${colis.produit || 'N/A'}</td>
                            <td class="text-center">${colis.quantite || 'N/A'}</td>
                            <td class="text-center">${valeur.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0})} ${devis.devise}</td>
                            <td class="text-center">${colis.type_colis || 'N/A'}</td>
                            <td class="text-center">
                                ${colis.longueur && colis.largeur && colis.hauteur ? 
                                    `${colis.longueur}x${colis.largeur}x${colis.hauteur} cm` : 
                                    'N/A'}
                            </td>
                            <td class="text-center">${colis.description || 'Aucune'}</td>
                        </tr>
                    `;
                });
            }

            Swal.fire({
                title: `<h4 style="color: #0e914b;">Détails du Devis #${devis.id}</h4>`,
                html: `
                    <div class="text-start">
                        <div class="info-section">
                            <h6 style="color: #0e914b;"><i class="fas fa-shipping-fast me-2"></i>Informations de Transport</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Mode de Transit:</strong> 
                                        ${devis.mode_transit === 'maritime' ? 
                                            '<span class="badge badge-info"><i class="fas fa-ship me-1"></i>Maritime</span>' : 
                                            '<span class="badge badge-primary"><i class="fas fa-plane me-1"></i>Aérien</span>'}
                                    </p>
                                    <p><strong>Pays d'Expédition:</strong> ${devis.pays_expedition}</p>
                                    <p><strong>Agence Expédition:</strong> ${devis.agence_expedition}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Agence Destination:</strong> ${devis.agence_destination}</p>
                                    <p><strong>Devise:</strong> ${devis.devise}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-section">
                            <h6 style="color: #0e914b;"><i class="fas fa-user me-2"></i>Informations Client</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nom:</strong> ${devis.name_client} ${devis.prenom_client}</p>
                                    <p><strong>Email:</strong> ${devis.email_client}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Contact:</strong> ${devis.contact_client}</p>
                                    <p><strong>Adresse:</strong> ${devis.adresse_client}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-section">
                            <h6 style="color: #0e914b;"><i class="fas fa-boxes me-2"></i>Détails des Colis</h6>
                            ${devis.colis && devis.colis.length > 0 ? `
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-white text-center">Produit</th>
                                                <th class="text-white text-center">Quantité</th>
                                                <th class="text-white text-center">Valeur</th>
                                                <th class="text-white text-center">Type Colis</th>
                                                <th class="text-white text-center">Dimensions</th>
                                                <th class="text-white text-center">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${colisHtml}
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2"><strong>Total Valeur</strong></td>
                                                <td colspan="4"><strong>${totalValeur.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0})} ${devis.devise}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            ` : `
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Aucun détail de colis disponible.
                                </div>
                            `}
                        </div>
                    </div>
                `,
                width: 900,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Fermer'
            });
        })
        .catch(error => {
            console.error('Erreur:', error);
            Swal.fire({
                title: 'Erreur',
                text: 'Impossible de charger les détails du devis',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
}

// Fonction pour afficher le formulaire de devis avec SweetAlert2
function showDevisForm(devisId, devise) {
    // Récupérer les détails du devis pour afficher le récapitulatif
    fetch(`/admin/quote/${devisId}/details`)
        .then(response => response.json())
        .then(devis => {
            // Calculer la valeur totale des colis
            let totalValeur = 0;
            let nombreColis = 0;
            if (devis.colis && Array.isArray(devis.colis)) {
                nombreColis = devis.colis.length;
                devis.colis.forEach(colis => {
                    totalValeur += parseFloat(colis.valeur || 0);
                });
            }

            const deviseSymbol = devise === 'EUR' ? '€' : devise;

            Swal.fire({
                title: `<h4 style="color: #0e914b;">Créer un Devis</h4>`,
                html: `
                    <div class="text-start">
                        <div class="alert alert-info mb-3">
                            <h6><i class="fas fa-info-circle me-2"></i>Récapitulatif de la demande</h6>
                            <p><strong>Client:</strong> ${devis.name_client} ${devis.prenom_client}</p>
                            <p><strong>Transport:</strong> ${devis.mode_transit} - ${devis.pays_expedition}</p>
                            <p><strong>Colis:</strong> ${nombreColis} colis - Valeur totale: ${totalValeur.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0})} ${devis.devise}</p>
                            <p><strong>Agence:</strong> ${devis.agence_destination}</p>
                        </div>
                        
                        <form id="montantForm">
                            <div class="form-group">
                                <label class="form-label"><strong>Montant du Devis *</strong></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="montantInput" name="montant_devis" required placeholder="Saisir le montant">
                                    <span class="input-group-text">${deviseSymbol}</span>
                                </div>
                                <small class="form-text text-muted">Montant total TTC pour le transport</small>
                            </div>
                        </form>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Valider le Devis',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#0e914b',
                cancelButtonColor: '#6c757d',
                preConfirm: () => {
                    const montant = document.getElementById('montantInput').value;
                    if (!montant || montant <= 0) {
                        Swal.showValidationMessage('Veuillez saisir un montant valide');
                        return false;
                    }
                    return { montant: montant };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Afficher le loading
                    Swal.fire({
                        title: 'Validation en cours...',
                        html: '<div class="loading-spinner"></div> Traitement du devis',
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });

                    // Envoyer la requête de validation
                    const formData = new FormData();
                    formData.append('montant_devis', result.value.montant);
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch(`/admin/quote/${devisId}/valider`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Succès !',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#0e914b'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Erreur !',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        Swal.fire({
                            title: 'Erreur',
                            text: 'Une erreur est survenue lors de la validation du devis',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        })
        .catch(error => {
            console.error('Erreur:', error);
            Swal.fire({
                title: 'Erreur',
                text: 'Impossible de charger les données du devis',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
}

// Message de bienvenue avec SweetAlert2
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            title: 'Succès !',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#0e914b',
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Erreur !',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#e74a3b'
        });
    @endif
});
</script>

@endsection