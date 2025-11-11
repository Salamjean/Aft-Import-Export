@extends('chauffeur.layouts.template')
@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <div class="page-header bg-gradient-primary rounded-3 p-4 shadow">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="text-white mb-2">📋 Mes Historiques</h1>
                        <p class="text-white-50 mb-0">Consultez l'historique vos programmes de dépôt et récupération</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="badge bg-light text-primary fs-6 p-2">
                            <i class="fas fa-tasks me-2"></i>
                            {{ count($programmes) }} programme(s)
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
                            <label class="form-label fw-semibold">Type</label>
                            <select class="modern-select" id="filter-type">
                                <option value="">Tous les types</option>
                                <option value="depot">Dépôt</option>
                                <option value="recuperation">Récupération</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Statut</label>
                            <select class="modern-select" id="filter-statut">
                                <option value="">Tous les statuts</option>
                                <option value="programme">Programmé</option>
                                <option value="en_cours">En cours</option>
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
                        <table class="table table-hover modern-table" id="programmes-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="text-align: center" class="ps-4">Type & Référence</th>
                                    <th style="text-align: center">Nature Objet</th>
                                    <th style="text-align: center">Quantité</th>
                                    <th style="text-align: center">Client/Destinataire</th>
                                    <th style="text-align: center">Contact</th>
                                    <th style="text-align: center">Date Programme</th>
                                    <th style="text-align: center">Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($programmes as $programme)
                                    <tr class="programme-row" 
                                        data-type="{{ $programme->type }}" 
                                        data-statut="{{ $programme->statut }}" 
                                        data-date="{{ $programme->date_programme ? \Carbon\Carbon::parse($programme->date_programme)->format('Y-m-d') : '' }}"
                                        data-search="{{ strtolower($programme->reference . ' ' . $programme->nom_concerne . ' ' . $programme->prenom_concerne . ' ' . $programme->nature_objet) }}">
                                        <td style="display: flex; justify-content:center" class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="reference-badge {{ $programme->type === 'depot' ? 'bg-primary' : 'bg-warning' }} text-white rounded-circle me-3">
                                                    @if($programme->type === 'depot')
                                                        <i class="fas fa-inbox"></i>
                                                    @else
                                                        <i class="fas fa-truck-pickup"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <strong class="d-block">{{ $programme->reference }}</strong>
                                                    <small class="text-muted text-uppercase">
                                                        {{ $programme->type === 'depot' ? 'Dépôt' : 'Récupération' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="text-align: center">
                                            <span class="badge bg-light text-dark border">{{ $programme->nature_objet }}</span>
                                        </td>
                                        <td style="text-align: center">
                                            <span class="quantity-badge">{{ $programme->quantite }}</span>
                                        </td>
                                        <td style="text-align: center">
                                            <strong>{{ $programme->nom_concerne }} {{ $programme->prenom_concerne }}</strong>
                                            <br><small class="text-muted">{{ Str::limit($programme->adresse, 30) }}</small>
                                        </td>
                                        <td style="text-align: center">
                                            <div>
                                                <i class="fas fa-phone text-success me-1"></i>
                                                {{ $programme->contact }}
                                            </div>
                                            @if($programme->email)
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    {{ Str::limit($programme->email, 20) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td style="text-align: center">
                                            @if($programme->date_programme)
                                                <div class="date-cell">
                                                    <i class="fas fa-calendar me-1 text-primary"></i>
                                                    {{ \Carbon\Carbon::parse($programme->date_programme)->format('d/m/Y') }}
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center">
                                            <span class="status-badge status-{{ $programme->statut }}">
                                                {{ $programme->statut }}
                                            </span>
                                        </td>
                                        <td style="text-align: center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-sm btn-outline-primary btn-action" 
                                                        onclick="showDetails('{{ $programme->type }}', {{ $programme->id }})" 
                                                        title="Détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @if($programme->type === 'recuperation')
                                                <button class="btn btn-sm btn-outline-success btn-action" 
                                                        onclick="downloadEtiquettes('{{ $programme->type }}', {{ $programme->id }})" 
                                                        title="Télécharger étiquettes">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">Aucun programme trouvé</h5>
                                                <p class="text-muted">Vous n'avez aucun programme de dépôt ou récupération assigné</p>
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

<!-- Inclure SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
:root {
    --primary-color: #3b933f;
    --secondary-color: #3b933f;
    --warning-color: #FF9800;
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
    background: linear-gradient(135deg, var(--warning-color), #F57C00) !important;
}

.stat-card.bg-success {
    background: linear-gradient(135deg, #4CAF50, #388E3C) !important;
}

.stat-card.bg-info {
    background: linear-gradient(135deg, #3b933f, #3b933f) !important;
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

.status-programme { background: #e3f2fd; color: #3b933f; }
.status-en_cours { background: #fff3e0; color: #f57c00; }
.status-termine { background: #e8f5e8; color: #4CAF50; }

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
    console.log('✅ Système programmes chauffeur initialisé');
    initializeFilters();
});

// Filtres
function initializeFilters() {
    const filters = ['filter-type', 'filter-statut', 'filter-date', 'filter-search'];
    filters.forEach(filterId => {
        const element = document.getElementById(filterId);
        if (element) {
            element.addEventListener('change', applyFilters);
            element.addEventListener('input', applyFilters);
        }
    });
}

function applyFilters() {
    const type = document.getElementById('filter-type').value;
    const statut = document.getElementById('filter-statut').value;
    const date = document.getElementById('filter-date').value;
    const search = document.getElementById('filter-search').value.toLowerCase();
    
    const rows = document.querySelectorAll('.programme-row');
    
    rows.forEach(row => {
        let showRow = true;
        
        if (type && row.dataset.type !== type) {
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
    document.getElementById('filter-type').value = '';
    document.getElementById('filter-statut').value = '';
    document.getElementById('filter-date').value = '';
    document.getElementById('filter-search').value = '';
    applyFilters();
}

// Détails du programme
async function showDetails(type, id) {
    Swal.fire({
        title: 'Chargement...',
        text: 'Récupération des détails du programme',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const response = await fetch(`/driver/planing/${type}/${id}/details`);
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

function displayDetailsModal(programmeData) {
    const typeLabel = programmeData.type === 'depot' ? 'Dépôt' : 'Récupération';
    const icon = programmeData.type === 'depot' ? 'fa-inbox' : 'fa-truck-pickup';
    const color = programmeData.type === 'depot' ? '#2196F3' : '#FF9800';
    
    Swal.fire({
        title: `Détails - ${programmeData.reference}`,
        html: `
            <div class="text-start">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-3" style="color: ${color}">
                            <i class="fas ${icon} me-2"></i>Informations du ${typeLabel}
                        </h6>
                        <div class="mb-2"><strong>Référence:</strong> ${programmeData.reference}</div>
                        <div class="mb-2"><strong>Type:</strong> <span class="badge" style="background: ${color}; color: white">${typeLabel}</span></div>
                        <div class="mb-2"><strong>Nature:</strong> ${programmeData.nature_objet}</div>
                        <div class="mb-2"><strong>Quantité:</strong> <span class="badge bg-primary">${programmeData.quantite}</span></div>
                        <div class="mb-2"><strong>Statut:</strong> <span class="status-badge status-${programmeData.statut}">${programmeData.statut}</span></div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3" style="color: ${color}">
                            <i class="fas fa-user me-2"></i>${programmeData.type === 'depot' ? 'Destinataire' : 'Client'}
                        </h6>
                        <div class="mb-2"><strong>Nom:</strong> ${programmeData.nom_concerne} ${programmeData.prenom_concerne}</div>
                        <div class="mb-2"><strong>Contact:</strong> ${programmeData.contact}</div>
                        <div class="mb-2"><strong>Email:</strong> ${programmeData.email || 'Non renseigné'}</div>
                        <div class="mb-2"><strong>Adresse:</strong> ${programmeData.adresse}</div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="mb-3" style="color: ${color}">
                            <i class="fas fa-calendar me-2"></i>Planning
                        </h6>
                        <div class="mb-2"><strong>Date programme:</strong> ${programmeData.date_programme}</div>
                        <div class="mb-2"><strong>Créé le:</strong> ${programmeData.created_at}</div>
                    </div>
                </div>
            </div>
        `,
        width: 800,
        confirmButtonColor: color,
        confirmButtonText: 'Fermer'
    });
}

// Téléchargement des étiquettes
async function downloadEtiquettes(type, id) {
    const typeLabel = type === 'depot' ? 'Dépôt' : 'Récupération';
    
    if (type === 'recuperation') {
        const result = await Swal.fire({
            title: 'Confirmation',
            html: `
                <div class="text-start">
                    <p>Vous allez télécharger les étiquettes pour cette récupération.</p>
                    <div class="alert alert-info mt-2">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note :</strong> Le statut passera automatiquement à "terminé" après le téléchargement.
                    </div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#FF9800',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-download me-2"></i>Télécharger',
            cancelButtonText: 'Annuler'
        });
        
        if (!result.isConfirmed) {
            return;
        }
    }
    
    Swal.fire({
        title: 'Préparation...',
        text: 'Génération du fichier PDF en cours',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        // Créer un lien temporaire pour le téléchargement
        const downloadUrl = `/driver/planing/${type}/${id}/download-etiquettes`;
        window.open(downloadUrl, '_blank');
        
        Swal.close();
        
        if (type === 'recuperation') {
            // Mettre à jour l'interface après un court délai
            setTimeout(() => {
                Swal.fire({
                    title: 'Téléchargement terminé',
                    text: 'Les étiquettes ont été téléchargées et le statut est passé à "terminé"',
                    icon: 'success',
                    confirmButtonColor: '#FF9800',
                    timer: 3000,
                    timerProgressBar: true
                }).then(() => {
                    location.reload();
                });
            }, 2000);
        } else {
            Swal.fire({
                title: 'Téléchargement démarré',
                text: 'Les étiquettes sont en cours de téléchargement',
                icon: 'success',
                confirmButtonColor: '#2196F3',
                timer: 2000,
                timerProgressBar: true
            });
        }
        
    } catch (error) {
        Swal.fire({
            title: 'Erreur',
            text: 'Erreur lors du téléchargement: ' + error.message,
            icon: 'error',
            confirmButtonColor: '#2196F3'
        });
    }
}

// Exposer les fonctions globalement
window.showDetails = showDetails;
window.downloadEtiquettes = downloadEtiquettes;
window.resetFilters = resetFilters;
</script>
@endsection