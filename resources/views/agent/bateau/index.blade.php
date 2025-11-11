@extends('agent.layouts.template')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@php
    $planifications = App\Models\Bateau::orderBy('created_at', 'desc')->get();
@endphp

<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-white rounded-circle p-2 me-3">
                                <i class="fas fa-list-alt fa-lg" style="color: #0d8644"></i>
                            </div>
                            <div>
                                <h3 class="card-title mb-0">Liste des Planifications</h3>
                                <p class="mb-0 opacity-75">Gérez et recherchez vos planifications de transport</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-white text-dark fs-6 px-3 py-2">
                                <i class="fas fa-chart-bar me-2"></i>
                                Total: {{ $planifications->count() }} planifications
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Barre de recherche et filtres -->
                    <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search" style="color: #0d8644"></i>
                            </span>
                            <input type="text" id="searchInput" class="form-control border-start-0 ps-3" placeholder="Rechercher..." style="height: 50px;">
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-truck-loading" style="color: #0d8644"></i>
                            </span>
                            <select class="form-control border-start-0 ps-3" id="typeFilter" style="height: 50px;">
                                <option value="">Tous les types</option>
                                <option value="Bateau">Bateau</option>
                                <option value="Avion">Avion</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-flag" style="color: #0d8644"></i>
                            </span>
                            <select class="form-control border-start-0 ps-3" id="statutFilter" style="height: 50px;">
                                <option value="">Tous les statuts</option>
                                <option value="depart">Départ</option>
                                <option value="en_cours">En cours</option>
                                <option value="arrive">Arrivé</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-calendar-alt" style="color: #0d8644"></i>
                            </span>
                            <input type="date" id="dateFilter" class="form-control border-start-0 ps-3" style="height: 50px;">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-fill" id="resetFilters" style="height: 50px; background: linear-gradient(135deg, #fea219 0%, #fea219 100%); border: none;">
                                <i class="fas fa-redo me-2"></i>Réinitialiser
                            </button>
                        </div>
                    </div>
                </div>

                    <!-- Tableau des planifications -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="planificationsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="border-radius: 10px 0 0 0;" class="text-center">
                                        <i class="fas fa-hashtag me-2" style="color: #0d8644"></i>Référence
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-truck-loading me-2" style="color: #0d8644"></i>Type
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-flag me-2" style="color: #0d8644"></i>Statut
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-box me-2" style="color: #0d8644"></i>Conteneur
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-building me-2" style="color: #0d8644"></i>Compagnie
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-ship me-2" style="color: #0d8644"></i>Nom
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-hashtag me-2" style="color: #0d8644"></i>Numéro
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-calendar-alt me-2" style="color: #0d8644"></i>Date Arrivée
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-map-marker-alt me-2" style="color: #0d8644"></i>Agence
                                    </th>
                                    <th style="border-radius: 0 10px 0 0;" class="text-center">
                                        <i class="fas fa-cogs me-2" style="color: #0d8644"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                            @foreach($planifications as $planification)
                                @php
                                    $conteneurName = $planification->conteneur->name_conteneur ?? 'N/A';
                                    $agenceName = $planification->agence->name ?? 'N/A';
                                    $searchData = strtolower(
                                        $planification->reference . ' ' . 
                                        $planification->compagnie . ' ' . 
                                        $planification->nom . ' ' . 
                                        $planification->numero . ' ' .
                                        $conteneurName . ' ' .
                                        $agenceName
                                    );
                                @endphp
                            <tr data-id="{{ $planification->id }}" 
                                data-type="{{ $planification->type_transport }}" 
                                data-statut="{{ $planification->statut }}"
                                data-date="{{ $planification->date_arrive }}" 
                                data-search="{{ $searchData }}">
                                <td class="text-center">
                                    <span class="fw-bold" style="color: black;">{{ $planification->reference }}</span>
                                </td>
                                <td style="color:white" class="text-center">
                                    <span class="badge badge-status {{ $planification->type_transport === 'Bateau' ? 'bg-warning' : 'bg-info' }}">
                                        <i class="fas {{ $planification->type_transport === 'Bateau' ? 'fa-ship' : 'fa-plane' }} me-1"></i>
                                        {{ $planification->type_transport }}
                                    </span>
                                </td>
                                <td style="color:white" class="text-center">
                                    <span class="badge badge-status 
                                        {{ $planification->statut === 'depart' ? 'bg-secondary' : '' }}
                                        {{ $planification->statut === 'en_cours' ? 'bg-primary' : '' }}
                                        {{ $planification->statut === 'arrive' ? 'bg-success' : '' }}">
                                        <i class="fas 
                                            {{ $planification->statut === 'depart' ? 'fa-flag' : '' }}
                                            {{ $planification->statut === 'en_cours' ? 'fa-spinner fa-spin' : '' }}
                                            {{ $planification->statut === 'arrive' ? 'fa-check' : '' }} me-1">
                                        </i>
                                        {{ $planification->statut === 'depart' ? 'Départ' : '' }}
                                        {{ $planification->statut === 'en_cours' ? 'En cours' : '' }}
                                        {{ $planification->statut === 'arrive' ? 'Arrivé' : '' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($planification->conteneur)
                                        <span class="fw-medium">
                                            <i class="fas fa-box me-1 text-muted"></i>
                                            {{ $planification->conteneur->name_conteneur }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $planification->compagnie }}</td>
                                <td class="text-center">{{ $planification->nom }}</td>
                                <td class="text-center">{{ $planification->numero }}</td>
                                <td class="text-center">
                                    <i class="fas fa-calendar me-2 text-muted"></i>
                                    {{ \Carbon\Carbon::parse($planification->date_arrive)->format('d/m/Y') }}
                                </td>
                                <td class="text-center">
                                    @if($planification->agence)
                                        <span class="fw-medium">
                                            <i class="fas fa-map-marker-alt me-1 text-muted"></i>
                                            {{ $planification->agence->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('agent.bateau.conteneur', $planification->id) }}" class="btn btn-action btn-open me-1" title="Ouvrir le bateau">
                                            <i class="fas fa-door-open"></i>
                                        </a>
                                        <button class="btn btn-action btn-delete" onclick="deleteItem({{ $planification->id }})" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Affichage de <span id="showingFrom">1</span> à <span id="showingTo">{{ $planifications->count() }}</span> sur <span id="totalRecords">{{ $planifications->count() }}</span> résultats
                        </div>
                        <nav>
                            <ul class="pagination justify-content-end" id="pagination">
                                <!-- La pagination sera générée ici -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de détails -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title">Détails de la Planification</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Contenu du modal -->
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary-color: #fea219;
    --secondary-color: #fea219;
}
.btn-open {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    color: white;
}

.card {
    border: none;
    border-radius: 20px;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #fea219 0%, #fea219 100%) !important;
    border-bottom: none;
}

.badge-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

/* Couleurs des statuts */
.bg-secondary { background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important; }
.bg-primary { background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important; }
.bg-success { background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%) !important; }
.bg-warning { background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important; }
.bg-info { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important; }

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #fea219;
    box-shadow: 0 0 0 0.2rem rgba(254, 162, 25, 0.25);
}

.input-group-text {
    border: 2px solid #e9ecef;
    border-right: none;
    border-radius: 10px 0 0 10px;
    background-color: #f8f9fa;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 14px;
    padding: 15px 12px;
    background-color: #f8f9fa;
}

.table td {
    padding: 12px;
    vertical-align: middle;
    border-color: #f1f3f4;
}

.table-hover tbody tr:hover {
    background-color: rgba(254, 162, 25, 0.05);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.badge-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.btn-action {
    padding: 6px 12px;
    border-radius: 8px;
    border: none;
    transition: all 0.3s ease;
}

.btn-action:hover {
    transform: translateY(-2px);
}

.btn-view {
    background: linear-gradient(135deg, #fea219 0%, #fea219 100%);
    color: white;
}

.btn-edit {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
}

.btn-delete {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
}

.pagination .page-link {
    border: none;
    color: #6c757d;
    border-radius: 8px;
    margin: 0 3px;
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #fea219 0%, #fea219 100%);
    border: none;
}

.pagination .page-link:hover {
    background-color: #f8f9fa;
}

.shadow-lg {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
}

.hidden-row {
    display: none;
}

/* Animation pour le chargement */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.loading-row {
    animation: pulse 1.5s ease-in-out infinite;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    const itemsPerPage = 10;
    let allRows = [];
    let filteredRows = [];

    // Éléments DOM
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const statutFilter = document.getElementById('statutFilter');
    const dateFilter = document.getElementById('dateFilter');
    const resetFilters = document.getElementById('resetFilters');
    const tableBody = document.getElementById('tableBody');
    const pagination = document.getElementById('pagination');
    const totalRecords = document.getElementById('totalRecords');
    const showingFrom = document.getElementById('showingFrom');
    const showingTo = document.getElementById('showingTo');

    // Initialiser les données
    initializeData();

    // Événements de recherche/filtre
    searchInput.addEventListener('input', debounce(filterData, 300));
    typeFilter.addEventListener('change', filterData);
    statutFilter.addEventListener('change', filterData);
    dateFilter.addEventListener('change', filterData);
    resetFilters.addEventListener('click', resetAllFilters);

    function initializeData() {
        // Récupérer toutes les lignes du tableau
        allRows = Array.from(tableBody.querySelectorAll('tr'));
        filteredRows = [...allRows];
        
        // Afficher toutes les lignes initialement
        showAllRows();
        renderPagination();
    }

    function filterData() {
        const searchTerm = searchInput.value.toLowerCase();
        const typeValue = typeFilter.value;
        const statutValue = statutFilter.value;
        const dateValue = dateFilter.value;

        filteredRows = allRows.filter(row => {
            const matchesSearch = !searchTerm || 
                row.getAttribute('data-search').includes(searchTerm);

            const matchesType = !typeValue || 
                row.getAttribute('data-type') === typeValue;

            const matchesStatut = !statutValue || 
                row.getAttribute('data-statut') === statutValue;

            const matchesDate = !dateValue || 
                row.getAttribute('data-date') === dateValue;

            return matchesSearch && matchesType && matchesStatut && matchesDate;
        });

        currentPage = 1;
        updateDisplay();
        renderPagination();
    }

    function resetAllFilters() {
        searchInput.value = '';
        typeFilter.value = '';
        statutFilter.value = '';
        dateFilter.value = '';
        filterData();
    }

    function updateDisplay() {
        // Cacher toutes les lignes
        allRows.forEach(row => row.classList.add('hidden-row'));
        
        // Afficher seulement les lignes filtrées de la page courante
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const pageRows = filteredRows.slice(startIndex, endIndex);
        
        pageRows.forEach(row => row.classList.remove('hidden-row'));
        
        updateShowingInfo();
        updateTotalCount();
    }

    function showAllRows() {
        allRows.forEach(row => row.classList.remove('hidden-row'));
        updateShowingInfo();
    }

    function renderPagination() {
        const totalPages = Math.ceil(filteredRows.length / itemsPerPage);
        
        if (totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }

        let paginationHTML = '';
        
        // Bouton précédent
        paginationHTML += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;

        // Pages
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                paginationHTML += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                    </li>
                `;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                paginationHTML += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Bouton suivant
        paginationHTML += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;

        pagination.innerHTML = paginationHTML;
    }

    function changePage(page) {
        currentPage = page;
        updateDisplay();
        renderPagination();
    }

    function updateTotalCount() {
        totalRecords.textContent = filteredRows.length;
    }

    function updateShowingInfo() {
        const start = (currentPage - 1) * itemsPerPage + 1;
        const end = Math.min(currentPage * itemsPerPage, filteredRows.length);
        showingFrom.textContent = start;
        showingTo.textContent = end;
    }

    // Fonction debounce pour les recherches
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Exposer la fonction changePage globalement
    window.changePage = function(page) {
        currentPage = page;
        updateDisplay();
        renderPagination();
    };
});

// Fonction pour ouvrir le bateau (afficher le conteneur associé)
function openBateau(id) {
    // Récupérer les données de la ligne
    const row = document.querySelector(`tr[data-id="${id}"]`);
    if (!row) {
        alert('Planification non trouvée');
        return;
    }

    // Afficher un message de confirmation
    if (confirm('Voulez-vous ouvrir ce bateau pour voir le conteneur associé ?')) {
        // Rediriger vers la vue détaillée avec l'ID
        window.location.href = `/agent/bateaux/${id}/conteneur`;
    }
}

// Fonction pour afficher les détails avec SweetAlert2
function showDetails(id) {
    // Récupérer la ligne correspondante
    const row = document.querySelector(`tr[data-id="${id}"]`);
    if (!row) {
        Swal.fire('Erreur', 'Planification non trouvée', 'error');
        return;
    }

    // Récupérer les données de la ligne
    const reference = row.cells[0].textContent.trim();
    const type = row.cells[1].querySelector('.badge').textContent.trim();
    const statut = row.cells[2].querySelector('.badge').textContent.trim();
    const conteneur = row.cells[3].textContent.trim();
    const compagnie = row.cells[4].textContent.trim();
    const nom = row.cells[5].textContent.trim();
    const numero = row.cells[6].textContent.trim();
    const dateArrive = row.cells[7].textContent.trim();
    const agence = row.cells[8].textContent.trim();

    // Créer le contenu HTML pour SweetAlert2
    const detailsHTML = `
        <div class="text-start">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">Informations Générales</h6>
                    <table class="table table-bordered table-sm">
                        <tr>
                            <td class="fw-medium" style="width: 40%;">Référence:</td>
                            <td><strong>${reference}</strong></td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Type:</td>
                            <td>${type}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Statut:</td>
                            <td>${statut}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Date d'arrivée:</td>
                            <td>${dateArrive}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">Informations Transport</h6>
                    <table class="table table-bordered table-sm">
                        <tr>
                            <td class="fw-medium" style="width: 40%;">Conteneur:</td>
                            <td>${conteneur}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Compagnie:</td>
                            <td>${compagnie}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Nom:</td>
                            <td>${nom}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Numéro:</td>
                            <td>${numero}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Agence:</td>
                            <td>${agence}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="mt-3 p-3 bg-light rounded">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Dernière mise à jour: ${new Date().toLocaleDateString('fr-FR')}
                </small>
            </div>
        </div>
    `;

    // Afficher avec SweetAlert2
    Swal.fire({
        title: `<i class="fas fa-ship me-2"></i>Détails de la Planification`,
        html: detailsHTML,
        width: 800,
        padding: '2em',
        background: '#fff',
        backdrop: `
            rgba(0,0,0,0.4)
            url("/images/nyan-cat.gif")
            left top
            no-repeat
        `,
        showCloseButton: true,
        showConfirmButton: false,
        customClass: {
            popup: 'rounded-3'
        }
    });
}

// Fonction pour supprimer avec SweetAlert2
function deleteItem(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    const reference = row ? row.cells[0].textContent.trim() : '';

    Swal.fire({
        title: 'Êtes-vous sûr?',
        html: `
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                <p>Vous êtes sur le point de supprimer la planification :</p>
                <p><strong>"${reference}"</strong></p>
                <p class="text-danger">Cette action est irréversible !</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fas fa-trash me-2"></i>Oui, supprimer!',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Annuler',
        reverseButtons: true,
        backdrop: true,
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Afficher un indicateur de chargement
            if (row) {
                row.classList.add('loading-row');
            }

            // Envoyer la requête de suppression
            fetch(`/agent/bateaux/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                    // Supprimer la ligne du tableau
                    if (row) {
                        row.remove();
                        // Mettre à jour les compteurs
                        initializeData();
                    }
                    
                    // Afficher un message de succès
                    Swal.fire({
                        title: 'Supprimé!',
                        html: `
                            <div class="text-center">
                                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                <p>La planification a été supprimée avec succès.</p>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonColor: '#28a745',
                        timer: 2000
                    });
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                Swal.fire({
                    title: 'Erreur!',
                    text: 'Erreur lors de la suppression: ' + error.message,
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
                if (row) {
                    row.classList.remove('loading-row');
                }
            });
        }
    });
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
            showConfirmButton: true
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