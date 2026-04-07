@extends('admin.layouts.template')

@section('content')
    <!-- Styles CSS -->
    <style>
        .bilan-financier-container {
            padding: 24px;
            background: #f1f5f9;
            min-height: 100vh;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        /* Header */
        .bilan-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            background: white;
            padding: 28px;
            border-radius: 24px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .header-title {
            font-size: 32px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.025em;
        }

        .header-subtitle {
            color: #64748b;
            margin: 8px 0 0 0;
            font-size: 16px;
            font-weight: 500;
        }

        /* Filters */
        .filter-card {
            background: white;
            padding: 24px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
        }

        .filter-card-header h5 {
            font-size: 16px;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .modern-input {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1.5px solid #e2e8f0;
            font-weight: 500;
            color: #1e293b;
            transition: all 0.2s ease;
            background-color: #f8fafc;
        }

        .modern-input:focus {
            border-color: #fea219;
            box-shadow: 0 0 0 4px rgba(254, 162, 25, 0.1);
            background-color: white;
        }

        .btn-filter {
            background: #fea219;
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(254, 162, 25, 0.2);
        }

        .btn-filter:hover {
            background: #e89215;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(254, 162, 25, 0.3);
            color: white;
        }

        .btn-reset {
            background: #f1f5f9;
            color: #64748b;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .btn-reset:hover {
            background: #e2e8f0;
            color: #0f172a;
            transform: rotate(90deg);
        }

        .btn-historique {
            background: white;
            color: #334154;
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none !important;
            border: 1.5px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .btn-historique:hover {
            background: #f8fafc;
            border-color: #fea219;
            color: #fea219;
        }

        .modern-btn {
            border-radius: 14px;
            padding: 12px 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .modern-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        /* Section Titles */
        .section-title {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.02em;
        }

        .section-title i {
            color: #fea219;
            font-size: 20px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .stat-card {
            background: white;
            padding: 28px;
            border-radius: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #f1f5f9;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            flex-shrink: 0;
        }

        .total-card .stat-icon { background: linear-gradient(135deg, #6366f1, #4f46e5); }
        .paye-card .stat-icon { background: linear-gradient(135deg, #10b981, #059669); }
        .impaye-card .stat-icon { background: linear-gradient(135deg, #f43f5e, #e11d48); }
        .taux-card .stat-icon { background: linear-gradient(135deg, #fea219, #ea9215); }

        .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            line-height: 1.2;
        }

        .stat-label {
            color: #64748b;
            margin: 4px 0;
            font-size: 15px;
            font-weight: 600;
        }

        .stat-detail {
            color: #94a3b8;
            font-size: 13px;
            font-weight: 500;
            margin-top: 6px;
        }

        .progress-bar {
            width: 100%;
            height: 10px;
            background: #f1f5f9;
            border-radius: 10px;
            margin-top: 12px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: #fea219;
            border-radius: 10px;
            transition: width 1s ease-in-out;
        }

        /* Mini Methods Grid */
        .method-mini-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }

        .method-mini-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 16px;
            background: #f8fafc;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .method-mini-item:hover {
            border-color: #fea219;
            background: white;
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .method-mini-item .label {
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .method-mini-item .value {
            font-size: 16px;
            font-weight: 800;
            color: #0d9488;
        }

        /* Agence Cards */
        .agences-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 28px;
        }

        .agence-card {
            background: white;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }

        .agence-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.06);
        }

        .agence-badge {
            background: #fff7ed;
            color: #c2410c;
            padding: 10px 18px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #ffedd5;
        }

        .encaisse-agents-box {
            background: #f0fdf4;
            padding: 24px;
            border-radius: 20px;
            text-align: center;
            border: 1px solid #dcfce7;
            margin-top: 10px;
        }

        .encaisse-agents-box .label {
            color: #166534;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 12px;
            display: block;
        }

        .encaisse-agents-box .amount {
            font-size: 28px;
            font-weight: 900;
            color: #15803d;
            margin: 0;
        }

        /* Chart Section */
        .analytics-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            border: 1px solid #f1f5f9;
        }

        .analytics-card.main-chart {
            border-radius: 28px;
            padding: 32px;
        }

        .chart-container {
            height: 400px;
        }

        @media (max-width: 768px) {
            .bilan-header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }
        }
    </style>

    <div class="bilan-financier-container">
        <!-- Header -->
        <div class="bilan-header">
            <div class="header-content">
                <h1 class="header-title">Bilan Financier</h1>
                <p class="header-subtitle">Suivi en temps réel des encaissements et de la facturation</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.bilan_financier.export', ['date_debut' => $dateDebut, 'date_fin' => $dateFin, 'agence_id' => $agenceId]) }}" 
                   class="btn modern-btn text-white" style="background-color:#198754; margin-right: 10px;">
                    <i class="fas fa-file-excel"></i>
                    Exporter Excel
                </a>
                <a href="{{ route('admin.bilan_financier.historique') }}" class="btn-historique">
                    <i class="fas fa-history"></i>
                    Historique des Paiements
                </a>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters-section mb-4">
            <div class="filter-card">
                <div class="filter-card-header mb-3">
                    <h5 class="m-0"><i class="fas fa-filter text-primary me-2"></i> Filtrer les données</h5>
                </div>
                <form action="{{ route('admin.bilan_financier.index') }}" method="GET" class="filter-form">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">
                                <i class="far fa-calendar-alt me-1"></i> Date Début
                            </label>
                            <input type="date" name="date_debut" class="form-control modern-input" value="{{ $dateDebut }}">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">
                                <i class="far fa-calendar-alt me-1"></i> Date Fin
                            </label>
                            <input type="date" name="date_fin" class="form-control modern-input" value="{{ $dateFin }}">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">
                                <i class="fas fa-building me-1"></i> Agence
                            </label>
                            <select name="agence_id" class="form-select modern-input">
                                <option value="">Toutes les agences</option>
                                @foreach($agences as $agence)
                                    <option value="{{ $agence->id }}" {{ $agenceId == $agence->id ? 'selected' : '' }}>
                                        {{ $agence->name }} ({{ $agence->pays }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-filter flex-grow-1">
                                    <i class="fas fa-search me-2"></i> Appliquer
                                </button>
                                <a href="{{ route('admin.bilan_financier.index') }}" class="btn btn-reset" title="Réinitialiser">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

            <!-- Statistiques Globales par Devise -->
            @foreach($statsGlobales as $devise => $stats)
                <div class="stats-globales-section mb-5">
                    <h2 class="section-title">
                        <i class="fas fa-globe"></i>
                        Bilan Global ({{ $devise }})
                    </h2>
                    <div class="stats-grid">
                        <!-- Montant Total (Facturation) -->
                        <div class="stat-card total-card">
                            <div class="stat-icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-value">{{ number_format($stats['montant_total'], 0, ',', ' ') }} {{ $devise }}</h3>
                                <p class="stat-label">Valeur Facturée (Période)</p>
                                <div class="stat-detail">{{ $stats['total_colis'] }} nouveaux colis</div>
                            </div>
                        </div>

                        <!-- Montant Payé (Encaissements) -->
                        <div class="stat-card paye-card">
                            <div class="stat-icon">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-value">{{ number_format($stats['montant_paye'], 0, ',', ' ') }} {{ $devise }}</h3>
                                <p class="stat-label">Encaissements Réels</p>
                                <div class="stat-detail">Somme des transactions reçues</div>
                            </div>
                        </div>

                        <!-- Montant Impayé -->
                        <div class="stat-card impaye-card">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-value">{{ number_format($stats['montant_impaye'], 0, ',', ' ') }} {{ $devise }}</h3>
                                <p class="stat-label">Reste à Recouvrer</p>
                                <div class="stat-detail">{{ $stats['non_payes'] + $stats['partiellement_payes'] }} colis avec reliquat</div>
                            </div>
                        </div>

                        <!-- Taux de Recouvrement -->
                        <div class="stat-card taux-card">
                            <div class="stat-icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-value">{{ $stats['taux_recouvrement'] }}%</h3>
                                <p class="stat-label">Taux Recouvrement</p>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $stats['taux_recouvrement'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Répartition par méthode pour cette devise -->
                    <div class="payment-methods-mini-grid mt-4">
                        <div class="analytics-card">
                            <div class="method-mini-list">
                                <div class="method-mini-item">
                                    <span class="label"><i class="fas fa-money-bill-wave"></i> Espèces:</span>
                                    <span class="value">{{ number_format($stats['montant_especes'], 0, ',', ' ') }} {{ $devise }}</span>
                                </div>
                                <div class="method-mini-item">
                                    <span class="label"><i class="fas fa-university"></i> Virement:</span>
                                    <span class="value">{{ number_format($stats['montant_virement'], 0, ',', ' ') }} {{ $devise }}</span>
                                </div>
                                <div class="method-mini-item">
                                    <span class="label"><i class="fas fa-money-check"></i> Chèque:</span>
                                    <span class="value">{{ number_format($stats['montant_cheque'], 0, ',', ' ') }} {{ $devise }}</span>
                                </div>
                                <div class="method-mini-item">
                                    <span class="label"><i class="fas fa-mobile-alt"></i> Mobile Money:</span>
                                    <span class="value">{{ number_format($stats['montant_mobile_money'], 0, ',', ' ') }} {{ $devise }}</span>
                                </div>
                                <div class="method-mini-item">
                                    <span class="label"><i class="fas fa-truck"></i> Livraison:</span>
                                    <span class="value">{{ number_format($stats['montant_livraison'], 0, ',', ' ') }} {{ $devise }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Évolution Mensuelle -->
            <div class="analytics-grid mb-5 col-12">
                <div class="analytics-card main-chart">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-line"></i> Performance Mensuelle (Facturation vs Encaissements)</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="evolutionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Bilan par Agence -->
            <div class="bilan-agences-section">
                <h2 class="section-title">
                    <i class="fas fa-building"></i>
                    Performance par Agence (Encaissements Réels)
                </h2>
                <div class="agences-grid">
                    @foreach($statsParAgence as $stat)
                        <div class="agence-card">
                            <div class="agence-header">
                                <div class="agence-info">
                                    <h3 class="agence-name">{{ $stat['agence']->name }}</h3>
                                    <p class="agence-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        {{ $stat['agence']->pays }}
                                    </p>
                                </div>
                                <div class="agence-badge">
                                    <i class="fas fa-box"></i>
                                    {{ $stat['total_colis'] }} colis
                                </div>
                            </div>

                            <div class="agence-stats-simple">
                                <div class="encaisse-agents-box">
                                    <span class="label">Total Encaissé (Toutes sources):</span>
                                    <h4 class="amount text-success">
                                        {{ number_format($stat['total_encaisse'], 0, ',', ' ') }} {{ $stat['agence']->devise ?? 'FCFA' }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            </style>

            <!-- Scripts pour les graphiques -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const ctx = document.getElementById('evolutionChart').getContext('2d');

                    const evolutionChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @json($statsGraphique['months']),
                            datasets: [
                                {
                                    label: 'Montant Total',
                                    data: @json($statsGraphique['montants_totaux']),
                                    borderColor: '#fea219',
                                    backgroundColor: 'rgba(254, 162, 25, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                },
                                {
                                    label: 'Montant Payé',
                                    data: @json($statsGraphique['montants_payes']),
                                    borderColor: '#0d8644',
                                    backgroundColor: 'rgba(13, 134, 68, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                },
                                {
                                    label: 'Montant Impayé',
                                    data: @json($statsGraphique['montants_impayes']),
                                    borderColor: '#e53e3e',
                                    backgroundColor: 'rgba(229, 62, 62, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
@endsection