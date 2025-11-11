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
                            <i class="fas fa-history"></i>
                        </div>
                        <div class="header-text">
                            <h3 class="card-title">Historique des Conteneurs</h3>
                            <p class="card-subtitle">Liste de tous les conteneurs avec leurs colis</p>
                        </div>
                    </div>
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
                                    <th class="text-center">Nom du Conteneur</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Numéro</th>
                                    <th class="text-center">Nombre de Colis</th>
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
                                        @if($conteneur->numero_conteneur)
                                            <span class="numero">{{ $conteneur->numero_conteneur }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">
                                            {{ $conteneur->colis_count }} colis
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="date">{{ $conteneur->created_at->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('agent.conteneur.colis.show', $conteneur->id) }}" class="btn btn-action btn-view" title="Ouvrir le conteneur">
                                                <i class="fas fa-list"></i>
                                            </a>
                                             <button type="button" class="btn btn-action btn-pdf" 
                                                    title="Télécharger PDF"
                                                   onclick="generateConteneurPDF({{ $conteneur->id }}, '{{ $conteneur->name_conteneur }}')">
                                                <i class="fas fa-file-pdf"></i>
                                            </button>
                                         @if($conteneur->statut == 'ouvert')
                                        <button type="button" class="btn btn-action btn-close" title="Fermer le conteneur" onclick="confirmClose({{ $conteneur->id }}, '{{ $conteneur->name_conteneur }}')">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                        @else
                                        <button type="button" class="btn btn-action btn-open" title="Ouvrir le conteneur" onclick="confirmOpen({{ $conteneur->id }}, '{{ $conteneur->name_conteneur }}')">
                                            <i class="fas fa-unlock"></i>
                                        </button>
                                        @endif
                                        
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center no-data">
                                        <div class="no-data-content">
                                            <i class="fas fa-inbox"></i>
                                            <h4>Aucun conteneur trouvé</h4>
                                            <p>Aucun conteneur n'a été créé pour le moment</p>
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

.btn-close:hover {
    background-color: #ff9800;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(255, 152, 0, 0.3);
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

/* Table Controls */
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

.badge-primary {
    background-color: #007bff;
    color: white;
}

/* Actions */
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
    text-decoration: none;
}

.btn-view {
    background-color: #e3f2fd;
    color: #1976d2;
}

.btn-view:hover {
    background-color: #1976d2;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(25, 118, 210, 0.3);
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
// Fonctionnalité de recherche et filtrage
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
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
@endsection