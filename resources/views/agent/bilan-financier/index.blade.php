@extends('agent.layouts.template')

@section('content')
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="bilan-financier-container">
        <!-- Header Section -->
        <div class="bilan-header">
            <div class="header-left">
                <h1 class="header-title">Mon Bilan Financier</h1>
                <p class="header-subtitle">
                    <span class="agence-pill"><i class="fas fa-building"></i> {{ $agence->name }}</span>
                    <span class="date-pill"><i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                </p>
            </div>
            <div class="header-right">
                <a href="{{ route($route_prefix . '.historique') }}" class="btn-historique">
                    <i class="fas fa-list-ul"></i> Historique des encaissements
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-card mb-5">
            <div class="filter-card-header d-flex align-items-center mb-4">
                <div class="header-icon"><i class="fas fa-filter"></i></div>
                <h5 class="mb-0 ms-3">Filtrer par période</h5>
            </div>
            <form action="{{ route($route_prefix) }}" method="GET" class="filter-form">
                <div class="row g-4 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Date Début</label>
                        <div class="input-group-modern">
                            <i class="far fa-calendar-minus"></i>
                            <input type="date" name="date_debut" class="modern-input" value="{{ $dateDebut }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date Fin</label>
                        <div class="input-group-modern">
                            <i class="far fa-calendar-check"></i>
                            <input type="date" name="date_fin" class="modern-input" value="{{ $dateFin }}">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex gap-3">
                        <button type="submit" class="btn-filter flex-grow-1">
                            <i class="fas fa-search me-2"></i> Appliquer
                        </button>
                        <a href="{{ route($route_prefix) }}" class="btn-reset" title="Réinitialiser">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Stats Section: Volume vs Real Cash -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="section-title">
                    <i class="fas fa-chart-pie"></i> Vue d'ensemble de l'Agence ({{ $devise }})
                </h2>
            </div>
        </div>

        <div class="stats-grid mb-5">
            <!-- Facturation Totale (Volume) -->
            <div class="stat-card volume-card">
                <div class="stat-icon-wrapper">
                    <div class="stat-icon"><i class="fas fa-file-invoice"></i></div>
                </div>
                <div class="stat-info">
                    <p class="stat-label">Total Facturé (Colis)</p>
                    <h3 class="stat-value">{{ number_format($statsAgence['montant_total'], 0, ',', ' ') }} <small>{{ $devise }}</small></h3>
                    <div class="stat-badge">{{ $statsAgence['total_colis'] }} colis expédiés</div>
                </div>
            </div>

            <!-- Encaissements Réels (Cash) -->
            <div class="stat-card cash-card">
                <div class="stat-icon-wrapper">
                    <div class="stat-icon"><i class="fas fa-hand-holding-dollar"></i></div>
                </div>
                <div class="stat-info">
                    <p class="stat-label">Total Réellement Encaissé</p>
                    <h3 class="stat-value text-success">{{ number_format($statsAgence['montant_paye'], 0, ',', ' ') }} <small>{{ $devise }}</small></h3>
                    <div class="stat-badge success">{{ $statsAgence['totalement_payes'] }} payés entièrement</div>
                </div>
            </div>

            <!-- Reste à Encaisser -->
            <div class="stat-card pending-card">
                <div class="stat-icon-wrapper">
                    <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                </div>
                <div class="stat-info">
                    <p class="stat-label">Reste à Encaisser</p>
                    <h3 class="stat-value text-danger">{{ number_format($statsAgence['montant_impaye'], 0, ',', ' ') }} <small>{{ $devise }}</small></h3>
                    <div class="stat-badge danger">{{ $statsAgence['non_payes'] + $statsAgence['partiellement_payes'] }} en attente</div>
                </div>
            </div>

            <!-- Performance -->
            <div class="stat-card performance-card">
                <div class="stat-icon-wrapper">
                    <div class="stat-icon"><i class="fas fa-rocket"></i></div>
                </div>
                <div class="stat-info">
                    <p class="stat-label">Taux de Recouvrement</p>
                    <h3 class="stat-value">{{ $statsAgence['taux_recouvrement'] }}%</h3>
                    <div class="custom-progress">
                        <div class="progress-fill" style="width: {{ $statsAgence['taux_recouvrement'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods Distribution -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="methods-card">
                    <div class="methods-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon-small"><i class="fas fa-money-check-alt"></i></div>
                            <h5 class="mb-0 ms-3">Répartition des encaissements par méthode</h5>
                        </div>
                    </div>
                    <div class="methods-body">
                        <div class="row g-4">
                            <div class="col-md">
                                <div class="method-box">
                                    <div class="m-icon especes"><i class="fas fa-money-bill-wave"></i></div>
                                    <div class="m-data">
                                        <span class="m-label">Espèces</span>
                                        <span class="m-value">{{ number_format($statsAgence['montant_especes'], 0, ',', ' ') }} <small>{{ $devise }}</small></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="method-box">
                                    <div class="m-icon virement"><i class="fas fa-university"></i></div>
                                    <div class="m-data">
                                        <span class="m-label">Virement</span>
                                        <span class="m-value">{{ number_format($statsAgence['montant_virement'], 0, ',', ' ') }} <small>{{ $devise }}</small></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="method-box">
                                    <div class="m-icon mobile"><i class="fas fa-mobile-alt"></i></div>
                                    <div class="m-data">
                                        <span class="m-label">Mobile Money</span>
                                        <span class="m-value">{{ number_format($statsAgence['montant_mobile_money'], 0, ',', ' ') }} <small>{{ $devise }}</small></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="method-box">
                                    <div class="m-icon cheque"><i class="fas fa-money-check"></i></div>
                                    <div class="m-data">
                                        <span class="m-label">Chèque</span>
                                        <span class="m-value">{{ number_format($statsAgence['montant_cheque'], 0, ',', ' ') }} <small>{{ $devise }}</small></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="method-box">
                                    <div class="m-icon livraison"><i class="fas fa-truck-loading"></i></div>
                                    <div class="m-data">
                                        <span class="m-label">Livraison</span>
                                        <span class="m-value">{{ number_format($statsAgence['montant_livraison'], 0, ',', ' ') }} <small>{{ $devise }}</small></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="analytics-card main-chart mb-5">
            <div class="chart-header d-flex justify-content-between align-items-center mb-4">
                <h3 class="chart-title"><i class="fas fa-chart-line-up me-2"></i> Évolution des Encaissements</h3>
                <span class="year-badge">Année: {{ $dateDebut ? date('Y', strtotime($dateDebut)) : date('Y') }}</span>
            </div>
            <div class="chart-container">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Scripts pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('evolutionChart').getContext('2d');
            
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(254, 162, 25, 0.4)');
            gradient.addColorStop(1, 'rgba(254, 162, 25, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($statsGraphique['labels']) !!},
                    datasets: [{
                        label: 'Encaissements Mensuels',
                        data: {!! json_encode($statsGraphique['data']) !!},
                        borderColor: '#fea219',
                        backgroundColor: gradient,
                        borderWidth: 4,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#fea219',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleFont: { size: 14, family: 'Outfit' },
                            bodyFont: { size: 13, family: 'Inter' },
                            padding: 15,
                            cornerRadius: 10,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                            ticks: { 
                                font: { size: 12, family: 'Inter', weight: '500' },
                                color: '#64748b',
                                callback: function(value) { return value.toLocaleString(); }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { 
                                font: { size: 12, family: 'Inter', weight: '600' },
                                color: '#475569'
                            }
                        }
                    }
                }
            });
        });
    </script>

    <style>
        :root {
            --primary: #fea219;
            --primary-light: #fff7ed;
            --secondary: #1e293b;
            --success: #10b981;
            --danger: #ef4444;
            --bg: #f8fafc;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .bilan-financier-container {
            padding: 30px;
            background: var(--bg);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }

        /* Bilan Header */
        .bilan-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
            background: white;
            padding: 30px;
            border-radius: 24px;
            box-shadow: var(--card-shadow);
        }

        .header-title {
            font-family: 'Outfit', sans-serif;
            font-size: 32px;
            font-weight: 800;
            color: var(--secondary);
            margin: 0;
            letter-spacing: -0.02em;
        }

        .agence-pill, .date-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 10px;
            margin-right: 10px;
        }

        .agence-pill { background: #eff6ff; color: #2563eb; }
        .date-pill { background: #f1f5f9; color: #64748b; }

        .btn-historique {
            background: var(--secondary);
            color: white;
            padding: 14px 24px;
            border-radius: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-historique:hover {
            background: #0f172a;
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
        }

        /* Filter Card */
        .filter-card {
            background: white;
            padding: 25px;
            border-radius: 22px;
            box-shadow: var(--card-shadow);
            border: 1px solid #e2e8f0;
        }

        .header-icon {
            width: 42px;
            height: 42px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .input-group-modern {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-modern i {
            position: absolute;
            left: 15px;
            color: #94a3b8;
        }

        .modern-input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            font-weight: 600;
            color: #334155;
            transition: all 0.2s ease;
        }

        .modern-input:focus {
            border-color: var(--primary);
            background: white;
            outline: none;
            box-shadow: 0 0 0 4px rgba(254, 162, 25, 0.1);
        }

        .btn-filter {
            background: var(--primary);
            color: white;
            border: none;
            padding: 13px;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .btn-filter:hover { background: #e89215; transform: translateY(-2px); }

        .btn-reset {
            width: 50px;
            background: #f1f5f9;
            color: #64748b;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-reset:hover { background: #e2e8f0; color: var(--secondary); transform: rotate(90deg); }

        /* Stats Section */
        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: var(--secondary);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i { color: var(--primary); }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .stat-card {
            background: white;
            padding: 28px;
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            gap: 22px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .stat-card:hover { transform: translateY(-8px); border-color: var(--primary-light); }

        .stat-icon-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #f8fafc;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .volume-card .stat-icon { background: linear-gradient(135deg, #6366f1, #4f46e5); }
        .cash-card .stat-icon { background: linear-gradient(135deg, #10b981, #059669); }
        .pending-card .stat-icon { background: linear-gradient(135deg, #f43f5e, #e11d48); }
        .performance-card .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }

        .stat-label { color: #64748b; font-size: 14px; font-weight: 600; margin-bottom: 5px; }
        .stat-value { font-family: 'Outfit', sans-serif; font-size: 28px; font-weight: 800; margin: 0; color: var(--secondary); line-height: 1.1; }
        .stat-value small { font-size: 14px; font-weight: 700; color: #94a3b8; }
        .stat-badge { font-size: 12px; font-weight: 700; color: #64748b; margin-top: 8px; }
        .stat-badge.success { color: var(--success); }
        .stat-badge.danger { color: var(--danger); }

        .custom-progress { height: 10px; background: #f1f5f9; border-radius: 10px; margin-top: 15px; overflow: hidden; }
        .progress-fill { height: 100%; background: var(--primary); border-radius: 10px; transition: width 1s ease; }

        /* Methods Card */
        .methods-card { background: white; border-radius: 24px; box-shadow: var(--card-shadow); padding: 30px; }
        .header-icon-small { width: 34px; height: 34px; background: #f1f5f9; color: var(--secondary); border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        
        .method-box {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 16px;
            border: 1.5px solid #edf2f7;
            transition: all 0.2s ease;
        }

        .method-box:hover { border-color: var(--primary); background: white; transform: scale(1.03); }

        .m-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: white; flex-shrink: 0; }
        .m-icon.especes { background: #10b981; }
        .m-icon.virement { background: #3b82f6; }
        .m-icon.mobile { background: #8b5cf6; }
        .m-icon.cheque { background: #f59e0b; }
        .m-icon.livraison { background: #64748b; }

        .m-data { display: flex; flex-direction: column; }
        .m-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
        .m-value { font-size: 15px; font-weight: 800; color: var(--secondary); }

        /* Chart Card */
        .analytics-card { background: white; padding: 35px; border-radius: 28px; box-shadow: var(--card-shadow); }
        .chart-title { font-family: 'Outfit', sans-serif; font-size: 22px; font-weight: 800; color: var(--secondary); }
        .year-badge { background: #f1f5f9; padding: 5px 15px; border-radius: 50px; font-size: 13px; font-weight: 700; color: #475569; }
        .chart-container { height: 400px; margin-top: 20px; }

        @media (max-width: 991px) {
            .bilan-header { flex-direction: column; gap: 20px; text-align: center; }
            .header-left { display: flex; flex-direction: column; align-items: center; }
        }
    </style>
@endsection
>
@endsection