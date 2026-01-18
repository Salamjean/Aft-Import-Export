@extends('agent.layouts.template')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card modern-card">
                    <div class="card-header modern-header">
                        <div class="header-content">
                            <div class="header-icon">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                            <div class="header-text">
                                <h3 class="card-title">Chargement des Colis</h3>
                                <p class="card-subtitle">Scanner les colis pour les charger dans les conteneurs</p>
                            </div>
                        </div>
                        <div class="header-actions">
                            <button onclick="openQRScannerCharge()" class="btn modern-btn text-white"
                                style="background-color:#007bff; margin-right: 10px;">
                                <i class="fas fa-qrcode"></i>
                                Scanner pour Chargement
                            </button>
                            <a href="{{ route('agent.scan.entrepot') }}" class="btn modern-btn"
                                style="background-color:#6c757d;">
                                <i class="fas fa-warehouse"></i>
                                Voir Entrepôt
                            </a>
                        </div>
                    </div>
                    <div class="card-body">

                        <!-- Filtres et recherche -->
                        <form method="GET" action="{{ route('agent.scan.charge') }}" class="table-controls">
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
                                        <a href="{{ route('scan.charge') }}" class="btn btn-secondary">
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
                                        <th class="text-center" style="font-size:12px">Conteneur</th>
                                        <th class="text-center" style="font-size:12px">Montant Total</th>
                                        <th class="text-center" style="font-size:12px">St. Colis</th>
                                        <th class="text-center" style="font-size:12px">Progression</th>
                                        <th class="text-center" style="font-size:12px">Date</th>
                                        <th class="text-center" style="font-size:12px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($colis as $item)
                                        <tr>
                                            <td class="text-center">
                                                @if($item->statut_paiement == 'non_paye')
                                                    <i class="fas fa-times-circle text-danger" title="Non payé"
                                                        style="font-size: 18px;"></i>
                                                @elseif($item->statut_paiement == 'partiellement_paye')
                                                    <i class="fas fa-exclamation-circle text-warning" title="Partiellement payé"
                                                        style="font-size: 18px;"></i>
                                                @else
                                                    <i class="fas fa-check-circle text-success" title="Totalement payé"
                                                        style="font-size: 18px;"></i>
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
                                                @if($item->conteneur)
                                                    <span class="badge bg-dark text-white">
                                                        <i class="fas fa-box"></i> {{ $item->conteneur->name_conteneur }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">Non assigné</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <strong>{{ number_format($item->montant_total, 0) }}</strong>
                                                {{ $item->devise }}
                                                <br>
                                                <small>Payé: {{ number_format($item->montant_paye, 0) }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="status-badge status-{{ $item->statut }}">
                                                    @if($item->statut == 'valide')
                                                        Validé
                                                    @elseif($item->statut == 'charge')
                                                        Chargé
                                                    @elseif($item->statut == 'entrepot')
                                                        Entrepôt
                                                    @elseif($item->statut == 'decharge')
                                                        Déchargé
                                                    @elseif($item->statut == 'livre')
                                                        Livré
                                                    @else
                                                        Annulé
                                                    @endif
                                                </span>
                                                <br>

                                                <!-- Barre de progression multi-couleurs -->
                                                @if($item->total_individuels > 0)
                                                    <div class="progress-multi mt-1">
                                                        @php
                                                            $pourcentageValide = ($item->individuels_valides / $item->total_individuels) * 100;
                                                            $pourcentageCharge = ($item->individuels_charges / $item->total_individuels) * 100;
                                                            $pourcentageEntrepot = ($item->individuels_entrepot / $item->total_individuels) * 100;
                                                            $pourcentageDecharge = ($item->individuels_decharges / $item->total_individuels) * 100;
                                                            $pourcentageLivre = ($item->individuels_livres / $item->total_individuels) * 100;
                                                            $pourcentageAnnule = ($item->individuels_annules / $item->total_individuels) * 100;
                                                        @endphp

                                                        @if($item->individuels_valides > 0)
                                                            <div class="progress-segment progress-valide"
                                                                style="width: {{ $pourcentageValide }}%"></div>
                                                        @endif

                                                        @if($item->individuels_charges > 0)
                                                            <div class="progress-segment progress-charge"
                                                                style="width: {{ $pourcentageCharge }}%"></div>
                                                        @endif

                                                        @if($item->individuels_entrepot > 0)
                                                            <div class="progress-segment progress-entrepot"
                                                                style="width: {{ $pourcentageEntrepot }}%"></div>
                                                        @endif

                                                        @if($item->individuels_decharges > 0)
                                                            <div class="progress-segment progress-decharge"
                                                                style="width: {{ $pourcentageDecharge }}%"></div>
                                                        @endif

                                                        @if($item->individuels_livres > 0)
                                                            <div class="progress-segment progress-livre"
                                                                style="width: {{ $pourcentageLivre }}%"></div>
                                                        @endif

                                                        @if($item->individuels_annules > 0)
                                                            <div class="progress-segment progress-annule"
                                                                style="width: {{ $pourcentageAnnule }}%"></div>
                                                        @endif
                                                    </div>

                                                    <!-- Légende -->
                                                    <div class="statut-legend">
                                                        @if($item->individuels_valides > 0)
                                                            <span class="statut-legend-item">
                                                                <span class="statut-color"
                                                                    style="background-color: #6c757d;"></span>V:{{ $item->individuels_valides }}
                                                            </span>
                                                        @endif
                                                        @if($item->individuels_charges > 0)
                                                            <span class="statut-legend-item">
                                                                <span class="statut-color"
                                                                    style="background-color: #ffc107;"></span>C:{{ $item->individuels_charges }}
                                                            </span>
                                                        @endif
                                                        @if($item->individuels_entrepot > 0)
                                                            <span class="statut-legend-item">
                                                                <span class="statut-color"
                                                                    style="background-color: #17a2b8;"></span>E:{{ $item->individuels_entrepot }}
                                                            </span>
                                                        @endif
                                                        @if($item->individuels_decharges > 0)
                                                            <span class="statut-legend-item">
                                                                <span class="statut-color"
                                                                    style="background-color: #007bff;"></span>D:{{ $item->individuels_decharges }}
                                                            </span>
                                                        @endif
                                                        @if($item->individuels_livres > 0)
                                                            <span class="statut-legend-item">
                                                                <span class="statut-color"
                                                                    style="background-color: #28a745;"></span>L:{{ $item->individuels_livres }}
                                                            </span>
                                                        @endif
                                                        @if($item->individuels_annules > 0)
                                                            <span class="statut-legend-item">
                                                                <span class="statut-color"
                                                                    style="background-color: #dc3545;"></span>A:{{ $item->individuels_annules }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <!-- Barre de progression pour le chargement -->
                                                @if($item->total_individuels > 0)
                                                    @php
                                                        $pourcentageCharge = ($item->individuels_charges / $item->total_individuels) * 100;
                                                    @endphp
                                                    <div class="progress mt-2" style="height: 20px;">
                                                        <div class="progress-bar bg-warning" role="progressbar"
                                                            style="width: {{ $pourcentageCharge }}%"
                                                            aria-valuenow="{{ $pourcentageCharge }}" aria-valuemin="0"
                                                            aria-valuemax="100">
                                                            {{ $item->individuels_charges }}/{{ $item->total_individuels }}
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ number_format($pourcentageCharge, 1) }}% chargés
                                                    </small>
                                                @else
                                                    <span class="text-muted">Aucune unité</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $item->created_at->format('d/m/Y') }}
                                                <br>
                                                <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="action-buttons">
                                                    <button class="btn-action btn-view"
                                                        onclick="showColisDetails({{ $item->id }})" title="Voir détails">
                                                        <i class="fas fa-eye"></i>
                                                    </button>

                                                    <!-- Bouton de paiement - grisé si totalement payé -->
                                                    <button
                                                        class="btn-action {{ $item->statut_paiement == 'totalement_paye' ? 'btn-payment-disabled' : 'btn-payment' }}"
                                                        @if($item->statut_paiement != 'totalement_paye')
                                                            onclick="showPaymentForm({{ $item->id }}, '{{ $item->reference_colis }}', {{ $item->montant_total }}, {{ $item->montant_paye }}, {{ $item->reste_a_payer ?? 0 }}, '{{ $item->devise }}')"
                                                        @else disabled @endif
                                                        title="{{ $item->statut_paiement == 'totalement_paye' ? 'Paiement complet' : 'Enregistrer un paiement' }}">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </button>

                                                    <!-- Bouton pour scanner une unité -->
                                                    {{-- <button class="btn-action btn-labels"
                                                        onclick="scanUniteCharge({{ $item->id }})" title="Scanner une unité">
                                                        <i class="fas fa-qrcode"></i>
                                                    </button> --}}

                                                    <!-- Bouton pour les documents -->
                                                    <button class="btn-action btn-labels"
                                                        onclick="showDocumentsOptions({{ $item->id }})"
                                                        title="Télécharger les documents">
                                                        <i class="fas fa-file-alt"></i>
                                                    </button>

                                                    {{-- <a href="{{ route('colis.edit', $item->id) }}"
                                                        class="btn-action btn-edit" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a> --}}
                                                    {{-- <button class="btn-action btn-delete"
                                                        onclick="confirmDelete({{ $item->id }}, '{{ $item->reference_colis }}')"
                                                        title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button> --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center">
                                                <div class="no-data-content py-5">
                                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                                    <h4>Aucun colis trouvé</h4>
                                                    <p class="text-muted">Aucun colis disponible pour le chargement</p>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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

        .status-charge {
            background-color: #ffeaa7;
            color: #856404;
            border: 1px solid #fdcb6e;
        }

        .status-entrepot {
            background-color: #d1edff;
            color: #0c63e4;
        }

        .status-decharge {
            background-color: #cce7ff;
            color: #0066cc;
        }

        .status-livre {
            background-color: #d1f7e4;
            color: #0d8b5a;
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

        /* Barre de progression multi-couleurs */
        .progress-multi {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-segment {
            height: 100%;
            display: inline-block;
        }

        .progress-valide {
            background-color: #6c757d;
        }

        .progress-charge {
            background-color: #ffc107;
        }

        .progress-entrepot {
            background-color: #17a2b8;
        }

        .progress-decharge {
            background-color: #007bff;
        }

        .progress-livre {
            background-color: #28a745;
        }

        .progress-annule {
            background-color: #dc3545;
        }

        /* Légende des statuts */
        .statut-legend {
            font-size: 9px;
            margin-top: 2px;
        }

        .statut-legend-item {
            display: inline-flex;
            align-items: center;
            margin-right: 8px;
        }

        .statut-color {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 3px;
        }

        /* Styles spécifiques pour la page de chargement */
        .progress {
            border-radius: 10px;
        }

        .progress-bar {
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 600;
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

            .table-controls .row>div {
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
        // Variables globales pour le scanner
        let qrScanner = null;
        let isScanning = false;

        // Fonction pour ouvrir le scanner QR code pour chargement
        function openQRScannerCharge() {
            console.log("🔓 Ouverture du scanner QR pour chargement...");

            Swal.fire({
                title: '📦 Scanner pour Chargement',
                html: `
                <div class="text-center">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Scanner les unités qui sont en entrepôt pour les charger
                    </div>
                    <div id="qr-scanner-container" style="width: 100%; max-width: 500px; margin: 0 auto;">
                        <div id="qr-reader" style="width: 100%; min-height: 300px; border: 2px dashed #007bff; border-radius: 10px; background: #f8f9fa;"></div>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted" id="scan-status">🔄 Initialisation du scanner...</p>
                        <div class="btn-group">
                            <button type="button" class="btn btn-warning btn-sm" onclick="switchCamera()" id="switch-camera-btn" disabled>
                                <i class="fas fa-camera-rotate"></i> Changer caméra
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="stopQRScannerAndClose()">
                                <i class="fas fa-stop"></i> Arrêter
                            </button>
                        </div>
                    </div>

                    <!-- Alternative manuelle -->
                    <div class="mt-4">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-keyboard me-2"></i>Entrée manuelle</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="manualCodeInput" 
                                               placeholder="Ex: CO-3HAJVMZS" autocomplete="off">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-outline-primary w-100" onclick="validateManualCodeCharge()">
                                            Valider
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `,
                showConfirmButton: false,
                showCloseButton: true,
                width: 600,
                backdrop: true,
                allowOutsideClick: false,
                didOpen: () => {
                    startQRScannerCharge();
                    // Ajouter l'événement Enter pour l'entrée manuelle
                    const manualInput = document.getElementById('manualCodeInput');
                    if (manualInput) {
                        manualInput.focus();
                        manualInput.addEventListener('keypress', function (e) {
                            if (e.key === 'Enter') {
                                validateManualCodeCharge();
                            }
                        });
                    }
                },
                willClose: () => {
                    stopQRScanner();
                }
            });
        }

        // Démarrer le scanner QR pour chargement
        function startQRScannerCharge() {
            console.log("🚀 Démarrage du scanner chargement...");

            const container = document.getElementById('qr-scanner-container');
            if (!container) {
                console.error('❌ Container QR scanner non trouvé');
                return;
            }

            try {
                // Vérifier que la bibliothèque est disponible
                if (typeof Html5Qrcode === 'undefined') {
                    throw new Error('❌ Bibliothèque Html5Qrcode non chargée');
                }

                // Vider le conteneur
                container.innerHTML = `
                <div id="qr-reader" style="width: 100%; min-height: 300px; border: 2px dashed #007bff; border-radius: 10px; background: #f8f9fa;">
                    <div class="text-center py-5">
                        <i class="fas fa-camera text-muted" style="font-size: 2rem;"></i>
                        <p class="mt-2">Initialisation de la caméra...</p>
                    </div>
                </div>
            `;

                // Créer l'instance du scanner
                const html5QrCode = new Html5Qrcode("qr-reader");

                const config = {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    },
                    aspectRatio: 1.0
                };

                console.log("⚙️ Configuration du scanner:", config);
                updateScanStatus('🔍 Recherche des caméras...');

                // Démarrer le scanner
                html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    onScanSuccessCharge,
                    onScanFailure
                ).then(() => {
                    console.log("✅ Scanner démarré avec succès");
                    qrScanner = html5QrCode;
                    isScanning = true;
                    updateScanStatus('✅ Scanner actif - Pointez vers un QR code');

                    // Activer le bouton de changement de caméra
                    const switchBtn = document.getElementById('switch-camera-btn');
                    if (switchBtn) {
                        switchBtn.disabled = false;
                    }

                }).catch((error) => {
                    console.error("❌ Erreur démarrage scanner:", error);
                    handleCameraError(error);
                });

            } catch (error) {
                console.error('❌ Erreur création scanner:', error);
                handleCameraError(error);
            }
        }

        // Callback quand un QR code est détecté pour chargement
        function onScanSuccessCharge(decodedText, decodedResult) {
            console.log("🎯 QR Code détecté pour chargement:", decodedText);

            // Mettre à jour le statut immédiatement
            updateScanStatus('✅ QR code détecté! Traitement...');

            // Désactiver les boutons pendant le traitement
            const switchBtn = document.getElementById('switch-camera-btn');
            if (switchBtn) switchBtn.disabled = true;

            // Arrêter le scanner et traiter le code
            handleScannedQRCodeCharge(decodedText);
        }

        // Fonction pour traiter le QR code scanné pour chargement
        function handleScannedQRCodeCharge(qrCode) {
            console.log("🔄 Traitement du QR code pour chargement:", qrCode);

            // Récupérer l'ID du conteneur sélectionné
            const conteneurSelect = document.getElementById('conteneurSelect');
            const conteneurId = conteneurSelect ? conteneurSelect.value : null;

            // Arrêter le scanner IMMÉDIATEMENT
            stopQRScanner().then(() => {
                console.log("✅ Scanner arrêté, fermeture du modal...");

                // Fermer le modal SweetAlert2
                Swal.close();

                // Afficher l'indicateur de chargement
                showProcessingModalCharge(qrCode, conteneurId);

            }).catch(error => {
                console.error("❌ Erreur lors de l'arrêt du scanner:", error);
                // Continuer quand même le traitement
                Swal.close();
                showProcessingModalCharge(qrCode, conteneurId);
            });
        }

        // Afficher le modal de traitement pour chargement
        function showProcessingModalCharge(qrCode, conteneurId) {
            Swal.fire({
                title: '🔄 Chargement en cours...',
                html: `
                <div class="text-center">
                    <i class="fas fa-truck-loading fa-spin text-primary" style="font-size: 3rem;"></i>
                    <div class="mt-3 p-3 bg-light rounded">
                        <strong>Code scanné:</strong><br>
                        <code style="font-size: 1.1rem; word-break: break-all; background: #fff; padding: 5px 10px; border-radius: 4px;">${qrCode}</code>
                        ${conteneurId ? `<br><strong>Conteneur:</strong> #${conteneurId}` : ''}
                    </div>
                    <p class="mt-3">Chargement de l'unité dans le conteneur...</p>
                </div>
            `,
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                backdrop: true,
                didOpen: () => {
                    // Traiter le code scanné
                    processScannedCodeCharge(qrCode, conteneurId);
                }
            });
        }

        // Fonction pour traiter le code scanné avec le serveur pour chargement
        function processScannedCodeCharge(qrCode, conteneurId) {
            console.log("📡 Envoi au serveur pour chargement:", qrCode, conteneurId);

            const url = '/agent/scan/scan-qr-charge';
            console.log("📍 URL utilisée:", url);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    qr_code: qrCode,
                    conteneur_id: conteneurId
                })
            })
                .then(response => {
                    console.log("📨 Réponse HTTP:", response.status, response.statusText);

                    if (!response.ok) {
                        throw new Error(`Erreur serveur: ${response.status} - ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("✅ Réponse JSON:", data);

                    if (data.success) {
                        showScanSuccessCharge(data, qrCode);
                    } else {
                        showScanErrorCharge(data, qrCode);
                    }
                })
                .catch(error => {
                    console.error('❌ Erreur complète:', error);
                    showScanConnectionErrorCharge(error, qrCode);
                });
        }

        // Afficher le succès du scan pour chargement
        function showScanSuccessCharge(data, qrCode) {
            Swal.fire({
                title: '✅ Chargement Réussi !',
                html: `
                <div class="text-center">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    <p class="mt-3"><strong>${data.message}</strong></p>

                    ${data.unite ? `
                    <div class="alert alert-success">
                        <h6>📦 Informations de l'unité</h6>
                        <p class="mb-1"><strong>Code:</strong> ${data.unite.code_colis}</p>
                        <p class="mb-1"><strong>Produit:</strong> ${data.unite.produit}</p>
                        <p class="mb-1"><strong>Position:</strong> ${data.unite.position}</p>
                        <p class="mb-1"><strong>Statut:</strong> 
                            <span class="badge bg-warning">${data.unite.nouveau_statut}</span>
                        </p>
                        <p class="mb-1"><strong>Localisation:</strong> ${data.unite.localisation}</p>
                    </div>
                    ` : ''}

                    ${data.colis ? `
                    <div class="alert alert-info">
                        <h6>📊 Progression du chargement</h6>
                        <p class="mb-1"><strong>Référence:</strong> ${data.colis.reference_colis}</p>
                        <p class="mb-1"><strong>Progression:</strong> 
                            ${data.colis.unites_chargees}/${data.colis.total_unites} unités chargées
                        </p>
                        <div class="progress mt-2">
                            <div class="progress-bar bg-warning" 
                                 role="progressbar" 
                                 style="width: ${data.colis.progression}%"
                                 aria-valuenow="${data.colis.progression}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                ${data.colis.progression}%
                            </div>
                        </div>
                    </div>
                    ` : ''}
                </div>
            `,
                icon: 'success',
                confirmButtonColor: '#007bff',
                confirmButtonText: '🔄 Scanner une autre unité',
                cancelButtonText: '📋 Retour à la liste',
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    openQRScannerCharge();
                } else {
                    location.reload();
                }
            });
        }

        // Afficher les erreurs pour le chargement
        function showScanErrorCharge(data, qrCode) {
            Swal.fire({
                title: 'ℹ️ Information',
                html: `
                <div class="text-center">
                    <i class="fas fa-info-circle text-warning" style="font-size: 3rem;"></i>
                    <p class="mt-3"><strong>${data.message}</strong></p>
                    ${data.unite ? `
                    <div class="alert alert-warning mt-3">
                        <p><strong>Code:</strong> ${data.unite.code_colis}</p>
                        <p><strong>Statut actuel:</strong> <span class="badge bg-warning">${data.unite.statut}</span></p>
                        <p><strong>Produit:</strong> ${data.unite.produit}</p>
                    </div>
                    ` : ''}
                </div>
            `,
                icon: 'info',
                confirmButtonColor: '#17a2b8',
                confirmButtonText: '🔄 Scanner à nouveau'
            }).then(() => {
                openQRScannerCharge();
            });
        }

        // Afficher les erreurs de connexion pour chargement
        function showScanConnectionErrorCharge(error, qrCode) {
            Swal.fire({
                title: '❌ Erreur de Connexion',
                html: `
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                    <p class="mt-3"><strong>Erreur lors du chargement</strong></p>
                    <p class="text-muted">${error.message}</p>
                    <div class="mt-3">
                        <button class="btn btn-primary me-2" onclick="openQRScannerCharge()">
                            <i class="fas fa-redo"></i> Ressayer
                        </button>
                    </div>
                </div>
            `,
                icon: 'error',
                showConfirmButton: false,
                showCloseButton: true,
                width: 500
            });
        }

        // Entrée manuelle depuis le scanner pour chargement
        function validateManualCodeCharge() {
            const manualInput = document.getElementById('manualCodeInput');
            const code = manualInput ? manualInput.value.trim() : '';
            const conteneurSelect = document.getElementById('conteneurSelect');
            const conteneurId = conteneurSelect ? conteneurSelect.value : null;

            if (!code) {
                Swal.fire({
                    title: 'Champ vide',
                    text: 'Veuillez entrer un code',
                    icon: 'warning',
                    confirmButtonColor: '#007bff'
                });
                return;
            }

            Swal.close();
            processScannedCodeCharge(code, conteneurId);
        }

        // Scanner une unité spécifique d'un colis
        function scanUniteCharge(colisId) {
            Swal.fire({
                title: 'Scanner une Unité',
                html: `
                <div class="text-center">
                    <p>Entrez le code de l'unité à charger</p>
                    <input type="text" id="uniteCodeInput" class="form-control form-control-lg" 
                           placeholder="Ex: CO-3HAJVMZS" 
                           style="text-align: center; font-size: 1.2rem;"
                           autocomplete="off" autofocus>
                    <div class="mt-3">
                        <label for="conteneurSelectSingle" class="form-label">Conteneur (Optionnel)</label>
                        <select class="form-control" id="conteneurSelectSingle">
                            <option value="">Sélectionner un conteneur</option>
                            <option value="1">Conteneur #1</option>
                            <option value="2">Conteneur #2</option>
                            <option value="3">Conteneur #3</option>
                        </select>
                    </div>
                </div>
            `,
                showCancelButton: true,
                confirmButtonText: 'Charger',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                preConfirm: () => {
                    const code = document.getElementById('uniteCodeInput').value.trim();
                    if (!code) {
                        Swal.showValidationMessage('Veuillez saisir un code');
                        return false;
                    }
                    return {
                        code: code,
                        conteneurId: document.getElementById('conteneurSelectSingle').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    processScannedCodeCharge(result.value.code, result.value.conteneurId);
                }
            });
        }

        // ============================================================================
        // FONCTIONS EXISTANTES DE LA PREMIÈRE VUE (conservées pour garder toutes les fonctionnalités)
        // ============================================================================

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

            fetch(`/agent/parcel/${colisId}`)
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
                            <!-- Section Informations Générales -->
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

                            <!-- Section Informations Financières -->
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

                            <!-- Section Détails des Types de Colis -->
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
            return date.toLocaleDateString('fr-FR') + ' ' + date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
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
                    fetch(`/agent/colis/${colisId}`, {
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

                    methodeSelect.addEventListener('change', function () {
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

            fetch(`/agent/parcel/${colisId}/paiement`, {
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

            switch (type) {
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

            switch (type) {
                case 'etiquette':
                    url = `/agent/parcel/${colisId}/etiquettes?action=${action}`;
                    break;
                case 'facture':
                    url = `/agent/parcel/${colisId}/facture?action=${action}`;
                    break;
                case 'bon_livraison':
                    url = `/agent/parcel/${colisId}/bon-livraison?action=${action}`;
                    break;
            }

            // Redirection vers la route spécifique
            window.location.href = url;
        }

        // ============================================================================
        // FONCTIONS DU SCANNER GÉNÉRIQUES (réutilisées)
        // ============================================================================

        // Fonction pour arrêter le scanner avec Promise
        function stopQRScanner() {
            return new Promise((resolve, reject) => {
                if (qrScanner && isScanning) {
                    console.log("🛑 Arrêt du scanner...");
                    qrScanner.stop().then(() => {
                        console.log("✅ Scanner arrêté avec succès");
                        qrScanner.clear();
                        qrScanner = null;
                        isScanning = false;
                        updateScanStatus('Scanner arrêté');
                        resolve();
                    }).catch((error) => {
                        console.error("❌ Erreur arrêt scanner:", error);
                        qrScanner = null;
                        isScanning = false;
                        reject(error);
                    });
                } else {
                    console.log("ℹ️ Scanner déjà arrêté");
                    resolve();
                }
            });
        }

        // Arrêt manuel du scanner
        function stopQRScannerAndClose() {
            stopQRScanner().then(() => {
                Swal.close();
            });
        }

        // Callback pour les erreurs de scan (normales)
        function onScanFailure(error) {
            // Ignorer les erreurs normales de scan
            if (error && !error.toString().includes('No MultiFormat Readers')) {
                console.log("🔍 Scan en cours...", error);
            }
        }

        // Mettre à jour le statut du scan
        function updateScanStatus(message) {
            const statusElement = document.getElementById('scan-status');
            if (statusElement) {
                statusElement.textContent = message;
                if (message.includes('✅') || message.includes('actif')) {
                    statusElement.className = 'text-success fw-bold';
                } else if (message.includes('❌') || message.includes('Erreur')) {
                    statusElement.className = 'text-danger fw-bold';
                } else {
                    statusElement.className = 'text-muted';
                }
            }
        }

        // Gestion des erreurs de caméra
        function handleCameraError(error) {
            let errorMessage = "Erreur d'accès à la caméra";
            console.error("❌ Erreur caméra:", error);

            if (error.name === 'NotAllowedError') {
                errorMessage = "📵 Permission caméra refusée";
            } else if (error.name === 'NotFoundError') {
                errorMessage = "📷 Aucune caméra trouvée";
            } else if (error.name === 'NotSupportedError') {
                errorMessage = "🌐 Navigateur non supporté";
            }

            updateScanStatus('❌ ' + errorMessage);

            const scannerContainer = document.getElementById('qr-scanner-container');
            if (scannerContainer) {
                scannerContainer.innerHTML = `
                <div class="alert alert-danger text-center py-4">
                    <i class="fas fa-camera-slash text-danger" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">${errorMessage}</h5>
                    <button class="btn btn-primary mt-2" onclick="openManualInput()">
                        <i class="fas fa-keyboard"></i> Saisir manuellement
                    </button>
                </div>
            `;
            }
        }

        // Fonction pour changer de caméra
        function switchCamera() {
            if (!qrScanner || !isScanning) return;

            updateScanStatus('🔄 Changement de caméra...');
            const switchBtn = document.getElementById('switch-camera-btn');
            if (switchBtn) switchBtn.disabled = true;

            stopQRScanner().then(() => {
                setTimeout(() => {
                    startQRScannerCharge();
                }, 500);
            });
        }

        // Ouvrir l'entrée manuelle directe
        function openManualInput() {
            Swal.fire({
                title: '⌨️ Saisie manuelle',
                html: `
                <div class="text-center">
                    <i class="fas fa-keyboard text-primary" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <p>Entrez le code du colis manuellement</p>
                    <input type="text" id="manualInput" class="form-control form-control-lg" 
                           placeholder="Ex: CO-3HAJVMZS" 
                           style="text-align: center; font-size: 1.2rem;"
                           autocomplete="off" autofocus>
                </div>
            `,
                showCancelButton: true,
                confirmButtonText: 'Valider',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                preConfirm: () => {
                    const code = document.getElementById('manualInput').value.trim();
                    if (!code) {
                        Swal.showValidationMessage('Veuillez saisir un code');
                        return false;
                    }
                    if (code.length < 3) {
                        Swal.showValidationMessage('Le code doit contenir au moins 3 caractères');
                        return false;
                    }
                    return code;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    processScannedCodeCharge(result.value, null);
                }
            });
        }

        // Nettoyage
        window.addEventListener('beforeunload', function () {
            stopQRScanner();
        });

        // Afficher les messages flash Laravel avec SweetAlert2
        document.addEventListener('DOMContentLoaded', function () {
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

        console.log("✅ Page de chargement des colis chargée avec toutes les fonctionnalités");
    </script>

@endsection