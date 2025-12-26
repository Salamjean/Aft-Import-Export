@extends('admin.layouts.template')

@section('content')
    <div class="bilan-financier-container">
        <!-- Header -->
        <div class="bilan-header">
            <div class="header-content">
                <h1 class="header-title">Bilan Financier</h1>
                <p class="header-subtitle">Vue d'ensemble des encaissements par agence</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.bilan_financier.historique') }}" class="btn-historique">
                    <i class="fas fa-history"></i>
                    Historique des Paiements
                </a>
                <div class="date-display">
                    <i class="fas fa-calendar-alt"></i>
                    {{ \Carbon\Carbon::now()->translatedFormat('l d F Y') }}
                </div>
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
                        <!-- Montant Total -->
                        <div class="stat-card total-card">
                            <div class="stat-icon">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-value">{{ number_format($stats['montant_total'], 0, ',', ' ') }} {{ $devise }}</h3>
                                <p class="stat-label">Valeur Totale</p>
                                <div class="stat-detail">{{ $stats['total_colis'] }} colis</div>
                            </div>
                        </div>

                        <!-- Montant Payé -->
                        <div class="stat-card paye-card">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-value">{{ number_format($stats['montant_paye'], 0, ',', ' ') }} {{ $devise }}</h3>
                                <p class="stat-label">Montant Encaissé</p>
                                <div class="stat-detail">{{ $stats['totalement_payes'] }} payés entièrement</div>
                            </div>
                        </div>

                        <!-- Montant Impayé -->
                        <div class="stat-card impaye-card">
                            <div class="stat-icon">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-value">{{ number_format($stats['montant_impaye'], 0, ',', ' ') }} {{ $devise }}</h3>
                                <p class="stat-label">Reste à Recouvrer</p>
                                <div class="stat-detail">{{ $stats['non_payes'] + $stats['partiellement_payes'] }} à suivre</div>
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
                                    <span class="label">Espèces:</span>
                                    <span class="value">{{ number_format($stats['montant_especes'], 0, ',', ' ') }} {{ $devise }}</span>
                                </div>
                                <div class="method-mini-item">
                                    <span class="label">Virement:</span>
                                    <span class="value">{{ number_format($stats['montant_virement'], 0, ',', ' ') }} {{ $devise }}</span>
                                </div>
                                <div class="method-mini-item">
                                    <span class="label">Chèque:</span>
                                    <span class="value">{{ number_format($stats['montant_cheque'], 0, ',', ' ') }} {{ $devise }}</span>
                                </div>
                                <div class="method-mini-item">
                                    <span class="label">Mobile:</span>
                                    <span class="value">{{ number_format($stats['montant_mobile_money'], 0, ',', ' ') }} {{ $devise }}</span>
                                </div>
                                <div class="method-mini-item">
                                    <span class="label">Livraison:</span>
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
                        <h3><i class="fas fa-chart-line"></i> Évolution Mensuelle (Global)</h3>
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
                    Encaissements par Agence (Agents uniquement)
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
                                    <span class="label">Total Encaissé par les Agents:</span>
                                    <h4 class="amount text-success">
                                        {{ number_format($stat['total_encaisse_agents'], 0, ',', ' ') }} {{ $stat['agence']->devise ?? 'FCFA' }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Styles CSS -->
        <style>
            .bilan-financier-container {
                padding: 20px;
                background: #f8fafc;
                min-height: 100vh;
            }

            /* Header */
            .bilan-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 30px;
                background: white;
                padding: 25px;
                border-radius: 20px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            }

            .header-title {
                font-size: 28px;
                font-weight: 700;
                color: #1a202c;
                margin: 0;
            }

            .header-subtitle {
                color: #718096;
                margin: 5px 0 0 0;
                font-size: 16px;
            }

            .date-display {
            background: #fea219;
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-historique {
            background: white;
            color: #1a202c;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .btn-historique:hover {
            background: #f7fafc;
            border-color: #fea219;
            color: #fea219;
            text-decoration: none;
        }

        /* Agence Stats Simple */
        .agence-stats-simple {
            padding: 20px 0;
        }

        .encaisse-agents-box {
            background: #f0fff4;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            border: 1px solid #c6f6d5;
        }

        .encaisse-agents-box .label {
            display: block;
            color: #2f855a;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .encaisse-agents-box .amount {
            font-size: 24px;
            font-weight: 800;
            margin: 0;
        }

        /* Section Titles */
            .section-title {
                font-size: 22px;
                font-weight: 700;
                color: #1a202c;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .section-title i {
                color: #fea219;
            }

            /* Stats Globales */
            .stats-globales-section {
                margin-bottom: 30px;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 25px;
            }

            .stat-card {
                background: white;
                padding: 25px;
                border-radius: 20px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                display: flex;
                align-items: center;
                gap: 20px;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                border-left: 5px solid;
            }

            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            }

            .total-card {
                border-left-color: #fea219;
            }

            .paye-card {
                border-left-color: #0d8644;
            }

            .impaye-card {
                border-left-color: #e53e3e;
            }

            .taux-card {
                border-left-color: #3b82f6;
            }

            .stat-icon {
                width: 70px;
                height: 70px;
                border-radius: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 28px;
                color: white;
            }

            .total-card .stat-icon {
                background: #fea219;
            }

            .paye-card .stat-icon {
                background: #0d8644;
            }

            .impaye-card .stat-icon {
                background: #e53e3e;
            }

            .taux-card .stat-icon {
                background: #3b82f6;
            }

            .stat-value {
                font-size: 24px;
                font-weight: 800;
                color: #1a202c;
                margin: 0;
            }

            .stat-label {
                color: #718096;
                margin: 5px 0;
                font-size: 14px;
            }

            .stat-detail {
                color: #a0aec0;
                font-size: 12px;
                margin-top: 5px;
            }

            .progress-bar {
                width: 100%;
                height: 8px;
                background: #e2e8f0;
                border-radius: 4px;
                margin-top: 10px;
                overflow: hidden;
            }

            .progress-fill {
                height: 100%;
                background: linear-gradient(90deg, #0d8644, #fea219);
                border-radius: 4px;
                transition: width 0.3s ease;
            }

            /* Analytics Grid */
            .payment-methods-section {
                margin-bottom: 30px;
            }

            .analytics-grid {
                display: grid;
                grid-template-columns: 12fr 1fr;
                gap: 25px;
            }

            .analytics-card {
                background: white;
                border-radius: 20px;
                padding: 25px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            }

            .card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            }

            .card-header h3 {
                margin: 0;
                color: #1a202c;
                font-weight: 700;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .card-header i {
                color: #fea219;
            }

            .chart-container {
                position: relative;
                height: 300px;
                width: 100%;
            }

            /* Payment Methods */
            .payment-list {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .payment-method-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 15px;
                background: #f7fafc;
                border-radius: 12px;
                transition: background 0.3s ease;
            }

            .payment-method-item:hover {
                background: #edf2f7;
            }

            .method-info {
                display: flex;
                align-items: center;
                gap: 12px;
                color: #4a5568;
                font-weight: 600;
            }

            .method-info i {
                width: 30px;
                height: 30px;
                background: #fff;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #fea219;
            }

            .method-amount {
                font-weight: 700;
                color: #0d8644;
                font-size: 14px;
            }

            /* Mini Methods Grid */
            .method-mini-list {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 15px;
            }

            .method-mini-item {
                display: flex;
                flex-direction: column;
                gap: 5px;
                padding: 10px;
                background: #f8fafc;
                border-radius: 8px;
                border-bottom: 3px solid #fea219;
            }

            .method-mini-item .label {
                font-size: 12px;
                color: #718096;
                font-weight: 600;
                text-transform: uppercase;
            }

            .method-mini-item .value {
                font-size: 14px;
                font-weight: 700;
                color: #0d8644;
            }

            /* Bilan par Agence */
            .bilan-agences-section {
                margin-bottom: 30px;
            }

            .agences-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
                gap: 25px;
            }

            .agence-card {
                background: white;
                border-radius: 20px;
                padding: 25px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                border-top: 4px solid #fea219;
            }

            .agence-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            }

            .agence-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 2px solid #f7fafc;
            }

            .agence-name {
                font-size: 20px;
                font-weight: 700;
                color: #1a202c;
                margin: 0 0 5px 0;
            }

            .agence-location {
                color: #718096;
                font-size: 14px;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .agence-badge {
                background: #fea219;
                color: white;
                padding: 8px 15px;
                border-radius: 20px;
                font-size: 13px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .agence-stats {
                margin-bottom: 20px;
            }

            .agence-stat-row {
                display: flex;
                justify-content: space-between;
                padding: 10px 0;
                border-bottom: 1px solid #f7fafc;
            }

            .agence-stat-row:last-child {
                border-bottom: none;
            }

            .agence-stat-row .stat-label {
                color: #718096;
                font-size: 14px;
            }

            .agence-stat-row .stat-value {
                font-weight: 700;
                font-size: 14px;
            }

            .agence-stat-row .stat-value.total {
                color: #fea219;
            }

            .agence-stat-row .stat-value.paye {
                color: #0d8644;
            }

            .agence-stat-row .stat-value.impaye {
                color: #e53e3e;
            }

            .agence-payment-status {
                display: flex;
                justify-content: space-around;
                margin-bottom: 20px;
                padding: 15px 0;
                background: #f7fafc;
                border-radius: 12px;
            }

            .payment-status-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 5px;
            }

            .status-badge {
                width: 45px;
                height: 45px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 16px;
                color: white;
            }

            .status-badge.success {
                background: #0d8644;
            }

            .status-badge.warning {
                background: #fea219;
            }

            .status-badge.danger {
                background: #e53e3e;
            }

            .status-label {
                font-size: 12px;
                color: #718096;
            }

            .agence-footer {
                padding-top: 15px;
                border-top: 2px solid #f7fafc;
            }

            .taux-container {
                display: flex;
                justify-content: space-between;
                margin-bottom: 8px;
            }

            .taux-label {
                color: #718096;
                font-size: 14px;
            }

            .taux-value {
                font-weight: 700;
                color: #3b82f6;
                font-size: 16px;
            }

            .encaisse-row {
                background: #f0fff4;
                padding: 12px 10px !important;
                border-radius: 8px;
                margin: 5px 0;
                border-bottom: none !important;
            }

            /* Responsive */
            @media (max-width: 1200px) {
                .analytics-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 768px) {
                .stats-grid {
                    grid-template-columns: 1fr;
                }

                .agences-grid {
                    grid-template-columns: 1fr;
                }

                .bilan-header {
                    flex-direction: column;
                    gap: 15px;
                    text-align: center;
                }
            }
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