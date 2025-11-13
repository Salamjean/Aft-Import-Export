@extends('admin.layouts.template')
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
                            <h3 class="card-title">Gestion des Colis</h3>
                            <p class="card-subtitle">Liste de tous les colis enregistrés</p>
                        </div>
                    </div>
                    <div class="header-actions">
                        <button onclick="downloadAllColisPDF()" class="btn modern-btn text-white" style="background-color:#dc3545; margin-right: 10px;">
                            <i class="fas fa-file-pdf"></i>
                            Télécharger PDF
                        </button>
                        <a href="{{ route('colis.create') }}" class="btn modern-btn text-white" style="background-color:#0e914b">
                            <i class="fas fa-plus"></i>
                            Nouveau Colis
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Filtres et recherche -->
                    <form method="GET" action="{{ route('colis.index') }}" class="table-controls">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" class="form-control modern-input" name="search" 
                                           value="{{ request('search') }}" placeholder="Rechercher un colis...">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="filter-group">
                                    <select class="form-control modern-select" name="status">
                                        <option value="">Tous les statuts</option>
                                        <option value="en_attente" {{ request('status') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                        <option value="traite" {{ request('status') == 'traite' ? 'selected' : '' }}>Traité</option>
                                        <option value="annule" {{ request('status') == 'annule' ? 'selected' : '' }}>Annulé</option>
                                    </select>
                                    <select class="form-control modern-select" name="mode_transit">
                                        <option value="">Tous les modes</option>
                                        <option value="Maritime" {{ request('mode_transit') == 'Maritime' ? 'selected' : '' }}>Maritime</option>
                                        <option value="Aerien" {{ request('mode_transit') == 'Aerien' ? 'selected' : '' }}>Aérien</option>
                                    </select>
                                    <select class="form-control modern-select" name="paiement">
                                        <option value="">Tous les paiements</option>
                                        <option value="non_paye" {{ request('paiement') == 'non_paye' ? 'selected' : '' }}>Non payé</option>
                                        <option value="partiellement_paye" {{ request('paiement') == 'partiellement_paye' ? 'selected' : '' }}>Partiellement payé</option>
                                        <option value="totalement_paye" {{ request('paiement') == 'totalement_paye' ? 'selected' : '' }}>Totalement payé</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Filtrer
                                    </button>
                                    <a href="{{ route('colis.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-refresh"></i> Réinitialiser
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Tableau des colis -->
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
                                @forelse($colis as $item)
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
                                        <span class="badge bg-primary text-white">
                                            {{ $item->nombre_types_colis }} type(s) de colis
                                        </span>
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
                                                valide
                                            @elseif($item->statut == 'traite')
                                                Traité
                                            @else
                                                Annulé
                                            @endif
                                        </span>
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
                                            
                                            <a href="{{ route('colis.edit', $item->id) }}" class="btn-action btn-edit" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn-action btn-delete" 
                                                    onclick="confirmDelete({{ $item->id }}, '{{ $item->reference_colis }}')" 
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">
                                        <div class="no-data-content py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <h4>Aucun colis trouvé</h4>
                                            <p class="text-muted">Commencez par créer votre premier colis</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($colis->hasPages())
                        <div class="pagination-container">
                            {{ $colis->links('pagination.modern') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inclure SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
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
.fas {
    transition: all 0.3s ease;
}

.fas:hover {
    transform: scale(1.2);
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

/* Contrôles de table */
.table-controls {
    margin-bottom: 25px;
}

.search-box {
    position: relative;
}

.search-box .fas {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--dark-gray);
}

.search-box .modern-input {
    padding-left: 45px;
}

.filter-group {
    display: flex;
    gap: 10px;
}

.filter-group .modern-select {
    flex: 1;
}

/* Table */
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

/* Badges */
.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

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

.paiement-non_paye {
    background-color: #f8d7da;
    color: #721c24;
}

.paiement-partiellement_paye {
    background-color: #fff3cd;
    color: #856404;
}

.paiement-totalement_paye {
    background-color: #d1edff;
    color: #0c63e4;
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

/* État vide */
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

/* Pagination */
.pagination-container {
    margin-top: 30px;
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    gap: 8px;
}

.page-link {
    border: none;
    border-radius: 6px;
    padding: 8px 16px;
    color: var(--text-color);
    transition: var(--transition);
}

.page-link:hover {
    background-color: var(--primary-color);
    color: var(--white);
}

.page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

/* Responsive */
@media (max-width: 768px) {
    .modern-header {
        flex-direction: column;
        text-align: center;
    }
    
    .header-content {
        justify-content: center;
    }
    
    .filter-group {
        flex-direction: column;
        margin-top: 15px;
    }
    
    .table-controls .row > div {
        margin-bottom: 15px;
    }
    
    .modern-table {
        font-size: 0.9rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<script>
// Fonction pour afficher les détails d'un colis avec SweetAlert2
function showColisDetails(colisId) {
    // Afficher un indicateur de chargement
    Swal.fire({
        title: 'Chargement...',
        text: 'Récupération des détails du colis',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(`/admin/parcel/${colisId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur lors du chargement des détails');
            }
            return response.json();
        })
        .then(data => {
            // Préparer l'affichage des statuts individuels
            const statutsIndividuelsHTML = data.statuts_individuels ? 
                generateStatutsIndividuelsHTML(data.statuts_individuels, data.compteur_statuts) : 
                '<div class="alert alert-warning">Aucun statut individuel disponible</div>';

            Swal.fire({
                title: `Détails du Colis - ${data.reference_colis}`,
                html: `
                    <div class="text-start">
                        <!-- Section Informations Générales (garder votre code existant) -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <strong>Expéditeur</strong>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-1"><strong>Nom:</strong> ${data.name_expediteur} ${data.prenom_expediteur || ''}</p>
                                        <p class="mb-1"><strong>Email:</strong> ${data.email_expediteur}</p>
                                        <p class="mb-1"><strong>Contact:</strong> ${data.contact_expediteur}</p>
                                        <p class="mb-0"><strong>Adresse:</strong> ${data.adresse_expediteur}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <strong>Destinataire</strong>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-1"><strong>Nom:</strong> ${data.name_destinataire} ${data.prenom_destinataire}</p>
                                        <p class="mb-1"><strong>Email:</strong> ${data.email_destinataire}</p>
                                        <p class="mb-1"><strong>Contact:</strong> ${data.indicatif} ${data.contact_destinataire}</p>
                                        <p class="mb-0"><strong>Adresse:</strong> ${data.adresse_destinataire}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <strong>Agences</strong>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-1"><strong>Expédition:</strong> ${data.agence_expedition}</p>
                                        <p class="mb-0"><strong>Destination:</strong> ${data.agence_destination}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <strong>Statuts Globaux</strong>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-1">
                                            <strong>Colis:</strong> 
                                            <span class="badge ${getStatusBadgeClass(data.statut)}">
                                                ${getStatusText(data.statut)}
                                            </span>
                                        </p>
                                        <p class="mb-0">
                                            <strong>Paiement:</strong> 
                                            <span class="badge ${getPaiementBadgeClass(data.statut_paiement)}">
                                                ${getPaiementText(data.statut_paiement)}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- NOUVELLE SECTION: Résumé des Statuts Individuels -->
                        <div class="card mb-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <strong>Résumé des Statuts Individuels</strong>
                                <span class="badge bg-primary">${data.total_individuels || 0} unité(s)</span>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    ${data.compteur_statuts ? `
                                        <div class="col">
                                            <div class="statut-counter ${data.compteur_statuts.valide > 0 ? 'text-success' : 'text-muted'}">
                                                <h4>${data.compteur_statuts.valide || 0}</h4>
                                                <small>Validé</small>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="statut-counter ${data.compteur_statuts.charge > 0 ? 'text-warning' : 'text-muted'}">
                                                <h4>${data.compteur_statuts.charge || 0}</h4>
                                                <small>Chargé</small>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="statut-counter ${data.compteur_statuts.entrepot > 0 ? 'text-info' : 'text-muted'}">
                                                <h4>${data.compteur_statuts.entrepot || 0}</h4>
                                                <small>Entrepôt</small>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="statut-counter ${data.compteur_statuts.decharge > 0 ? 'text-primary' : 'text-muted'}">
                                                <h4>${data.compteur_statuts.decharge || 0}</h4>
                                                <small>Déchargé</small>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="statut-counter ${data.compteur_statuts.livre > 0 ? 'text-success' : 'text-muted'}">
                                                <h4>${data.compteur_statuts.livre || 0}</h4>
                                                <small>Livré</small>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="statut-counter ${data.compteur_statuts.annule > 0 ? 'text-danger' : 'text-muted'}">
                                                <h4>${data.compteur_statuts.annule || 0}</h4>
                                                <small>Annulé</small>
                                            </div>
                                        </div>
                                    ` : '<div class="col-12 text-muted">Aucune donnée disponible</div>'}
                                </div>
                            </div>
                        </div>

                        <!-- SECTION: Détails des Statuts Individuels -->
                        <div class="card">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <strong>Détails des Unités Individuelles</strong>
                            </div>
                            <div class="card-body">
                                ${statutsIndividuelsHTML}
                            </div>
                        </div>

                        <!-- Section Informations Financières (garder votre code existant) -->
                        <div class="card mt-3">
                            <div class="card-header bg-light">
                                <strong>Informations Financières</strong>
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Montant Total:</strong> ${parseFloat(data.montant_total).toFixed(0)} ${data.devise}</p>
                                <p class="mb-1"><strong>Montant Payé:</strong> ${parseFloat(data.montant_paye).toFixed(0)} ${data.devise}</p>
                                <p class="mb-1"><strong>Reste à Payer:</strong> ${parseFloat(data.reste_a_payer || 0).toFixed(0)} ${data.devise}</p>
                                <p class="mb-0"><strong>Méthode Paiement:</strong> ${getMethodePaiementText(data.methode_paiement)}</p>
                            </div>
                        </div>

                        <!-- Section Détails des Types de Colis (garder votre code existant) -->
                        <div class="card mt-3">
                            <div class="card-header bg-light">
                                <strong>Détails des Types de Colis (${data.nombre_types_colis} type(s))</strong>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th class="text-center">Produit</th>
                                                <th class="text-center">Quantité</th>
                                                <th class="text-center">Prix Unitaire</th>
                                                <th class="text-center">Total</th>
                                                <th class="text-center">Dimensions</th>
                                                <th class="text-center">Poids</th>
                                                <th class="text-center">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${data.colis_details.map((colis, index) => `
                                                <tr>
                                                    <td class="text-center"><strong>${index + 1}</strong></td>
                                                    <td class="text-center">${colis.produit}</td>
                                                    <td class="text-center">${colis.quantite}</td>
                                                    <td class="text-center">${parseFloat(colis.prix_unitaire).toFixed(0)} ${data.devise}</td>
                                                    <td class="text-center">${parseFloat(colis.quantite * colis.prix_unitaire).toFixed(0)} ${data.devise}</td>
                                                    <td class="text-center">
                                                        ${colis.longueur && colis.largeur && colis.hauteur ? 
                                                            `${colis.longueur}x${colis.largeur}x${colis.hauteur} cm` : 
                                                            'Non spécifié'}
                                                    </td>
                                                    <td class="text-center">${colis.poids ? colis.poids + ' kg' : '--'}</td>
                                                    <td class="text-center">${colis.description || '--'}</td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-active">
                                                <td colspan="4" class="text-end"><strong>Total Colis:</strong></td>
                                                <td colspan="4"><strong>${parseFloat(data.montant_colis || data.montant_total).toFixed(0)} ${data.devise}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                `,
                width: 1000,
                showCloseButton: true,
                showConfirmButton: true,
                confirmButtonText: 'Fermer',
                confirmButtonColor: '#fea219'
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

// Fonction pour générer l'affichage des statuts individuels
function generateStatutsIndividuelsHTML(statutsIndividuels, compteurStatuts) {
    if (!statutsIndividuels || Object.keys(statutsIndividuels).length === 0) {
        return '<div class="alert alert-warning text-center">Aucun statut individuel disponible</div>';
    }

    let html = `
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th class="text-center">Code Colis</th>
                        <th class="text-center">Produit</th>
                        <th class="text-center">Unité</th>
                        <th class="text-center">Statut</th>
                        <th class="text-center">Localisation</th>
                        <th class="text-center">Date Modif.</th>
                    </tr>
                </thead>
                <tbody>
    `;

    // Convertir l'objet en tableau et trier par colis_numero et unite_numero
    const statutsArray = Object.values(statutsIndividuels).sort((a, b) => {
        if (a.colis_numero !== b.colis_numero) {
            return a.colis_numero - b.colis_numero;
        }
        return a.unite_numero - b.unite_numero;
    });

    statutsArray.forEach(statut => {
        html += `
            <tr>
                <td class="text-center">
                    <code class="bg-light p-1 rounded">${statut.code_colis}</code>
                </td>
                <td class="text-center">${statut.produit}</td>
                <td class="text-center">
                    <span class="badge bg-secondary text-white">Colis ${statut.colis_numero} - Unité ${statut.unite_numero}</span>
                </td>
                <td class="text-center text-white">
                    <span class="badge ${getStatutIndividuelBadgeClass(statut.statut)}">
                        ${getStatutIndividuelText(statut.statut)}
                    </span>
                </td>
                <td class="text-center">${statut.localisation_actuelle || 'Non spécifié'}</td>
                <td class="text-center">
                    <small>${formatDate(statut.date_modification)}</small>
                </td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    return html;
}

// Fonction pour obtenir la classe CSS du badge de statut individuel
function getStatutIndividuelBadgeClass(statut) {
    const classes = {
        'valide': 'bg-success',
        'charge': 'bg-warning',
        'entrepot': 'bg-info',
        'decharge': 'bg-primary',
        'livre': 'bg-success',
        'annule': 'bg-danger'
    };
    return classes[statut] || 'bg-secondary';
}

// Fonction pour obtenir le texte du statut individuel
function getStatutIndividuelText(statut) {
    const textes = {
        'valide': 'Validé',
        'charge': 'Chargé',
        'entrepot': 'Entrepôt',
        'decharge': 'Déchargé',
        'livre': 'Livré',
        'annule': 'Annulé'
    };
    return textes[statut] || statut;
}

// Fonction pour formater la date
function formatDate(dateString) {
    if (!dateString) return '--';
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR') + ' ' + date.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'});
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
        reverseButtons: true,
        customClass: {
            popup: 'sweet-popup'
        }
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
            fetch(`/admin/parcel/${colisId}`, {
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

// Fonctions utilitaires pour les textes et classes
function getStatusText(statut) {
    const statuts = {
        'en_attente': 'En attente',
        'traite': 'Traité',
        'annule': 'Annulé'
    };
    return statuts[statut] || statut;
}

function getStatusBadgeClass(statut) {
    const classes = {
        'en_attente': 'bg-warning',
        'traite': 'bg-success',
        'annule': 'bg-danger'
    };
    return classes[statut] || 'bg-secondary';
}

function getPaiementText(paiement) {
    const paiements = {
        'non_paye': 'Non payé',
        'partiellement_paye': 'Partiellement payé',
        'totalement_paye': 'Totalement payé'
    };
    return paiements[paiement] || paiement;
}

function getPaiementBadgeClass(paiement) {
    const classes = {
        'non_paye': 'bg-danger',
        'partiellement_paye': 'bg-warning',
        'totalement_paye': 'bg-success'
    };
    return classes[paiement] || 'bg-secondary';
}

function getMethodePaiementText(methode) {
    const methodes = {
        'espece': 'Espèce',
        'virement_bancaire': 'Virement Bancaire',
        'cheque': 'Chèque',
        'mobile_money': 'Mobile Money',
        'livraison': 'Paiement à la Livraison'
    };
    return methodes[methode] || methode;
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

    fetch(`/admin/parcel/${colisId}/paiement`, {
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
                    <div class="col-6">
                        <button class="btn btn-outline-primary w-100" onclick="generateDocument(${colisId}, '${type}', 'preview')">
                            <i class="fas fa-eye me-2"></i>
                            Aperçu
                        </button>
                    </div>
                    <div class="col-6">
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
            url = `/admin/parcel/${colisId}/etiquettes?action=${action}`;
            break;
        case 'facture':
            url = `/admin/parcel/${colisId}/facture?action=${action}`;
            break;
        case 'bon_livraison':
            url = `/admin/parcel/${colisId}/bon-livraison?action=${action}`;
            break;
    }
    
    // Redirection vers la route spécifique
    window.location.href = url;
}

// Fonction pour télécharger tous les colis en PDF
function downloadAllColisPDF() {
    // Afficher un indicateur de chargement
    Swal.fire({
        title: 'Génération du PDF...',
        text: 'Préparation du rapport de tous les colis',
        allowOutsideClick: true,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Récupérer les paramètres de filtres actuels
    const searchParams = new URLSearchParams(window.location.search);
    
    // Construire l'URL avec les paramètres
    let url = '/admin/parcel/export/pdf?' + searchParams.toString();

    // Redirection vers l'URL de génération du PDF
    window.location.href = url;
}

// Afficher les messages flash Laravel avec SweetAlert2
document.addEventListener('DOMContentLoaded', function() {
    // Message de succès
    @if(session('success'))
        Swal.fire({
            title: 'Succès !',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#fea219',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    // Message d'erreur
    @if(session('error'))
        Swal.fire({
            title: 'Erreur !',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#fea219'
        });
    @endif
});
</script>
@endsection