@extends('admin.layouts.template')
@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <div class="page-header bg-gradient-primary rounded-3 p-4 shadow" style="background: linear-gradient(135deg, #fea219, #e8910c) !important;">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="text-white mb-2">📋 Demandes de Récupération</h1>
                        <p class="text-white-50 mb-0">Gérez et suivez toutes les demandes de récupération</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="badge bg-light text-orange fs-6 p-2">
                            <i class="fas fa-tasks me-2"></i>
                            {{ $demandes->total() }} demande(s)
                        </div>
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
                            <h4 class="card-title">{{ $totalDemandes ?? '0' }}</h4>
                            <p class="card-text">Total Demandes</p>
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
                            <h4 class="card-title">{{ $enAttenteCount ?? '0' }}</h4>
                            <p class="card-text">En Attente</p>
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
                            <h4 class="card-title">{{ $traiteCount ?? '0' }}</h4>
                            <p class="card-text">Traitées</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h4 class="card-title">{{ $annuleCount ?? '0' }}</h4>
                            <p class="card-text">Annulées</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-times-circle fa-2x"></i>
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
                            <h5 class="card-title mb-0 text-orange">
                                <i class="fas fa-list me-2"></i>Liste des Demandes
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
                            <select class="modern-select" id="filter-agence">
                                <option value="">Toutes les agences</option>
                                @foreach($agences as $agence)
                                    <option value="{{ $agence->id }}">{{ $agence->name }} - {{ $agence->pays }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="modern-select" id="filter-statut">
                                <option value="">Tous les statuts</option>
                                <option value="en_attente">En attente</option>
                                <option value="traite">Traité</option>
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
                        <table class="table table-hover modern-table" id="demandes-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="text-align: center" class="ps-4">Référence</th>
                                    <th style="text-align: center">Agence</th>
                                    <th style="text-align: center">Nature Objet</th>
                                    <th style="text-align: center">Quantité</th>
                                    <th style="text-align: center">Client</th>
                                    <th style="text-align: center">Contact</th>
                                    <th style="text-align: center">Date Demande</th>
                                    <th style="text-align: center">Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($demandes as $demande)
                                    <tr class="demande-row" 
                                        data-agence="{{ $demande->agence_id }}" 
                                        data-statut="{{ $demande->statut }}" 
                                        data-date="{{ $demande->created_at->format('Y-m-d') }}"
                                        data-search="{{ strtolower($demande->reference . ' ' . $demande->nom_concerne . ' ' . $demande->prenom_concerne . ' ' . $demande->nature_objet) }}">
                                        <td style="display: flex; justify-content:center" class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="reference-badge bg-orange text-white rounded-circle me-3">
                                                    <i class="fas fa-truck-pickup"></i>
                                                </div>
                                                <div>
                                                    <strong class="d-block">{{ $demande->reference }}</strong>
                                                    <small class="text-muted">{{ $demande->created_at->format('d/m/Y') }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="text-align: center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-building text-orange"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $demande->agence->name }}</strong>
                                                    <br><small class="text-muted">{{ $demande->agence->pays }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="text-align: center">
                                            <span class="badge bg-light text-dark border">{{ $demande->nature_objet }}</span>
                                        </td>
                                        <td style="text-align: center">
                                            <span class="quantity-badge">{{ $demande->quantite }}</span>
                                        </td>
                                        <td style="text-align: center">
                                            <strong>{{ $demande->nom_concerne }} {{ $demande->prenom_concerne }}</strong>
                                            <br><small class="text-muted">{{ Str::limit($demande->adresse_recuperation, 30) }}</small>
                                        </td>
                                        <td style="text-align: center">
                                            <div>
                                                <i class="fas fa-phone text-success me-1"></i>
                                                {{ $demande->contact }}
                                            </div>
                                            @if($demande->email)
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    {{ Str::limit($demande->email, 20) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td style="text-align: center">
                                            <div class="date-cell">
                                                <i class="fas fa-calendar me-1 text-orange"></i>
                                                {{ $demande->created_at->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td style="text-align: center">
                                            <span class="status-badge status-{{ $demande->statut }}">
                                                {{ $demande->statut === 'en_attente' ? 'En Attente' : 
                                                   ($demande->statut === 'traite' ? 'Traité' : 'Annulé') }}
                                            </span>
                                        </td>
                                        <td style="text-align: center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <!-- Bouton Détails -->
                                                <button class="btn btn-sm btn-outline-primary btn-action" 
                                                        onclick="showDetails({{ $demande->id }})" 
                                                        title="Détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                <!-- Bouton Marquer comme Terminé (seulement si en attente) -->
                                                @if($demande->statut === 'en_attente')
                                                <button class="btn btn-sm btn-outline-success btn-action" 
                                                        onclick="markAsTraite({{ $demande->id }})" 
                                                        title="Marquer comme traité">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                                @endif

                                                <!-- Bouton Annuler (seulement si en attente) -->
                                                @if($demande->statut === 'en_attente')
                                                <button class="btn btn-sm btn-outline-danger btn-action" 
                                                        onclick="markAsAnnule({{ $demande->id }})" 
                                                        title="Annuler">
                                                    <i class="fas fa-times-circle"></i>
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
                                                <h5 class="text-muted">Aucune demande de récupération trouvée</h5>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($demandes->hasPages())
                        <div class="pagination-container">
                            {{ $demandes->links('pagination.modern') }}
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
    --primary-orange: #fea219;
    --dark-orange: #e8910c;
    --light-orange: #ffb74d;
}

/* En-tête */
.page-header {
    background: linear-gradient(135deg, var(--primary-orange), var(--dark-orange)) !important;
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

.stat-card.bg-primary { background: linear-gradient(135deg, var(--primary-orange), var(--dark-orange)) !important; }
.stat-card.bg-warning { background: linear-gradient(135deg, #FF9800, #F57C00) !important; }
.stat-card.bg-success { background: linear-gradient(135deg, #4CAF50, #388E3C) !important; }
.stat-card.bg-info { background: linear-gradient(135deg, #00BCD4, #0097A7) !important; }

.stat-icon {
    opacity: 0.8;
}

/* Carte principale */
.modern-card {
    border-radius: 15px;
    border: none;
    border-top: 4px solid var(--primary-orange);
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

.bg-orange {
    background-color: var(--primary-orange) !important;
}

.quantity-badge {
    background: var(--primary-orange);
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

.status-en_attente { background: #fff3e0; color: #f57c00; }
.status-traite { background: #e8f5e8; color: #4CAF50; }
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

.btn-outline-primary {
    border-color: var(--primary-orange);
    color: var(--primary-orange);
}

.btn-outline-primary:hover {
    background-color: var(--primary-orange);
    color: white;
}

/* Formulaires */
.modern-select, .modern-input {
    border-radius: 10px;
    border: 1px solid #e0e0e0;
    padding: 0.75rem;
    transition: all 0.3s ease;
}

.modern-select:focus, .modern-input:focus {
    border-color: var(--primary-orange);
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

.text-orange {
    color: var(--primary-orange) !important;
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
// Initialisation quand le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Système demandes récupération initialisé');
    initializeFilters();
});

// Filtres
function initializeFilters() {
    const filters = ['filter-agence', 'filter-statut', 'filter-date', 'filter-search'];
    filters.forEach(filterId => {
        const element = document.getElementById(filterId);
        if (element) {
            element.addEventListener('change', applyFilters);
            element.addEventListener('input', applyFilters);
        }
    });
}

function applyFilters() {
    const agence = document.getElementById('filter-agence').value;
    const statut = document.getElementById('filter-statut').value;
    const date = document.getElementById('filter-date').value;
    const search = document.getElementById('filter-search').value.toLowerCase();
    
    const rows = document.querySelectorAll('.demande-row');
    
    rows.forEach(row => {
        let showRow = true;
        
        if (agence && row.dataset.agence !== agence) {
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
    document.getElementById('filter-agence').value = '';
    document.getElementById('filter-statut').value = '';
    document.getElementById('filter-date').value = '';
    document.getElementById('filter-search').value = '';
    applyFilters();
}

// Détails de la demande
async function showDetails(demandeId) {
    Swal.fire({
        title: 'Chargement...',
        text: 'Récupération des détails de la demande',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        // CORRECTION : Utiliser la route avec le paramètre
        const url = `{{ route('admin.demandes-recuperation.details', ':id') }}`.replace(':id', demandeId);
        const response = await fetch(url);
        
        // Vérifier si la réponse est JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const textResponse = await response.text();
            console.error('Non-JSON response:', textResponse.substring(0, 500));
            throw new Error('Le serveur a retourné une réponse non-JSON. Vérifiez la route.');
        }
        
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

function displayDetailsModal(demandeData) {
    const statutLabels = {
        'en_attente': 'En Attente',
        'traite': 'Traité',
        'annule': 'Annulé'
    };
    
    const statutClass = `status-${demandeData.statut}`;
    const statutLabel = statutLabels[demandeData.statut] || demandeData.statut;
    
    Swal.fire({
        title: `Détails - ${demandeData.reference}`,
        html: `
            <div class="text-start">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-3" style="color: #fea219">
                            <i class="fas fa-truck-pickup me-2"></i>Informations de la Demande
                        </h6>
                        <div class="mb-2"><strong>Référence:</strong> ${demandeData.reference}</div>
                        <div class="mb-2"><strong>Nature:</strong> ${demandeData.nature_objet}</div>
                        <div class="mb-2"><strong>Quantité:</strong> <span class="quantity-badge">${demandeData.quantite}</span></div>
                        <div class="mb-2"><strong>Statut:</strong> <span class="status-badge ${statutClass}">${statutLabel}</span></div>
                        <div class="mb-2"><strong>Date demande:</strong> ${demandeData.created_at}</div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3" style="color: #fea219">
                            <i class="fas fa-building me-2"></i>Agence d'Expédition
                        </h6>
                        <div class="mb-2"><strong>Agence:</strong> ${demandeData.agence.name}</div>
                        <div class="mb-2"><strong>Pays:</strong> ${demandeData.agence.pays}</div>
                        <div class="mb-2"><strong>Adresse:</strong> ${demandeData.agence.adresse}</div>
                        <div class="mb-2"><strong>Devise:</strong> ${demandeData.agence.devise}</div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="mb-3" style="color: #fea219">
                            <i class="fas fa-user me-2"></i>Informations du Client
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2"><strong>Nom:</strong> ${demandeData.nom_concerne} ${demandeData.prenom_concerne}</div>
                                <div class="mb-2"><strong>Contact:</strong> ${demandeData.contact}</div>
                                <div class="mb-2"><strong>Email:</strong> ${demandeData.email || 'Non renseigné'}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2"><strong>Adresse récupération:</strong> ${demandeData.adresse_recuperation}</div>
                                <div class="mb-2"><strong>Date souhaitée:</strong> ${demandeData.date_recuperation || 'Dès que possible'}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `,
        width: 800,
        confirmButtonColor: '#fea219',
        confirmButtonText: 'Fermer'
    });
}

// Marquer comme traité
async function markAsTraite(demandeId) {
    const result = await Swal.fire({
        title: 'Marquer comme traité',
        html: `
            <div class="text-start">
                <p>Êtes-vous sûr de vouloir marquer cette demande comme traitée ?</p>
                <div class="alert alert-success mt-2">
                    <i class="fas fa-info-circle me-2"></i>
                    Cette action changera le statut de la demande à "traité".
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4CAF50',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check-circle me-2"></i>Marquer comme traité',
        cancelButtonText: 'Annuler'
    });
    
    if (!result.isConfirmed) {
        return;
    }
    
    Swal.fire({
        title: 'Traitement...',
        text: 'Mise à jour du statut de la demande',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const response = await fetch(`/admin/request/${demandeId}/traiter`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                title: '✅ Demande traitée !',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#4CAF50',
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Erreur lors de la mise à jour');
        }
    } catch (error) {
        Swal.fire({
            title: '❌ Erreur',
            text: 'Erreur: ' + error.message,
            icon: 'error',
            confirmButtonColor: '#fea219'
        });
    }
}

// Marquer comme annulé
async function markAsAnnule(demandeId) {
    const result = await Swal.fire({
        title: 'Annuler la demande',
        html: `
            <div class="text-start">
                <p>Êtes-vous sûr de vouloir annuler cette demande ?</p>
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cette action est irréversible et changera le statut à "annulé".
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-times-circle me-2"></i>Oui, annuler',
        cancelButtonText: 'Annuler'
    });
    
    if (!result.isConfirmed) {
        return;
    }
    
    Swal.fire({
        title: 'Traitement...',
        text: 'Annulation de la demande',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const response = await fetch(`/admin/request/${demandeId}/annuler`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                title: '✅ Demande annulée !',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#d33',
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Erreur lors de l\'annulation');
        }
    } catch (error) {
        Swal.fire({
            title: '❌ Erreur',
            text: 'Erreur: ' + error.message,
            icon: 'error',
            confirmButtonColor: '#fea219'
        });
    }
}

// Exposer les fonctions globalement
window.showDetails = showDetails;
window.markAsTraite = markAsTraite;
window.markAsAnnule = markAsAnnule;
window.resetFilters = resetFilters;
</script>
@endsection