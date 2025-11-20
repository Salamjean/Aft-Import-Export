@extends('agent.layouts.template')
@section('content')
<div class="container-fluid">
    <!-- Header avec statistiques -->
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <div class="page-header bg-gradient-primary rounded-3 p-4 shadow" style="background: linear-gradient(135deg, #3a913e, #3a913e) !important;">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="text-white mb-2">🚚 Programmes de Récupération</h1>
                        <p class="text-white-50 mb-0">Gérez et suivez tous vos programmes de récupération</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('agent.recuperation.create') }}" class="btn btn-light btn-lg rounded-pill px-4">
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
                            <h4 class="card-title">{{ $totalRecuperations ?? '0' }}</h4>
                            <p class="card-text">Total Récupérations</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-truck-pickup fa-2x"></i>
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
                            <i class="fas fa-truck-loading fa-2x"></i>
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
                            <select class="modern-select" id="filter-chauffeur">
                                <option value="">Tous les chauffeurs</option>
                                @foreach($chauffeurs as $chauffeur)
                                    <option value="{{ $chauffeur->id }}">{{ $chauffeur->nom }} {{ $chauffeur->prenom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Statut</label>
                            <select class="modern-select" id="filter-statut">
                                <option value="">Tous les statuts</option>
                                <option value="programme">Programmé</option>
                                <option value="en_cours">En cours</option>
                                <option value="termine">Terminé</option>
                                <option value="annule">Annulé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" class="modern-input" id="filter-date">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Recherche</label>
                            <input type="text" class="modern-input" id="filter-search" placeholder="Référence, nom...">
                        </div>
                    </div>

                    <!-- Tableau -->
                    <div class="table-responsive">
                        <table class="table table-hover modern-table" id="recuperations-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="text-align: center" class="ps-4">Référence</th>
                                    <th style="text-align: center">Chauffeur</th>
                                    <th style="text-align: center">Nature Objet</th>
                                    <th style="text-align: center">Quantité</th>
                                    <th style="text-align: center">Client</th>
                                    <th style="text-align: center">Contact</th>
                                    <th style="text-align: center">Date Programme</th>
                                    <th style="text-align: center">Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recuperations as $recuperation)
                                    <tr class="recuperation-row" 
                                        data-chauffeur="{{ $recuperation->chauffeur_id }}" 
                                        data-statut="{{ $recuperation->statut }}" 
                                        data-date="{{ $recuperation->date_recuperation ? \Carbon\Carbon::parse($recuperation->date_recuperation)->format('Y-m-d') : '' }}"
                                        data-search="{{ strtolower($recuperation->reference . ' ' . $recuperation->nom_concerne . ' ' . $recuperation->prenom_concerne . ' ' . $recuperation->nature_objet) }}">
                                        <td style="display: flex; justify-content:center" class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="reference-badge bg-primary text-white rounded-circle me-3">
                                                    <i class="fas fa-truck-pickup"></i>
                                                </div>
                                                <div>
                                                    <strong class="d-block">{{ $recuperation->reference }}</strong>
                                                    @if($recuperation->quantite > 1)
                                                        <small class="text-muted">{{ $recuperation->quantite }} codes</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td style="text-align: center">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                                <span>{{ $recuperation->chauffeur->nom }} {{ $recuperation->chauffeur->prenom }}</span>
                                            </div>
                                        </td>
                                        <td style="text-align: center">
                                            <span class="badge bg-light text-dark border">{{ $recuperation->nature_objet }}</span>
                                        </td>
                                        <td style="text-align: center">
                                            <span class="quantity-badge">{{ $recuperation->quantite }}</span>
                                        </td>
                                        <td style="text-align: center">
                                            <strong>{{ $recuperation->nom_concerne }} {{ $recuperation->prenom_concerne }}</strong>
                                            <br><small class="text-muted">{{ Str::limit($recuperation->adresse_recuperation, 30) }}</small>
                                        </td>
                                        <td style="text-align: center">
                                            <div>
                                                <i class="fas fa-phone text-success me-1"></i>
                                                {{ $recuperation->contact }}
                                            </div>
                                            @if($recuperation->email)
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    {{ Str::limit($recuperation->email, 20) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td style="text-align: center">
                                            @if($recuperation->date_recuperation)
                                                <div class="date-cell">
                                                    <i class="fas fa-calendar me-1 text-primary"></i>
                                                    {{ \Carbon\Carbon::parse($recuperation->date_recuperation)->format('d/m/Y') }}
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center">
                                            <span class="status-badge status-{{ $recuperation->statut }}">
                                                {{ $recuperation->statut }}
                                            </span>
                                        </td>
                                        <td style="text-align: center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-sm btn-outline-primary btn-action" 
                                                        onclick="showDetails({{ $recuperation->id }})" 
                                                        title="Détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @if ($recuperation->statut !== 'termine')
                                                    <a href="{{ route('agent.recuperation.edit', $recuperation->id) }}" 
                                                        class="btn btn-sm btn-outline-warning btn-action" 
                                                        title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-danger btn-action" 
                                                            onclick="confirmDelete({{ $recuperation->id }})" 
                                                            title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-truck-pickup fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">Aucun programme de récupération trouvé</h5>
                                                <p class="text-muted">Commencez par créer votre premier programme de récupération</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($recuperations->hasPages())
                        <div class="pagination-container">
                            {{ $recuperations->links('pagination.modern') }}
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
                <i class="fas fa-tags me-2"></i>Étiquettes de la Récupération
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
    --primary-color: #2196F3;
    --secondary-color: #1976D2;
    --accent-color: #0d8644;
}

/* En-tête */
.page-header {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
}

/* Cartes de statistiques */
.stat-card {
    border: none;
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.shadow-hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.stat-card.bg-warning {
    background: linear-gradient(135deg, #FF9800, #F57C00) !important;
}

.stat-card.bg-success {
    background: linear-gradient(135deg, #4CAF50, #388E3C) !important;
}

.stat-card.bg-info {
    background: linear-gradient(135deg, #00BCD4, #0097A7) !important;
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

.status-programme { background: #e3f2fd; color: #1976d2; }
.status-en_cours { background: #fff3e0; color: #f57c00; }
.status-termine { background: #e8f5e8; color: var(--secondary-color); }
.status-annule { background: #ffebee; color: #c62828; }

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
    box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
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
    .page-header {
        text-align: center;
    }
    
    .stat-card .card-body {
        padding: 1rem;
    }
    
    .btn-action {
        padding: 0.25rem 0.5rem;
    }
    
    .custom-modal-content {
        margin: 10% auto;
        width: 95%;
    }
    
    .custom-modal-body {
        padding: 1rem;
        max-height: 60vh;
    }
}
</style>

<script>
// Variables globales
let currentRecuperationId = null;
let isDeleting = false;

// Initialisation quand le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Système de récupérations initialisé avec SweetAlert2');
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
    
    const rows = document.querySelectorAll('.recuperation-row');
    
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

// Détails de la récupération avec SweetAlert2
async function showDetails(recuperationId) {
    console.log('Chargement des détails pour:', recuperationId);
    
    Swal.fire({
        title: 'Chargement...',
        text: 'Récupération des détails de la récupération',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const response = await fetch(`/agent/schedule/recuperation/${recuperationId}/details`);
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
            confirmButtonColor: '#2196F3'
        });
    }
}

function displayDetailsModal(recuperationData) {
    let codesHtml = '';
    if (recuperationData.code_nature) {
        const codes = recuperationData.code_nature.split(',').map(code => 
            `<span class="badge bg-light text-dark border me-2 mb-2">${code}</span>`
        ).join('');
        
        codesHtml = `
            <div class="row mt-4">
            </div>
        `;
    }
    
    // Section destinataire (si les informations existent)
    let destinataireHtml = '';
    if (recuperationData.nom_destinataire && recuperationData.contact_destinataire) {
        destinataireHtml = `
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="text-success mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>Informations du Destinataire
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2"><strong>Nom:</strong> ${recuperationData.nom_destinataire} ${recuperationData.prenom_destinataire}</div>
                            <div class="mb-2"><strong>Contact:</strong> ${recuperationData.indicatif_destinataire} ${recuperationData.contact_destinataire}</div>
                            ${recuperationData.email_destinataire ? `<div class="mb-2"><strong>Email:</strong> ${recuperationData.email_destinataire}</div>` : ''}
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2"><strong>Adresse:</strong> ${recuperationData.adresse_destinataire}</div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    // SECTION LIVRAISON - Affichage conditionnel
    let livraisonHtml = '';
    if (recuperationData.type_livraison && recuperationData.lieu_livraison) {
        const typeLivraisonLabel = recuperationData.type_livraison === 'livraison' ? 'Livraison' : 'Enlèvement';
        const typeLivraisonIcon = recuperationData.type_livraison === 'livraison' ? 'fa-truck' : 'fa-box';
        const typeLivraisonColor = recuperationData.type_livraison === 'livraison' ? '#28a745' : '#ffc107';
        
        livraisonHtml = `
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="mb-3" style="color: ${typeLivraisonColor}">
                        <i class="fas ${typeLivraisonIcon} me-2"></i>Informations de ${typeLivraisonLabel}
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong>Type:</strong> 
                                <span class="badge" style="background: ${typeLivraisonColor}; color: white">
                                    ${typeLivraisonLabel}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong>Lieu:</strong> ${recuperationData.lieu_livraison}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    const statutClass = `status-${recuperationData.statut}`;
    
    Swal.fire({
        title: `Détails - ${recuperationData.reference}`,
        html: `
            <div class="text-start">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-truck-pickup me-2"></i>Informations de la Récupération
                        </h6>
                        <div class="mb-2"><strong>Référence:</strong> ${recuperationData.reference}</div>
                        <div class="mb-2"><strong>Nature:</strong> ${recuperationData.nature_objet}</div>
                        <div class="mb-2"><strong>Quantité:</strong> <span class="quantity-badge">${recuperationData.quantite}</span></div>
                        <div class="mb-2"><strong>Statut:</strong> <span class="status-badge ${statutClass}">${recuperationData.statut}</span></div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-user me-2"></i>Client
                        </h6>
                        <div class="mb-2"><strong>Nom:</strong> ${recuperationData.nom_concerne} ${recuperationData.prenom_concerne}</div>
                        <div class="mb-2"><strong>Contact:</strong> ${recuperationData.contact}</div>
                        <div class="mb-2"><strong>Email:</strong> ${recuperationData.email || 'Non renseigné'}</div>
                        <div class="mb-2"><strong>Adresse récupération:</strong> ${recuperationData.adresse_recuperation}</div>
                    </div>
                </div>
                ${destinataireHtml}
                ${livraisonHtml}
                ${codesHtml}
                <div class="mt-3 text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Créé le ${recuperationData.created_at} | Chauffeur: ${recuperationData.chauffeur}
                </div>
            </div>
        `,
        width: 900,
        confirmButtonColor: '#2196F3',
        confirmButtonText: 'Fermer'
    });
}

// Gestion des étiquettes
function showEtiquettesOptions(recuperationId) {
    Swal.fire({
        title: 'Options Étiquettes',
        text: 'Que souhaitez-vous faire avec les étiquettes ?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-eye me-2"></i>Voir les étiquettes',
        cancelButtonText: '<i class="fas fa-download me-2"></i>Télécharger PDF',
        showDenyButton: true,
        denyButtonText: 'Annuler',
        confirmButtonColor: '#4CAF50',
        cancelButtonColor: '#2196F3',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            showEtiquettes(recuperationId);
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            downloadEtiquettes(recuperationId);
        }
    });
}

function showEtiquettes(recuperationId) {
    currentRecuperationId = recuperationId;
    
    Swal.fire({
        title: 'Ouverture des étiquettes',
        text: 'Les étiquettes vont s\'ouvrir dans un nouvel onglet...',
        icon: 'info',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true
    });
    
    setTimeout(() => {
        window.open(`/agent/recuperation/${recuperationId}/etiquettes`, '_blank');
        
        Swal.fire({
            title: 'Étiquettes ouvertes',
            text: 'Les étiquettes ont été ouvertes dans un nouvel onglet.',
            icon: 'success',
            confirmButtonColor: '#4CAF50',
            confirmButtonText: 'OK'
        });
    }, 1600);
}

function downloadEtiquettes(recuperationId) {
    Swal.fire({
        title: 'Téléchargement',
        text: 'Préparation du fichier PDF...',
        icon: 'info',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true
    });
    
    setTimeout(() => {
        window.open(`/agent/recuperation/${recuperationId}/download-etiquettes`, '_blank');
        
        Swal.fire({
            title: 'Téléchargement démarré',
            text: 'Le fichier PDF devrait commencer à télécharger',
            icon: 'success',
            confirmButtonColor: '#4CAF50',
            confirmButtonText: 'OK'
        });
    }, 1600);
}

function downloadCurrentEtiquettes() {
    if (currentRecuperationId) {
        downloadEtiquettes(currentRecuperationId);
    }
}

// Suppression
function confirmDelete(recuperationId) {
    if (isDeleting) {
        return;
    }

    Swal.fire({
        title: 'Êtes-vous sûr ?',
        html: `
            <div class="text-start">
                <p>Cette action supprimera définitivement ce programme de récupération !</p>
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
            deleteRecuperation(recuperationId);
        }
    });
}

async function deleteRecuperation(recuperationId) {
    try {
        Swal.fire({
            title: 'Suppression en cours...',
            text: 'Veuillez patienter',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', '{{ csrf_token() }}');

        const response = await fetch(`/agent/schedule/recuperation/${recuperationId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            location.reload();
            return;
        }

        const data = await response.json();

        Swal.close();
        isDeleting = false;

        if (data.success) {
            Swal.fire({
                title: 'Supprimé !',
                text: data.message || 'La récupération a été supprimée avec succès.',
                icon: 'success',
                confirmButtonColor: '#4CAF50',
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(data.error || 'Erreur inconnue lors de la suppression');
        }

    } catch (error) {
        isDeleting = false;
        Swal.close();
        
        console.error('Erreur détaillée:', error);
        
        let errorMessage = 'Une erreur est survenue lors de la suppression';
        
        if (error.message.includes('JSON') || error.message.includes('fetch')) {
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
            confirmButtonColor: '#2196F3'
        });
    }
}

// Autres actions
function editRecuperation(recuperationId) {
    Swal.fire({
        title: 'Modification',
        text: 'Redirection vers la page de modification...',
        icon: 'info',
        showConfirmButton: false,
        timer: 1000,
        timerProgressBar: true
    });
    
    setTimeout(() => {
        window.location.href = `/agent/recuperation/${recuperationId}/edit`;
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
window.editRecuperation = editRecuperation;
window.confirmDelete = confirmDelete;
window.resetFilters = resetFilters;
window.openDetailsModal = openDetailsModal;
window.closeDetailsModal = closeDetailsModal;
window.openEtiquettesModal = openEtiquettesModal;
window.closeEtiquettesModal = closeEtiquettesModal;
window.downloadCurrentEtiquettes = downloadCurrentEtiquettes;

// Afficher les messages flash Laravel avec SweetAlert2
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            title: 'Succès !',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#2196F3',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Erreur !',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#2196F3'
        });
    @endif
});
</script>
@endsection