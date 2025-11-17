@extends('ivoire.layouts.template')
@section('content')
    <div class="container-fluid">
        <!-- Header avec statistiques -->
        <div class="row mb-4 mt-4">
            <div class="col-12">
                <div class="page-header bg-gradient-primary rounded-3 p-4 shadow"
                    style="background: linear-gradient(135deg, #fea219, #fea219) !important;">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="text-white mb-2">🚚 Programmes de Livraison</h1>
                            <p class="text-white-50 mb-0">Gérez et suivez tous vos programmes de livraison</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('livraison.create') }}" class="btn btn-light btn-lg rounded-pill px-4">
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
                                <h4 class="card-title">{{ $totalLivraisons ?? '0' }}</h4>
                                <p class="card-text">Total Livraisons</p>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-shipping-fast fa-2x"></i>
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
                                    @foreach ($chauffeurs as $chauffeur)
                                        <option value="{{ $chauffeur->id }}">{{ $chauffeur->name }} {{ $chauffeur->prenom }}
                                        </option>
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
                                <input type="text" class="modern-input" id="filter-search"
                                    placeholder="Référence, nom...">
                            </div>
                        </div>

                        <!-- Tableau -->
                        <div class="table-responsive">
                            <table class="table table-hover modern-table" id="livraisons-table">
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
                                    @forelse($livraisons as $livraison)
                                        <tr class="livraison-row" data-chauffeur="{{ $livraison->chauffeur_id }}"
                                            data-statut="{{ $livraison->statut }}"
                                            data-date="{{ $livraison->date_depot ? \Carbon\Carbon::parse($livraison->date_depot)->format('Y-m-d') : '' }}"
                                            data-search="{{ strtolower($livraison->reference . ' ' . $livraison->nom_concerne . ' ' . $livraison->prenom_concerne . ' ' . $livraison->nature_objet) }}">
                                            <td style="display: flex; justify-content:center" class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="reference-badge bg-primary text-white rounded-circle me-3">
                                                        <i class="fas fa-shipping-fast"></i>
                                                    </div>
                                                    <div>
                                                        <strong class="d-block">{{ $livraison->reference }}</strong>
                                                        @if ($livraison->quantite > 1)
                                                            <small class="text-muted">{{ $livraison->quantite }}
                                                                articles</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="text-align: center">
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </div>
                                                    <span>
                                                        @if ($livraison->chauffeur)
                                                            {{ $livraison->chauffeur->name }}
                                                            {{ $livraison->chauffeur->prenom }}
                                                        @else
                                                            <span class="text-muted">Non assigné</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </td>
                                            <td style="text-align: center">
                                                <span
                                                    class="badge bg-light text-dark border">{{ $livraison->nature_objet }}</span>
                                            </td>
                                            <td style="text-align: center">
                                                <span class="quantity-badge">{{ $livraison->quantite }}</span>
                                            </td>
                                            <td style="text-align: center">
                                                <strong>{{ $livraison->nom_concerne }}
                                                    {{ $livraison->prenom_concerne }}</strong>
                                                <br><small
                                                    class="text-muted">{{ Str::limit($livraison->adresse_depot, 30) }}</small>
                                            </td>
                                            <td style="text-align: center">
                                                <div>
                                                    <i class="fas fa-phone text-success me-1"></i>
                                                    {{ $livraison->contact }}
                                                </div>
                                                @if ($livraison->email)
                                                    <small class="text-muted">
                                                        <i class="fas fa-envelope me-1"></i>
                                                        {{ Str::limit($livraison->email, 20) }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td style="text-align: center">
                                                @if ($livraison->date_depot)
                                                    <div class="date-cell">
                                                        <i class="fas fa-calendar me-1 text-primary"></i>
                                                        {{ \Carbon\Carbon::parse($livraison->date_depot)->format('d/m/Y') }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td style="text-align: center">
                                                <span class="status-badge status-{{ $livraison->statut }}">
                                                    {{ $livraison->statut }}
                                                </span>
                                            </td>
                                            <td style="text-align: center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button class="btn btn-sm btn-outline-primary btn-action"
                                                        onclick="showDetails({{ $livraison->id }})" title="Détails">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if ($livraison->statut !== 'termine')
                                                        <a href="{{ route('livraison.edit', $livraison->id) }}"
                                                            class="btn btn-sm btn-outline-warning btn-action"
                                                            title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button class="btn btn-sm btn-outline-danger btn-action"
                                                            onclick="confirmDelete({{ $livraison->id }})" title="Supprimer">
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
                                                    <i class="fas fa-shipping-fast fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Aucun programme de livraison trouvé</h5>
                                                    <p class="text-muted">Commencez par créer votre premier programme de
                                                        livraison</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($livraisons->hasPages())
                            <div class="pagination-container">
                                {{ $livraisons->links('pagination.modern') }}
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
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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

        .status-programme {
            background: #e3f2fd;
            color: #1976d2;
        }

        .status-en_cours {
            background: #fff3e0;
            color: #f57c00;
        }

        .status-termine {
            background: #e8f5e8;
            color: var(--secondary-color);
        }

        .status-annule {
            background: #ffebee;
            color: #c62828;
        }

        /* Boutons */
        .btn-action {
            border-radius: 8px;
            padding: 0.375rem 0.75rem;
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Formulaires */
        .modern-select,
        .modern-input {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .modern-select:focus,
        .modern-input:focus {
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
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }

        .custom-modal-content {
            background-color: white;
            margin: 5% auto;
            border-radius: 15px;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
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
            background-color: rgba(255, 255, 255, 0.2);
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
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
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
        let currentLivraisonId = null;
        let isDeleting = false;

        // Initialisation quand le DOM est chargé
        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ Système de livraisons initialisé avec SweetAlert2');
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

            const rows = document.querySelectorAll('.livraison-row');

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

        // Détails de la livraison avec SweetAlert2
        async function showDetails(livraisonId) {
            console.log('Chargement des détails pour:', livraisonId);

            Swal.fire({
                title: 'Chargement...',
                text: 'Récupération des détails de la livraison',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // Adaptez cette URL à votre route de détails pour les livraisons
                const response = await fetch(`/agent/ivory/delivery/${livraisonId}/details`);
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

        function displayDetailsModal(livraisonData) {
            const statutClass = `status-${livraisonData.statut}`;

            Swal.fire({
                title: `Détails - ${livraisonData.reference}`,
                html: `
            <div class="text-start">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-shipping-fast me-2"></i>Informations de la Livraison
                        </h6>
                        <div class="mb-2"><strong>Référence:</strong> ${livraisonData.reference}</div>
                        <div class="mb-2"><strong>Nature:</strong> ${livraisonData.nature_objet}</div>
                        <div class="mb-2"><strong>Quantité:</strong> <span class="quantity-badge">${livraisonData.quantite}</span></div>
                        <div class="mb-2"><strong>Statut:</strong> <span class="status-badge ${statutClass}">${livraisonData.statut}</span></div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-user me-2"></i>Destinataire
                        </h6>
                        <div class="mb-2"><strong>Nom:</strong> ${livraisonData.nom_concerne} ${livraisonData.prenom_concerne}</div>
                        <div class="mb-2"><strong>Contact:</strong> ${livraisonData.contact}</div>
                        <div class="mb-2"><strong>Email:</strong> ${livraisonData.email || 'Non renseigné'}</div>
                        <div class="mb-2"><strong>Adresse:</strong> ${livraisonData.adresse_depot}</div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-truck me-2"></i>Informations du Chauffeur
                        </h6>
                        <div class="mb-2"><strong>Chauffeur:</strong> ${livraisonData.chauffeur || 'Non assigné'}</div>
                        <div class="mb-2"><strong>Date prévue:</strong> ${livraisonData.date_depot || 'Non définie'}</div>
                    </div>
                </div>
                <div class="mt-3 text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Créé le ${livraisonData.created_at}
                </div>
            </div>
        `,
                width: 800,
                confirmButtonColor: '#2196F3',
                confirmButtonText: 'Fermer'
            });
        }

        // Suppression
        function confirmDelete(livraisonId) {
            if (isDeleting) {
                return;
            }

            Swal.fire({
                title: 'Êtes-vous sûr ?',
                html: `
            <div class="text-start">
                <p>Cette action supprimera définitivement ce programme de livraison !</p>
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
                    deleteLivraison(livraisonId);
                }
            });
        }

        async function deleteLivraison(livraisonId) {
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

                // Adaptez cette URL à votre route de suppression pour les livraisons
                const response = await fetch(`/agent/ivory/delivery/${livraisonId}`, {
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
                        text: data.message || 'La livraison a été supprimée avec succès.',
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
                        Si le problème persiste, contactez l'administrateur.
                    </small>
                </div>
            `,
                    icon: 'error',
                    confirmButtonColor: '#2196F3'
                });
            }
        }

        // Autres actions
        function editLivraison(livraisonId) {
            Swal.fire({
                title: 'Modification',
                text: 'Redirection vers la page de modification...',
                icon: 'info',
                showConfirmButton: false,
                timer: 1000,
                timerProgressBar: true
            });

            setTimeout(() => {
                // Adaptez cette URL à votre route d'édition pour les livraisons
                window.location.href = `/agent/livraison/${livraisonId}/edit`;
            }, 1100);
        }

        // Fonctions pour les modals custom
        function openDetailsModal() {
            document.getElementById('detailsModal').style.display = 'block';
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').style.display = 'none';
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
        window.editLivraison = editLivraison;
        window.confirmDelete = confirmDelete;
        window.resetFilters = resetFilters;
        window.openDetailsModal = openDetailsModal;
        window.closeDetailsModal = closeDetailsModal;

        // Afficher les messages flash Laravel avec SweetAlert2
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    title: 'Succès !',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonColor: '#2196F3',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
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
