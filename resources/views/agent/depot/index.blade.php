@extends('agent.layouts.template')
@section('content')
<div class="container-fluid">
    <!-- Header avec statistiques -->
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <div class="page-header bg-gradient-primary rounded-3 p-4 shadow">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="text-white mb-2">📦 Programmes de Dépôt</h1>
                        <p class="text-white-50 mb-0">Gérez et suivez tous vos programmes de dépôt</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('agent.depot.create') }}" class="btn btn-light btn-lg rounded-pill px-4">
                            <i class="fas fa-plus-circle me-2"></i>Nouveau Programme
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-primary text-white mb-4 shadow-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $totalDepots ?? '0' }}</h4>
                            <p class="card-text">Total Dépôts</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-inbox fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-warning text-white mb-4 shadow-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $programmesCount ?? '0' }}</h4>
                            <p class="card-text">Programmés</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-success text-white mb-4 shadow-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $enCoursCount ?? '0' }}</h4>
                            <p class="card-text">En Cours</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-truck fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-info text-white mb-4 shadow-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $terminesCount ?? '0' }}</h4>
                            <p class="card-text">Terminés</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="row">
        <div class="col-12">
            <div class="card modern-card shadow border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="card-title mb-0 text-primary">
                                <i class="fas fa-list me-2"></i>Liste des Programmes
                            </h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-outline-secondary btn-sm" onclick="resetFilters()">
                                <i class="fas fa-redo me-1"></i>Réinitialiser
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtres -->
                    <div class="row mb-4 g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Chauffeur</label>
                            <select class=" modern-select" id="filter-chauffeur">
                                <option value="">Tous les chauffeurs</option>
                                @foreach($chauffeurs as $chauffeur)
                                    <option value="{{ $chauffeur->id }}">{{ $chauffeur->nom }} {{ $chauffeur->prenom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Statut</label>
                            <select class=" modern-select" id="filter-statut">
                                <option value="">Tous les statuts</option>
                                <option value="programme">Programmé</option>
                                <option value="en_cours">En cours</option>
                                <option value="termine">Terminé</option>
                                <option value="annule">Annulé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" class=" modern-input" id="filter-date">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Recherche</label>
                            <input type="text" class=" modern-input" id="filter-search" placeholder="Référence, nom...">
                        </div>
                    </div>

                    <!-- Tableau -->
                    <div class="table-responsive">
                        <table class="table table-hover modern-table" id="depots-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="text-align: center" class="ps-4">Référence</th>
                                    <th style="text-align: center">Chauffeur</th>
                                    <th style="text-align: center">Nature Objet</th>
                                    <th style="text-align: center">Quantité</th>
                                    <th style="text-align: center">Destinataire</th>
                                    <th style="text-align: center">Contact</th>
                                    <th style="text-align: center">Date Programme</th>
                                    <th style="text-align: center">Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($depots as $depot)
                                    <tr class="depot-row" 
                                        data-chauffeur="{{ $depot->chauffeur_id }}" 
                                        data-statut="{{ $depot->statut }}" 
                                        data-date="{{ $depot->date_depot ? \Carbon\Carbon::parse($depot->date_depot)->format('Y-m-d') : '' }}"
                                        data-search="{{ strtolower($depot->reference . ' ' . $depot->nom_concerne . ' ' . $depot->prenom_concerne . ' ' . $depot->nature_objet) }}">
                                        <td style="display: flex; justify-content:center" class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="reference-badge bg-primary text-white rounded-circle me-3">
                                                    <i class="fas fa-box"></i>
                                                </div>
                                                <div>
                                                    <strong class="d-block">{{ $depot->reference }}</strong>
                                                    @if($depot->quantite > 1)
                                                        <small class="text-muted">{{ $depot->quantite }} codes</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td style="text-align: center">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                                <span>{{ $depot->chauffeur->nom }} {{ $depot->chauffeur->prenom }}</span>
                                            </div>
                                        </td>
                                        <td style="text-align: center">
                                            <span class="badge bg-light text-dark border">{{ $depot->nature_objet }}</span>
                                        </td>
                                        <td style="text-align: center">
                                            <span class="quantity-badge">{{ $depot->quantite }}</span>
                                        </td>
                                        <td style="text-align: center">
                                            <strong>{{ $depot->nom_concerne }} {{ $depot->prenom_concerne }}</strong>
                                            <br><small class="text-muted">{{ Str::limit($depot->adresse_depot, 30) }}</small>
                                        </td>
                                        <td style="text-align: center">
                                            <div>
                                                <i class="fas fa-phone text-success me-1"></i>
                                                {{ $depot->contact }}
                                            </div>
                                            @if($depot->email)
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    {{ Str::limit($depot->email, 20) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td style="text-align: center">
                                            @if($depot->date_depot)
                                                <div class="date-cell">
                                                    <i class="fas fa-calendar me-1 text-primary"></i>
                                                    {{ \Carbon\Carbon::parse($depot->date_depot)->format('d/m/Y') }}
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center">
                                            <span class="status-badge status-{{ $depot->statut }}">
                                                {{ $depot->statut }}
                                            </span>
                                        </td>
                                        <td style="text-align: center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-sm btn-outline-primary btn-action" 
                                                        onclick="showDetails({{ $depot->id }})" 
                                                        title="Détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning btn-action" 
                                                        onclick="editDepot({{ $depot->id }})" 
                                                        title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-success btn-action" 
                                                                onclick="showEtiquettesOptions({{ $depot->id }})" 
                                                                title="Étiquettes">
                                                            <i class="fas fa-tag"></i>
                                                        </button>
                                                    </div>
                                                <button class="btn btn-sm btn-outline-danger btn-action" 
                                                        onclick="confirmDelete({{ $depot->id }})" 
                                                        title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">Aucun programme de dépôt trouvé</h5>
                                                <p class="text-muted">Commencez par créer votre premier programme de dépôt</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($depots->hasPages())
                            <div class="pagination-container">
                                {{ $depots->links('pagination.modern') }}
                            </div>
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Détails - Version CSS Pure -->
<div id="detailsModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header bg-primary">
            <h5 class="custom-modal-title">
                <i class="fas fa-info-circle me-2"></i>Détails du Programme
            </h5>
            <button type="button" class="custom-close" onclick="closeDetailsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="custom-modal-body" id="detailsContent">
            <!-- Contenu chargé dynamiquement -->
        </div>
    </div>
</div>



<!-- Modal Étiquettes - Version CSS Pure -->
<div id="etiquettesModal" class="custom-modal">
    <div class="custom-modal-content large">
        <div class="custom-modal-header bg-success">
            <h5 class="custom-modal-title">
                <i class="fas fa-tags me-2"></i>Étiquettes du Dépôt
            </h5>
            <button type="button" class="custom-close" onclick="closeEtiquettesModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="custom-modal-body" id="etiquettesContent">
            <!-- Contenu des étiquettes chargé dynamiquement -->
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeEtiquettesModal()">Fermer</button>
            <button type="button" class="btn btn-success" onclick="downloadCurrentEtiquettes()">
                <i class="fas fa-download me-2"></i>Télécharger PDF
            </button>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Inclure SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
:root {
    --primary-color: #fea219;
    --secondary-color: #0d8644;
    --accent-color: #0d8644;
}

/* En-tête */
.page-header {
    background: linear-gradient(135deg, var(--primary-color), #ffb74d) !important;
}

/* Cartes de statistiques */
.stat-card {
    border: none;
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Modals CSS Pure */
.custom-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    animation: fadeIn 0.3s;
}

.custom-modal-content {
    background-color: white;
    margin: 5% auto;
    border-radius: 15px;
    width: 90%;
    max-width: 800px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    animation: slideIn 0.3s;
}

.custom-modal-content.large {
    max-width: 95%;
}

.custom-modal-header {
    padding: 1.5rem;
    border-radius: 15px 15px 0 0;
    color: white;
    display: flex;
    justify-content: between;
    align-items: center;
}

.custom-modal-title {
    margin: 0;
    font-size: 1.25rem;
}

.custom-close {
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background-color 0.3s;
}

.custom-close:hover {
    background-color: rgba(255,255,255,0.2);
}

.custom-modal-body {
    padding: 2rem;
    max-height: 70vh;
    overflow-y: auto;
}

.custom-modal-footer {
    padding: 1.5rem;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { 
        opacity: 0;
        transform: translateY(-50px);
    }
    to { 
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .custom-modal-content {
        margin: 10% auto;
        width: 95%;
    }
    
    .custom-modal-body {
        padding: 1rem;
        max-height: 60vh;
    }
}

.stat-card:hover {
    transform: translateY(-5px);
}

.shadow-hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.stat-card.bg-warning {
    background: linear-gradient(135deg, var(--primary-color), #ffb74d) !important;
}

.stat-card.bg-success {
    background: linear-gradient(135deg, var(--secondary-color), #0daa5e) !important;
}

.stat-icon {
    opacity: 0.8;
}

/* Carte principale */
.modern-card {
    border-radius: 15px;
    border: none;
}

.modern-table {
    border-collapse: separate;
    border-spacing: 0;
}

.modern-table thead th {
    border: none;
    font-weight: 600;
    color: #2c3e50;
    padding: 1rem 0.75rem;
    background-color: #f8f9fa;
}

.modern-table tbody tr {
    transition: all 0.3s ease;
}

.modern-table tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
}

/* Badges et éléments UI */
.reference-badge {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
}

.quantity-badge {
    background: var(--primary-color);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8rem;
}

.status-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-programmé { background: #e3f2fd; color: #1976d2; }
.status-en_cours { background: #fff3e0; color: #f57c00; }
.status-terminé { background: #e8f5e8; color: var(--secondary-color); }
.status-annulé { background: #ffebee; color: #c62828; }

/* Boutons */
.btn-action {
    border-radius: 8px;
    padding: 0.375rem 0.75rem;
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Formulaires */
.modern-select, .modern-input {
    border-radius: 10px;
    border: 1px solid #e0e0e0;
    padding: 0.75rem;
    transition: all 0.3s ease;
}

.modern-select:focus, .modern-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(254, 162, 25, 0.25);
}

/* État vide */
.empty-state {
    padding: 3rem 1rem;
}

.avatar-sm {
    width: 32px;
    height: 32px;
}

.date-cell {
    font-weight: 500;
    color: #2c3e50;
}

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        text-align: center;
    }
    
    .stat-card .card-body {
        padding: 1rem;
    }
    
    .btn-action {
        padding: 0.25rem 0.5rem;
    }
}
</style>

<script>
// Variables globales
let currentDepotId = null;
let isDeleting = false; // Pour bloquer les doubles clics

// Initialisation quand le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Système de dépôts initialisé avec SweetAlert2');
    initializeFilters();
});

// Filtres
function initializeFilters() {
    const filters = ['filter-chauffeur', 'filter-statut', 'filter-date', 'filter-search'];
    filters.forEach(filterId => {
        const element = document.getElementById(filterId);
        if (element) {
            element.addEventListener('change', applyFilters);
            element.addEventListener('input', applyFilters);
        }
    });
}

function applyFilters() {
    const chauffeurId = document.getElementById('filter-chauffeur').value;
    const statut = document.getElementById('filter-statut').value;
    const date = document.getElementById('filter-date').value;
    const search = document.getElementById('filter-search').value.toLowerCase();
    
    const rows = document.querySelectorAll('.depot-row');
    
    rows.forEach(row => {
        let showRow = true;
        
        if (chauffeurId && row.dataset.chauffeur !== chauffeurId) {
            showRow = false;
        }
        
        if (statut && row.dataset.statut !== statut) {
            showRow = false;
        }
        
        if (date && row.dataset.date !== date) {
            showRow = false;
        }
        
        if (search && !row.dataset.search.includes(search)) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('filter-chauffeur').value = '';
    document.getElementById('filter-statut').value = '';
    document.getElementById('filter-date').value = '';
    document.getElementById('filter-search').value = '';
    applyFilters();
}

// Détails du dépôt avec SweetAlert2
async function showDetails(depotId) {
    console.log('Chargement des détails pour:', depotId);
    
    // Afficher un SweetAlert de chargement
    Swal.fire({
        title: 'Chargement...',
        text: 'Récupération des détails du dépôt',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const response = await fetch(`/agent/schedule/${depotId}/details`);
        const data = await response.json();
        
        if (data.success && data.data) {
            Swal.close();
            displayDetailsModal(data.data);
        } else {
            throw new Error(data.error || 'Données non disponibles');
        }
    } catch (error) {
        console.error('Erreur:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Erreur lors du chargement des détails: ' + error.message,
            confirmButtonColor: '#fea219'
        });
    }
}

function displayDetailsModal(depotData) {
    let codesHtml = '';
    if (depotData.codes_qr && depotData.codes_qr.length > 0) {
        const codes = depotData.codes_qr.map(code => 
            `<span class="badge bg-light text-dark border me-2 mb-2">${code.code}</span>`
        ).join('');
        
        codesHtml = `
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-qrcode me-2"></i>Codes Générés (${depotData.quantite})
                    </h6>
                    <div class="bg-light rounded p-3">
                        ${codes}
                    </div>
                </div>
            </div>
        `;
    }
    
    const statutClass = `status-${depotData.statut}`;
    
    // Afficher directement dans une SweetAlert au lieu du modal Bootstrap
    Swal.fire({
        title: `Détails - ${depotData.reference}`,
        html: `
            <div class="text-start">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-box me-2"></i>Informations du Dépôt
                        </h6>
                        <div class="mb-2"><strong>Référence:</strong> ${depotData.reference}</div>
                        <div class="mb-2"><strong>Nature:</strong> ${depotData.nature_objet}</div>
                        <div class="mb-2"><strong>Quantité:</strong> <span class="quantity-badge">${depotData.quantite}</span></div>
                        <div class="mb-2"><strong>Statut:</strong> <span class="status-badge ${statutClass}">${depotData.statut}</span></div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-user me-2"></i>Destinataire
                        </h6>
                        <div class="mb-2"><strong>Nom:</strong> ${depotData.nom_concerne} ${depotData.prenom_concerne}</div>
                        <div class="mb-2"><strong>Contact:</strong> ${depotData.contact}</div>
                        <div class="mb-2"><strong>Email:</strong> ${depotData.email || 'Non renseigné'}</div>
                        <div class="mb-2"><strong>Adresse:</strong> ${depotData.adresse_depot}</div>
                    </div>
                </div>
                ${codesHtml}
                <div class="mt-3 text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Créé le ${depotData.created_at} | Chauffeur: ${depotData.chauffeur}
                </div>
            </div>
        `,
        width: 800,
        confirmButtonColor: '#fea219',
        confirmButtonText: 'Fermer'
    });
}

// Gestion des étiquettes
function showEtiquettesOptions(depotId) {
    Swal.fire({
        title: 'Options Étiquettes',
        text: 'Que souhaitez-vous faire avec les étiquettes ?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-eye me-2"></i>Voir les étiquettes',
        cancelButtonText: '<i class="fas fa-download me-2"></i>Télécharger PDF',
        showDenyButton: true,
        denyButtonText: 'Annuler',
        confirmButtonColor: '#0d8644',
        cancelButtonColor: '#fea219',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            showEtiquettes(depotId);
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            downloadEtiquettes(depotId);
        }
    });
}

function showEtiquettes(depotId) {
    currentDepotId = depotId;
    
    Swal.fire({
        title: 'Ouverture des étiquettes',
        text: 'Les étiquettes vont s\'ouvrir dans un nouvel onglet...',
        icon: 'info',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true
    });
    
    setTimeout(() => {
        window.open(`/agent/schedule/${depotId}/etiquettes`, '_blank');
        
        Swal.fire({
            title: 'Étiquettes ouvertes',
            text: 'Les étiquettes ont été ouvertes dans un nouvel onglet.',
            icon: 'success',
            confirmButtonColor: '#0d8644',
            confirmButtonText: 'OK'
        });
    }, 1600);
}

function downloadEtiquettes(depotId) {
    Swal.fire({
        title: 'Téléchargement',
        text: 'Préparation du fichier PDF...',
        icon: 'info',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true
    });
    
    setTimeout(() => {
        window.open(`/agent/schedule/${depotId}/download-etiquettes`, '_blank');
        
        Swal.fire({
            title: 'Téléchargement démarré',
            text: 'Le fichier PDF devrait commencer à télécharger',
            icon: 'success',
            confirmButtonColor: '#0d8644',
            confirmButtonText: 'OK'
        });
    }, 1600);
}

function downloadCurrentEtiquettes() {
    if (currentDepotId) {
        downloadEtiquettes(currentDepotId);
    }
}

// Suppression - VERSION CORRIGÉE
function confirmDelete(depotId) {
    // Bloquer les doubles clics
    if (isDeleting) {
        return;
    }

    Swal.fire({
        title: 'Êtes-vous sûr ?',
        html: `
            <div class="text-start">
                <p>Cette action supprimera définitivement ce programme de dépôt !</p>
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Attention :</strong> Cette action est irréversible.
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fas fa-trash me-2"></i>Oui, supprimer !',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Annuler',
        reverseButtons: true,
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            isDeleting = true;
            deleteDepot(depotId);
        }
    });
}

async function deleteDepot(depotId) {
    try {
        // Afficher un indicateur de chargement
        Swal.fire({
            title: 'Suppression en cours...',
            text: 'Veuillez patienter',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Utiliser FormData pour envoyer les données
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', '{{ csrf_token() }}');

        const response = await fetch(`/agent/schedule/${depotId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        // Vérifier si la réponse est du JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            // Si ce n'est pas du JSON, c'est probablement une redirection
            console.log('Redirection détectée, rechargement de la page...');
            location.reload();
            return;
        }

        const data = await response.json();

        // Fermer l'indicateur de chargement
        Swal.close();
        isDeleting = false;

        if (data.success) {
            Swal.fire({
                title: 'Supprimé !',
                text: data.message || 'Le dépôt a été supprimé avec succès.',
                icon: 'success',
                confirmButtonColor: '#0d8644',
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                // Recharger la page
                location.reload();
            });
        } else {
            throw new Error(data.error || 'Erreur inconnue lors de la suppression');
        }

    } catch (error) {
        // Réinitialiser le flag en cas d'erreur
        isDeleting = false;
        Swal.close();
        
        console.error('Erreur détaillée:', error);
        
        let errorMessage = 'Une erreur est survenue lors de la suppression';
        
        if (error.message.includes('JSON') || error.message.includes('fetch')) {
            // Si erreur JSON ou fetch, c'est probablement une redirection
            console.log('Redirection après suppression, rechargement...');
            location.reload();
            return;
        } else {
            errorMessage = error.message;
        }

        Swal.fire({
            title: 'Erreur !',
            html: `
                <div class="text-start">
                    <p>${errorMessage}</p>
                    <small class="text-muted">
                        Si le problème persiste, contactez l'agentistrateur.
                    </small>
                </div>
            `,
            icon: 'error',
            confirmButtonColor: '#fea219'
        });
    }
}

// Autres actions
function editDepot(depotId) {
    Swal.fire({
        title: 'Modification',
        text: 'Redirection vers la page de modification...',
        icon: 'info',
        showConfirmButton: false,
        timer: 1000,
        timerProgressBar: true
    });
    
    setTimeout(() => {
        window.location.href = `/agent/schedule/${depotId}/edit`;
    }, 1100);
}

// Fonctions pour les modals custom
function openDetailsModal() {
    document.getElementById('detailsModal').style.display = 'block';
}

function closeDetailsModal() {
    document.getElementById('detailsModal').style.display = 'none';
}

function openEtiquettesModal() {
    document.getElementById('etiquettesModal').style.display = 'block';
}

function closeEtiquettesModal() {
    document.getElementById('etiquettesModal').style.display = 'none';
}

// Fermer les modals en cliquant à l'extérieur
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.custom-modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
});

// Exposer les fonctions globalement
window.showDetails = showDetails;
window.showEtiquettes = showEtiquettes;
window.downloadEtiquettes = downloadEtiquettes;
window.editDepot = editDepot;
window.confirmDelete = confirmDelete;
window.resetFilters = resetFilters;
window.openDetailsModal = openDetailsModal;
window.closeDetailsModal = closeDetailsModal;
window.openEtiquettesModal = openEtiquettesModal;
window.closeEtiquettesModal = closeEtiquettesModal;
window.downloadCurrentEtiquettes = downloadCurrentEtiquettes;

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