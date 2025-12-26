@extends('ivoire.layouts.template')

@section('content')
    <div class="bilan-financier-container">
        <!-- Header -->
        <div class="bilan-header">
            <div class="header-content">
                <h1 class="header-title">Bilan Financier (Côte d'Ivoire)</h1>
                <p class="header-subtitle">Agence: <strong>{{ $agence->name }}</strong></p>
            </div>
            <div class="header-actions">
                <a href="{{ route($route_prefix . '.historique') }}" class="btn-historique">
                    <i class="fas fa-history"></i>
                    Mes Encaissements
                </a>
                <div class="date-display">
                    <i class="fas fa-calendar-alt"></i>
                    {{ \Carbon\Carbon::now()->translatedFormat('l d F Y') }}
                </div>
            </div>
        </div>

        <!-- Bilan Global de l'Agence -->
        <div class="stats-globales-section mb-5">
            <h2 class="section-title">
                <i class="fas fa-chart-line"></i>
                Bilan de l'Agence ({{ $devise }})
            </h2>
            <div class="stats-grid">
                <!-- Montant Total -->
                <div class="stat-card total-card">
                    <div class="stat-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ number_format($statsAgence['montant_total'], 0, ',', ' ') }} {{ $devise }}
                        </h3>
                        <p class="stat-label">Valeur Totale Colis</p>
                        <div class="stat-detail">{{ $statsAgence['total_colis'] }} colis enregistrés</div>
                    </div>
                </div>

                <!-- Montant Encaissé -->
                <div class="stat-card paye-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ number_format($statsAgence['montant_paye'], 0, ',', ' ') }} {{ $devise }}
                        </h3>
                        <p class="stat-label">Total Encaissé</p>
                        <div class="stat-detail">{{ $statsAgence['totalement_payes'] }} payés entièrement</div>
                    </div>
                </div>

                <!-- Reste à Recouvrer -->
                <div class="stat-card impaye-card">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ number_format($statsAgence['montant_impaye'], 0, ',', ' ') }}
                            {{ $devise }}
                        </h3>
                        <p class="stat-label">Reste à Recouvrer</p>
                        <div class="stat-detail">{{ $statsAgence['non_payes'] + $statsAgence['partiellement_payes'] }} à
                            suivre</div>
                    </div>
                </div>

                <!-- Taux de Recouvrement -->
                <div class="stat-card taux-card">
                    <div class="stat-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ $statsAgence['taux_recouvrement'] }}%</h3>
                        <p class="stat-label">Taux Recouvrement</p>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $statsAgence['taux_recouvrement'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Répartition par méthode -->
            <div class="payment-methods-mini-grid mt-4">
                <div class="analytics-card">
                    <div class="card-header text-muted mb-3">
                        <small><strong>REPARTITION DES ENCAISSEMENTS (AGENCE)</strong></small>
                    </div>
                    <div class="method-mini-list">
                        <div class="method-mini-item">
                            <span class="label">Espèces:</span>
                            <span class="value">{{ number_format($statsAgence['montant_especes'], 0, ',', ' ') }}
                                {{ $devise }}</span>
                        </div>
                        <div class="method-mini-item">
                            <span class="label">Virement:</span>
                            <span class="value">{{ number_format($statsAgence['montant_virement'], 0, ',', ' ') }}
                                {{ $devise }}</span>
                        </div>
                        <div class="method-mini-item">
                            <span class="label">Chèque:</span>
                            <span class="value">{{ number_format($statsAgence['montant_cheque'], 0, ',', ' ') }}
                                {{ $devise }}</span>
                        </div>
                        <div class="method-mini-item">
                            <span class="label">Mobile:</span>
                            <span class="value">{{ number_format($statsAgence['montant_mobile_money'], 0, ',', ' ') }}
                                {{ $devise }}</span>
                        </div>
                        <div class="method-mini-item">
                            <span class="label">Livraison:</span>
                            <span class="value">{{ number_format($statsAgence['montant_livraison'], 0, ',', ' ') }}
                                {{ $devise }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Évolution Mensuelle -->
        <div class="analytics-grid mb-5">
            <div class="analytics-card main-chart">
                <div class="card-header">
                    <h3><i class="fas fa-chart-line"></i> Évolution des Encaissements de l'Agence</h3>
                </div>
                <div class="chart-container">
                    <canvas id="evolutionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('evolutionChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($statsGraphique['labels']) !!},
                    datasets: [{
                        label: 'Encaissements ({{ $devise }})',
                        data: {!! json_encode($statsGraphique['data']) !!},
                        borderColor: '#fea219',
                        backgroundColor: 'rgba(254, 162, 25, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fea219',
                        pointBorderColor: '#fff',
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { font: { size: 11 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } }
                        }
                    }
                }
            });
        });
    </script>

    <style>
        .bilan-financier-container {
            padding: 20px;
            background: #f8fafc;
        }

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

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .total-card .stat-icon {
            background: #eff6ff;
            color: #3b82f6;
        }

        .paye-card .stat-icon {
            background: #f0fdf4;
            color: #10b981;
        }

        .impaye-card .stat-icon {
            background: #fef2f2;
            color: #ef4444;
        }

        .taux-card .stat-icon {
            background: #fff7ed;
            color: #f59e0b;
        }

        .stat-value {
            font-size: 22px;
            font-weight: 800;
            margin: 0;
            color: #1a202c;
        }

        .stat-label {
            color: #718096;
            font-size: 14px;
            font-weight: 600;
            margin: 2px 0;
        }

        .stat-detail {
            font-size: 12px;
            color: #a0aec0;
        }

        .progress-bar {
            height: 8px;
            background: #edf2f7;
            border-radius: 4px;
            margin-top: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: #fea219;
            border-radius: 4px;
        }

        /* Section Title */
        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: #fea219;
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
            padding: 15px;
            background: white;
            border-radius: 12px;
            border-bottom: 3px solid #fea219;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        .method-mini-item .label {
            font-size: 11px;
            color: #718096;
            font-weight: 700;
            text-transform: uppercase;
        }

        .method-mini-item .value {
            font-size: 15px;
            font-weight: 800;
            color: #2d3748;
        }

        /* Chart */
        .analytics-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .chart-container {
            height: 350px;
            position: relative;
        }
    </style>
@endsection