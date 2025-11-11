@extends('user.layouts.template')
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
    background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
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

/* Styles pour les boutons de filtre */
.btn-group .btn.active {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-outline-secondary.active {
    background: #6c757d;
    border-color: #6c757d;
    color: white;
}

.btn-outline-warning.active {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.btn-outline-success.active {
    background: var(--secondary-color);
    border-color: var(--secondary-color);
    color: white;
}

.btn-outline-danger.active {
    background: #e74a3b;
    border-color: #e74a3b;
    color: white;
}

/* Styles pour les modaux */
.modal-header {
    background: linear-gradient(135deg, var(--secondary-color) 0%, var(--secondary-color) 100%);
    color: white;
    border-bottom: none;
    padding: 1.5rem 2rem;
}

.modal-header .close {
    color: white;
    opacity: 0.8;
    font-size: 1.5rem;
}

.modal-header .close:hover {
    opacity: 1;
    transform: scale(1.1);
}

.modal-title {
    font-weight: 700;
    font-size: 1.3rem;
}

.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

/* Styles pour les tableaux dans les modaux */
.table-sm th {
    background: #f8f9fa;
    color: #495057;
    font-weight: 600;
}

.table-bordered {
    border: 1px solid #e3e6f0;
    border-radius: 10px;
    overflow: hidden;
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
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
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

.btn-outline-danger {
    color: #e74a3b;
    border-color: #e74a3b;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.btn-outline-danger:hover {
    background: linear-gradient(135deg, #e74a3b 0%, #d52a1a 100%);
    border-color: #e74a3b;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(231, 74, 59, 0.3);
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

/* Statut badges dans le modal */
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
    background: linear-gradient(135deg, #0a7a3d 0%, #0a7a3d 100%);
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

.swal2-cancel {
    background: #6c757d !important;
    border: none !important;
    border-radius: 8px !important;
}
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-file-invoice me-2"></i>
                        Toutes Mes Demandes de Devis
                    </h4>
                    <a href="{{ route('user.devis.create') }}" class="btn" style="background-color: white">
                        <i class="fas fa-plus me-2"></i> Nouvelle Demande
                    </a>
                </div>
                <div class="card-body">
                    
                    <!-- Filtres par statut -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="btn-group" role="group">
                                <a href="{{ route('user.devis.attente', ['statut' => 'en_attente']) }}" 
                                   class="btn btn-outline-warning {{ request('statut') == 'en_attente' ? 'active' : '' }}">
                                    <i class="fas fa-clock me-2"></i>En Attente ({{ $devisEnAttente }})
                                </a>
                                <a href="{{ route('user.devis.confirmed') }}" 
                                   class="btn btn-outline-success {{ request('statut') == 'traite' ? 'active' : '' }}">
                                    <i class="fas fa-check me-2"></i>Traités ({{ $devisTraites }})
                                </a>
                            </div>
                        </div>
                    </div>

                    @if($devis->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-white text-center">Date Demande</th>
                                        <th class="text-white text-center">Mode Transit</th>
                                        <th class="text-white text-center">Pays Expédition</th>
                                        <th class="text-white text-center">Agence Expédition</th>
                                        <th class="text-white text-center">Nombre Colis</th>
                                        <th class="text-white text-center">Montant Devis</th>
                                        <th class="text-white text-center">Statut</th>
                                        <th class="text-white text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($devis as $devi)
                                        <tr>
                                            <td class="text-center">
                                                <div class="d-flex flex-column">
                                                    <span class="font-weight-bold">{{ $devi->created_at->format('d/m/Y') }}</span>
                                                    <small class="text-muted">{{ $devi->created_at->format('H:i') }}</small>
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
                                                @endphp
                                                <span class="badge badge-light">
                                                    <i class="fas fa-box me-1"></i>{{ $nombreColis }} colis
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($devi->montant_devis)
                                                    <strong class="text-success">
                                                        {{ number_format($devi->montant_devis, 0, ',', ' ') }} {{ $devi->devise }}
                                                    </strong>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="fas fa-clock me-1"></i>En attente
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($devi->statut == 'en_attente')
                                                    <span class="badge badge-warning status-badge">
                                                        <i class="fas fa-clock me-1"></i>En attente
                                                    </span>
                                                @elseif($devi->statut == 'traite')
                                                    <span class="badge badge-success status-badge">
                                                        <i class="fas fa-check me-1"></i>Traité
                                                    </span>
                                                @elseif($devi->statut == 'annule')
                                                    <span class="badge badge-danger status-badge">
                                                        <i class="fas fa-times me-1"></i>Annulé
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    @if($devi->statut == 'en_attente')
                                                        <!-- Bouton Accepter pour les devis en attente -->
                                                        <button type="button" class="btn btn-sm btn-success"
                                                                onclick="accepterDevis({{ $devi->id }}, '{{ $devi->id }}')">
                                                            <i class="fas fa-check me-1"></i> Accepter
                                                        </button>
                                                    @else
                                                        <!-- Bouton Détails pour les devis traités -->
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                onclick="showDevisDetails({{ $devi->id }})">
                                                            <i class="fas fa-eye me-1"></i> Détails
                                                        </button>
                                                    @endif
                                                    
                                                    @if($devi->statut == 'en_attente')
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                                onclick="confirmAnnulation({{ $devi->id }}, '{{ $devi->id }}')">
                                                            <i class="fas fa-times me-1"></i> Annuler
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                         @if($devis->hasPages())
                            <div class="pagination-container">
                                {{ $devis->links('pagination.modern') }}
                            </div>
                        @endif

                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-file-invoice fa-4x text-muted mb-3"></i>
                                <h3 class="text-muted mb-3">Aucune demande de devis</h3>
                                <p class="text-muted mb-4">
                                    @if(request('statut'))
                                        Vous n'avez aucune demande de devis avec le statut "{{ request('statut') }}".
                                    @else
                                        Vous n'avez encore fait aucune demande de devis.
                                    @endif
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
// Fonction pour afficher les détails d'un devis avec les vraies valeurs
function showDevisDetails(devisId) {
    // Récupérer les données du devis depuis le serveur
    fetch(`/user/quote/${devisId}/details`)
        .then(response => response.json())
        .then(devis => {
            // Construire le HTML des colis
            let colisHtml = '';
            let totalValeur = 0;
            
            if (devis.colis && Array.isArray(devis.colis)) {
                devis.colis.forEach((colis, index) => {
                    const valeur = parseFloat(colis.valeur || 0);
                    totalValeur += valeur;
                    
                    colisHtml += `
                        <tr>
                            <td>${colis.produit || 'N/A'}</td>
                            <td>${colis.quantite || 'N/A'}</td>
                            <td>${valeur.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0})} ${devis.devise}</td>
                            <td>${colis.type_colis || 'N/A'}</td>
                            <td>
                                ${colis.longueur && colis.largeur && colis.hauteur ? 
                                    `${colis.longueur}x${colis.largeur}x${colis.hauteur} cm` : 
                                    'N/A'}
                            </td>
                            <td>${colis.description || 'Aucune'}</td>
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
                                        ${devis.mode_transit === 'Maritime' ? 
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
                                                <th>Produit</th>
                                                <th>Quantité</th>
                                                <th>Valeur</th>
                                                <th>Type Colis</th>
                                                <th>Dimensions</th>
                                                <th>Description</th>
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
                        
                        ${devis.montant_devis ? `
                            <div class="alert alert-success">
                                <h6><i class="fas fa-euro-sign me-2"></i>Devis Proposé</h6>
                                <p class="mb-0"><strong>Montant:</strong> ${parseFloat(devis.montant_devis).toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0})} ${devis.devise}</p>
                                ${devis.statut === 'traite' ? `
                                    <small class="text-muted">Devis traité le ${new Date(devis.updated_at).toLocaleDateString('fr-FR')}</small>
                                ` : ''}
                            </div>
                        ` : `
                            <div class="alert alert-info">
                                <p class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    ${devis.statut === 'en_attente' ? 
                                        'Votre demande est en cours de traitement. Vous serez notifié dès que le devis sera disponible.' : 
                                        'Cette demande a été annulée.'}
                                </p>
                            </div>
                        `}
                    </div>
                `,
                width: 900,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Fermer',
                customClass: {
                    popup: 'border-radius-15'
                }
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

// Fonction pour confirmer l'annulation avec SweetAlert2
function confirmAnnulation(devisId, devisNumber) {
    Swal.fire({
        title: 'Êtes-vous sûr ?',
        html: `
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                <p>Vous êtes sur le point d'annuler la demande de devis <strong>#${devisNumber}</strong></p>
                <p class="text-muted">Cette action est irréversible.</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Oui, annuler !',
        cancelButtonText: 'Non, garder',
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Afficher un loading
            Swal.fire({
                title: 'Annulation en cours...',
                html: '<div class="loading-spinner"></div> Traitement de votre demande',
                showConfirmButton: false,
                allowOutsideClick: false
            });

            // Créer le formulaire pour l'annulation
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/user/devis/${devisId}/annuler`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
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

    // Animation pour les cartes
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Fonction pour accepter un devis
function accepterDevis(devisId, devisNumber) {
    Swal.fire({
        title: 'Mode de livraison',
        html: `
            <div class="text-start">
                <p class="mb-3">Comment souhaitez-vous procéder pour l'envoi de vos colis ?</p>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="mode_livraison" id="depot_agence" value="depot_agence" checked>
                    <label class="form-check-label" for="depot_agence">
                        <strong>Envoye en agence</strong><br>
                        <small class="text-muted">Vous déposez vos colis dans notre agence</small>
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="mode_livraison" id="passe_recuperer" value="passe_recuperer">
                    <label class="form-check-label" for="passe_recuperer">
                        <strong>Retrait à domicile</strong><br>
                        <small class="text-muted">Notre agence passe récupérer vos colis</small>
                    </label>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Confirmer',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#0e914b',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        preConfirm: () => {
            const modeLivraison = document.querySelector('input[name="mode_livraison"]:checked').value;
            return { mode_livraison: modeLivraison };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Afficher un loading
            Swal.fire({
                title: 'Acceptation en cours...',
                html: '<div class="loading-spinner"></div> Traitement de votre demande',
                showConfirmButton: false,
                allowOutsideClick: false
            });

            // Envoyer la requête d'acceptation avec le mode de livraison
            fetch(`/user/quote/${devisId}/accepter`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    mode_livraison: result.value.mode_livraison
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let successMessage = 'Devis accepté avec succès !';
                    if (data.reference) {
                        successMessage += `<br><strong>Référence : ${data.reference}</strong>`;
                    }
                    
                    Swal.fire({
                        title: 'Succès !',
                        html: successMessage,
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
                    text: 'Une erreur est survenue lors de l\'acceptation du devis',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}
</script>

@endsection