@extends('agent.layouts.template')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
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
                            <h3 class="card-title">Gestion des Conteneurs</h3>
                            <p class="card-subtitle">Liste de tous les conteneurs enregistrés</p>
                        </div>
                    </div>
                    <a href="{{ route('agent.conteneur.create') }}" class="btn modern-btn text-white" style="background-color:#0e914b ">
                        <i class="fas fa-plus"></i>
                        Nouveau Conteneur
                    </a>
                </div>
                <div class="card-body">

                    <!-- Filtres et recherche -->
                    <div class="table-controls">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" class="form-control modern-input" id="searchInput" placeholder="Rechercher un conteneur...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="filter-group">
                                    <select class="form-control modern-select" id="statusFilter">
                                        <option value="">Tous les statuts</option>
                                        <option value="ouvert">Ouvert</option>
                                        <option value="fermer">Fermé</option>
                                    </select>
                                    <select class="form-control modern-select" id="typeFilter">
                                        <option value="">Tous les types</option>
                                        <option value="Conteneur">Conteneur</option>
                                        <option value="Ballon">Ballon</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des conteneurs -->
                    <div class="table-responsive">
                        <table class="table modern-table">
                            <thead>
                                <tr>
                                    <th class="text-center">Nom</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Agence d'Expédition</th> <!-- Nouvelle colonne -->
                                    <th class="text-center">Numéro</th>
                                    <th class="text-center">Date de création</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($conteneurs as $conteneur)
                                <tr>
                                     <td class="text-center">
                                        <div class="conteneur-info">
                                            <div class="conteneur-name">{{ $conteneur->name_conteneur }}</div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge type-badge type-{{ strtolower($conteneur->type_conteneur) }}">
                                            {{ $conteneur->type_conteneur }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge status-{{ $conteneur->statut }}">
                                            <i class="fas {{ $conteneur->statut == 'ouvert' ? 'fa-unlock' : 'fa-lock' }}"></i>
                                            {{ ucfirst($conteneur->statut) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($conteneur->colis->count() > 0)
                                            @php
                                                // Récupérer l'agence d'expédition du premier colis du conteneur
                                                $premierColis = $conteneur->colis->first();
                                                $agenceExpedition = $premierColis->agenceExpedition;
                                            @endphp
                                            <span class="agence-name">
                                                {{ $agenceExpedition->name ?? 'Non spécifiée' }}
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $agenceExpedition->pays ?? '' }}</small>
                                        @else
                                            <span class="text-muted">Aucun colis</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($conteneur->numero_conteneur)
                                            <span class="numero">{{ $conteneur->numero_conteneur }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="date">{{ $conteneur->created_at->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('agent.conteneur.edit', $conteneur->id) }}" class="btn btn-action btn-edit" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <button type="button" class="btn btn-action btn-pdf" 
                                                    title="Télécharger PDF"
                                                   onclick="generateConteneurPDF({{ $conteneur->id }}, '{{ $conteneur->name_conteneur }}')">
                                                <i class="fas fa-file-pdf"></i>
                                            </button>
                                            
                                            <form action="{{ route('agent.conteneur.destroy', $conteneur->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-action btn-delete" title="Supprimer" onclick="confirmDelete({{ $conteneur->id }}, '{{ $conteneur->name_conteneur }}', this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <div class="action-buttons">
                                                <a href="{{ route('agent.conteneur.colis.show', $conteneur->id) }}" class="btn btn-action btn-view" title="Ouvrir le conteneur">
                                                    <i class="fas fa-list"></i>
                                                </a>
                                            </div>

                                             @if($conteneur->statut == 'ouvert')
                                            <button type="button" class="btn btn-action btn-close" title="Fermer le conteneur" onclick="confirmClose({{ $conteneur->id }}, '{{ $conteneur->name_conteneur }}')">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                            @else
                                            <button type="button" class="btn btn-action btn-open" title="Ouvrir le conteneur" onclick="confirmOpen({{ $conteneur->id }}, '{{ $conteneur->name_conteneur }}')">
                                                <i class="fas fa-unlock"></i>
                                            </button>
                                            @endif
                                            
                                            {{-- <button type="button" class="btn btn-action btn-view" title="Voir détails" onclick="showDetails({{ $conteneur->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button> --}}
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center no-data">
                                        <div class="no-data-content">
                                            <i class="fas fa-inbox"></i>
                                            <h4>Aucun conteneur trouvé</h4>
                                            <p>Commencez par créer votre premier conteneur</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                     @if($conteneurs->hasPages())
                        <div class="pagination-container">
                            {{ $conteneurs->links('pagination.modern') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

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
.btn-close {
    background-color: #fff3e0;
    color: #ff9800;
}

.btn-pdf {
    background-color: #fff3e0;
    color: #f79c14;
}

.btn-pdf:hover {
    background-color: #f79c14;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(247, 156, 20, 0.3);
}

.btn-close:hover {
    background-color: #ff9800;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(255, 152, 0, 0.3);
}

.btn-open {
    background-color: #e8f5e8;
    color: #4caf50;
}

.btn-open:hover {
    background-color: #4caf50;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(76, 175, 80, 0.3);
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

/* Alertes */
.alert-modern {
    border: none;
    border-radius: 8px;
    padding: 16px 20px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
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
    gap: 15px;
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
    padding: 15px;
    border-bottom: 2px solid var(--medium-gray);
    text-transform: uppercase;
    font-size: 0.85rem;
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
    padding: 15px;
    border-bottom: 1px solid var(--medium-gray);
    vertical-align: middle;
}

/* Badges */
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

/* Actions */
.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-action {
    width: 35px;
    height: 35px;
    border: none;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    cursor: pointer;
}

.btn-edit {
    background-color: #e3f2fd;
    color: #1976d2;
}

.btn-edit:hover {
    background-color: #1976d2;
    color: white;
    transform: translateY(-2px);
}

.btn-delete {
    background-color: #ffebee;
    color: #c62828;
}

.btn-delete:hover {
    background-color: #c62828;
    color: white;
    transform: translateY(-2px);
}

/* État vide */
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

.action-buttons {
    display: flex;
    gap: 8px;
    justify-content: center;
}

.btn-action {
    width: 35px;
    height: 35px;
    border: none;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    cursor: pointer;
    font-size: 0.9rem;
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

/* Modal pour les détails */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1050;
    backdrop-filter: blur(5px);
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 30px;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    max-width: 500px;
    width: 90%;
}

.modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--dark-gray);
}

.modal-close:hover {
    color: var(--primary-color);
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
    
    .action-buttons {
        flex-direction: column;
    }
    
    .modern-table {
        font-size: 0.9rem;
    }
}
</style>

<script>
// Fonctionnalité de recherche et filtrage basique
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const tableRows = document.querySelectorAll('.modern-table tbody tr');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const typeValue = typeFilter.value;
        
        tableRows.forEach(row => {
            const name = row.querySelector('.conteneur-name').textContent.toLowerCase();
            const type = row.querySelector('.type-badge').textContent.toLowerCase();
            const status = row.querySelector('.status-badge').textContent.toLowerCase();
            
            const matchesSearch = name.includes(searchTerm);
            const matchesStatus = !statusValue || status.includes(statusValue);
            const matchesType = !typeValue || type.includes(typeValue);
            
            if (matchesSearch && matchesStatus && matchesType) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    typeFilter.addEventListener('change', filterTable);
});
</script>
<script>
// Fonction pour afficher les détails dans un pop-up SweetAlert2
function showDetails(id) {
    // Simulation de données - À remplacer par un appel AJAX réel
    const conteneurData = {
        id: id,
        name: 'Conteneur ' + id,
        type: 'Conteneur',
        statut: 'ouvert',
        numero: 'CTN-' + id,
        created_at: new Date().toLocaleDateString('fr-FR')
    };

    // Appel AJAX pour récupérer les vraies données
    fetch(`/agent/container/${id}/details`)
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                title: `Détails du Conteneur`,
                html: `
                    <div class="details-container">
                        <div class="detail-item">
                            <strong>Nom:</strong> ${data.name_conteneur}
                        </div>
                        <div class="detail-item">
                            <strong>Type:</strong> 
                            <span class="badge type-badge type-${data.type_conteneur.toLowerCase()}">
                                ${data.type_conteneur}
                            </span>
                        </div>
                        <div class="detail-item">
                            <strong>Statut:</strong> 
                            <span class="status-badge status-${data.statut}">
                                <i class="fas ${data.statut === 'ouvert' ? 'fa-unlock' : 'fa-lock'}"></i>
                                ${data.statut}
                            </span>
                        </div>
                        <div class="detail-item">
                            <strong>Numéro:</strong> ${data.numero_conteneur || 'Non renseigné'}
                        </div>
                        <div class="detail-item">
                            <strong>Créé le:</strong> ${new Date(data.created_at).toLocaleDateString('fr-FR')}
                        </div>
                    </div>
                `,
                icon: 'info',
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: 'Modifier',
                cancelButtonText: 'Fermer',
                confirmButtonColor: '#fea219',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
                customClass: {
                    popup: 'sweet-popup',
                    htmlContainer: 'sweet-html-container'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirection vers la page d'édition
                    window.location.href = `/agent/conteneur/${id}/edit`;
                }
            });
        })
        .catch(error => {
            console.error('Erreur:', error);
            Swal.fire({
                title: 'Erreur',
                text: 'Impossible de charger les détails du conteneur',
                icon: 'error',
                confirmButtonColor: '#fea219'
            });
        });
}

// Fonction de confirmation de suppression avec SweetAlert2
function confirmDelete(id, name, button) {
    const form = button.closest('form');
    
    Swal.fire({
        title: 'Êtes-vous sûr ?',
        html: `
            <div class="delete-confirmation">
                <i class="fas fa-exclamation-triangle" style="color: #fea219; font-size: 3rem; margin-bottom: 1rem;"></i>
                <p>Vous êtes sur le point de supprimer le conteneur :</p>
                <p><strong>"${name}"</strong></p>
                <p class="warning-text">Cette action est irréversible !</p>
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
            // Soumettre le formulaire normalement
            form.submit();
        }
    });
}

// Version alternative si vous préférez soumettre le formulaire directement
function confirmDeleteSimple(id, name, button) {
    const form = button.closest('form');
    
    Swal.fire({
        title: 'Confirmation de suppression',
        html: `
            <div class="delete-confirmation">
                <i class="fas fa-trash-alt" style="color: #fea219; font-size: 3rem; margin-bottom: 1rem;"></i>
                <p>Supprimer le conteneur <strong>"${name}"</strong> ?</p>
                <p class="warning-text">Cette action ne peut pas être annulée.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-trash"></i> Supprimer',
        cancelButtonText: '<i class="fas fa-times"></i> Annuler',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        customClass: {
            popup: 'sweet-popup',
            confirmButton: 'sweet-confirm-delete',
            cancelButton: 'sweet-cancel'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Soumettre le formulaire directement
            form.submit();
        }
    });
}

// Fonction pour fermer un conteneur
function confirmClose(id, name) {
    Swal.fire({
        title: 'Fermer le conteneur ?',
        html: `
            <div class="close-confirmation">
                <i class="fas fa-lock" style="color: #ff9800; font-size: 3rem; margin-bottom: 1rem;"></i>
                <p>Vous êtes sur le point de fermer le conteneur :</p>
                <p><strong>"${name}"</strong></p>
                <p class="warning-text">Une fois fermé, vous ne pourrez plus ajouter de colis.</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Oui, fermer !',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#ff9800',
        cancelButtonColor: '#6c757d',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Appel AJAX pour fermer le conteneur
            closeConteneur(id);
        }
    });
}

// Fonction pour générer le PDF du conteneur
function generateConteneurPDF(conteneurId, name) {
    console.log('Téléchargement PDF pour conteneur:', conteneurId, name);
    
    // Afficher le loader
    Swal.fire({
        title: 'Génération du PDF',
        text: 'Veuillez patienter...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Utiliser l'URL correcte selon vos routes agent
    const url = `/agent/container/conteneur/${conteneurId}/pdf`;
    console.log('URL:', url);

    // Créer un lien temporaire pour le téléchargement
    const link = document.createElement('a');
    link.href = url;
    link.style.display = 'none';
    
    // Ajouter au document et déclencher le clic
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Fermer l'alerte après un délai
    setTimeout(() => {
        Swal.close();
        Swal.fire({
            title: 'PDF Généré !',
            text: `Le rapport du conteneur "${name}" a été téléchargé`,
            icon: 'success',
            confirmButtonColor: '#f79c14',
            timer: 3000,
            showConfirmButton: false
        });
    }, 2000);
}

// Fonction alternative si vous voulez générer le PDF côté client avec jsPDF
function generatePDFWithJSPDF(conteneurId) {
    // Cette fonction nécessite l'inclusion de jsPDF et html2canvas
    // Vous pouvez l'utiliser comme alternative si vous préférez
    
    const { jsPDF } = window.jspdf;
    
    // Afficher le loader
    const pdfLoading = document.getElementById('pdfLoading');
    pdfLoading.style.display = 'flex';

    // Sélectionner l'élément à convertir en PDF
    const element = document.querySelector('.modern-card');
    
    html2canvas(element, {
        scale: 2,
        useCORS: true,
        logging: false
    }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF('p', 'mm', 'a4');
        const imgWidth = 210;
        const pageHeight = 295;
        const imgHeight = canvas.height * imgWidth / canvas.width;
        let heightLeft = imgHeight;
        let position = 0;

        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        pdf.save(`conteneur-${conteneurId}-${new Date().toISOString().split('T')[0]}.pdf`);
        pdfLoading.style.display = 'none';
        
        Swal.fire({
            title: 'PDF Généré !',
            text: 'Le fichier PDF a été téléchargé avec succès',
            icon: 'success',
            confirmButtonColor: '#f79c14',
            timer: 3000,
            showConfirmButton: false
        });
    }).catch(error => {
        console.error('Erreur:', error);
        pdfLoading.style.display = 'none';
        Swal.fire({
            title: 'Erreur !',
            text: 'Erreur lors de la génération du PDF',
            icon: 'error',
            confirmButtonColor: '#f79c14'
        });
    });
}

// Fonction pour ouvrir un conteneur
function confirmOpen(id, name) {
    Swal.fire({
        title: 'Ouvrir le conteneur ?',
        html: `
            <div class="open-confirmation">
                <i class="fas fa-unlock" style="color: #4caf50; font-size: 3rem; margin-bottom: 1rem;"></i>
                <p>Vous êtes sur le point d'ouvrir le conteneur :</p>
                <p><strong>"${name}"</strong></p>
                <p class="info-text">Vous pourrez à nouveau ajouter des colis.</p>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Oui, ouvrir !',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#4caf50',
        cancelButtonColor: '#6c757d',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Appel AJAX pour ouvrir le conteneur
            openConteneur(id);
        }
    });
}

// Fonction AJAX pour fermer le conteneur
function closeConteneur(id) {
    fetch(`/agent/conteneur/${id}/close`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Succès !',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#fea219',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Erreur !',
                text: data.message,
                icon: 'error',
                confirmButtonColor: '#fea219'
            });
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        Swal.fire({
            title: 'Erreur !',
            text: 'Une erreur est survenue lors de la fermeture du conteneur',
            icon: 'error',
            confirmButtonColor: '#fea219'
        });
    });
}

// Fonction AJAX pour ouvrir le conteneur
function openConteneur(id) {
    fetch(`/agent/conteneur/${id}/open`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Succès !',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#fea219',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Erreur !',
                text: data.message,
                icon: 'error',
                confirmButtonColor: '#fea219'
            });
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        Swal.fire({
            title: 'Erreur !',
            text: 'Une erreur est survenue lors de l\'ouverture du conteneur',
            icon: 'error',
            confirmButtonColor: '#fea219'
        });
    });
}

// Fonction pour afficher les messages Flash Laravel avec SweetAlert2
document.addEventListener('DOMContentLoaded', function() {
    // Afficher les messages de succès
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

    // Afficher les messages d'erreur
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
@endsection