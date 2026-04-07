@extends('agent.layouts.template')

@section('content')
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="bilan-financier-container">
        <!-- Header Section -->
        <div class="bilan-header">
            <div class="header-left">
                <h1 class="header-title">Bilan Financier (Côte d'Ivoire)</h1>
                <p class="header-subtitle">
                    <span class="agence-pill"><i class="fas fa-building"></i> {{ $agence->name }}</span>
                    <span class="date-pill"><i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                </p>
            </div>
            <div class="header-right">
                <a href="{{ route($route_prefix . '.historique') }}" class="btn-historique">
                    <i class="fas fa-list-ul"></i> Journal des transactions
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-card mb-5">
            <div class="filter-card-header d-flex align-items-center mb-4">
                <div class="header-icon"><i class="fas fa-filter"></i></div>
                <h5 class="mb-0 ms-3">Filtrer par période</h5>
            </div>
            <form action="{{ route($route_prefix . '.index') }}" method="GET" class="filter-form">
                <div class="row g-4 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-bold small">DATE DÉBUT</label>
                        <div class="input-group-modern">
                            <i class="far fa-calendar-minus"></i>
                            <input type="date" name="date_debut" class="modern-input" value="{{ $dateDebut }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-bold small">DATE FIN</label>
                        <div class="input-group-modern">
                            <i class="far fa-calendar-check"></i>
                            <input type="date" name="date_fin" class="modern-input" value="{{ $dateFin }}">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex gap-3">
                        <button type="submit" class="btn-filter flex-grow-1">
                            <i class="fas fa-search me-2"></i> Appliquer
                        </button>
                        <a href="{{ route($route_prefix . '.index') }}" class="btn-reset" title="Réinitialiser">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Stats Section -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="section-title">
                    <i class="fas fa-chart-pie text-primary"></i> Synthèse Financière ({{ $devise }})
                </h2>
            </div>
        </div>

        <div class="stats-grid mb-5">
            <!-- Facturation Totale -->
            <div class="stat-card volume-card">
                <div class="stat-icon-wrapper">
                    <div class="stat-icon bg-indigo"><i class="fas fa-file-invoice"></i></div>
                </div>
                <div class="stat-info">
                    <p class="stat-label">Total Volume D'Affaires</p>
                    <h3 class="stat-value">{{ number_format($statsAgence['montant_total'], 0, ',', ' ') }} <small>{{ $devise }}</small></h3>
                    <div class="stat-badge">{{ $statsAgence['total_colis'] }} colis expédiés</div>
                </div>
            </div>

            <!-- Encaissements Réels -->
            <div class="stat-card cash-card">
                <div class="stat-icon-wrapper">
                    <div class="stat-icon bg-emerald"><i class="fas fa-hand-holding-dollar"></i></div>
                </div>
                <div class="stat-info">
                    <p class="stat-label">Encaissé Réellement (Cash)</p>
                    <h3 class="stat-value text-success">{{ number_format($statsAgence['montant_paye'], 0, ',', ' ') }} <small>{{ $devise }}</small></h3>
                    <div class="stat-badge success">{{ $statsAgence['totalement_payes'] }} payés entièrement</div>
                </div>
            </div>

            <!-- Reste à Encaisser -->
            <div class="stat-card pending-card">
                <div class="stat-icon-wrapper">
                    <div class="stat-icon bg-rose"><i class="fas fa-wallet"></i></div>
                </div>
                <div class="stat-info">
                    <p class="stat-label">Reste à Recouvrer</p>
                    <h3 class="stat-value text-danger">{{ number_format($statsAgence['montant_impaye'], 0, ',', ' ') }} <small>{{ $devise }}</small></h3>
                    <div class="stat-badge danger">{{ $statsAgence['non_payes'] + $statsAgence['partiellement_payes'] }} dossiers incomplets</div>
                </div>
            </div>

            <!-- Performance -->
            <div class="stat-card performance-card">
                <div class="stat-icon-wrapper">
                    <div class="stat-icon bg-amber"><i class="fas fa-rocket"></i></div>
                </div>
                <div class="stat-info">
                    <p class="stat-label">Taux de Collecte</p>
                    <h3 class="stat-value">{{ $statsAgence['taux_recouvrement'] }}%</h3>
                    <div class="custom-progress">
                        <div class="progress-fill" style="width: {{ $statsAgence['taux_recouvrement'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="analytic-card-wrapper mb-5">
            <div class="methods-card border-0 shadow-lg">
                <div class="methods-header p-4 border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="header-icon-small"><i class="fas fa-coins text-warning"></i></div>
                        <h5 class="mb-0 ms-3 fw-bold">Détails des encaissements sur site</h5>
                    </div>
                </div>
                <div class="methods-body p-4">
                    <div class="row g-3">
                        @php
                            $methods = [
                                ['label' => 'Espèces', 'key' => 'montant_especes', 'icon' => 'fa-money-bill-wave', 'color' => '#10b981'],
                                ['label' => 'Virement', 'key' => 'montant_virement', 'icon' => 'fa-university', 'color' => '#3b82f6'],
                                ['label' => 'Mobile Money', 'key' => 'montant_mobile_money', 'icon' => 'fa-mobile-alt', 'color' => '#8b5cf6'],
                                ['label' => 'Chèque', 'key' => 'montant_cheque', 'icon' => 'fa-money-check', 'color' => '#f59e0b'],
                                ['label' => 'Livraison', 'key' => 'montant_livraison', 'icon' => 'fa-truck-loading', 'color' => '#64748b']
                            ];
                        @endphp
                        @foreach($methods as $m)
                        <div class="col-md-2-4 col-sm-6">
                            <div class="method-badge-v2">
                                <div class="m-icon-circle" style="background: {{ $m['color'] }}">
                                    <i class="fas {{ $m['icon'] }}"></i>
                                </div>
                                <div class="m-content">
                                    <span class="m-title">{{ $m['label'] }}</span>
                                    <span class="m-amount">{{ number_format($statsAgence[$m['key']], 0, ',', ' ') }} <small>{{ $devise }}</small></span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="analytics-card main-chart-box border-0 shadow-lg">
            <div class="chart-header d-flex justify-content-between align-items-center mb-4">
                <h3 class="chart-title"><i class="fas fa-chart-line me-2 text-primary"></i> Évolution des Recettes</h3>
                <span class="badge rounded-pill bg-light text-dark px-3 py-2 border">Période: {{ $dateDebut ? date('Y', strtotime($dateDebut)) : date('Y') }}</span>
            </div>
            <div class="chart-container" style="height: 380px;">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Scripts -->
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
                        label: 'Encaissements',
                        data: {!! json_encode($statsGraphique['data']) !!},
                        borderColor: '#fea219',
                        backgroundColor: gradient,
                        borderWidth: 4,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#fea219',
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: '#f1f5f9' },
                            ticks: { callback: v => v.toLocaleString() + ' {{ $devise }}' }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>

    <style>
        :root { --primary: #fea219; --secondary: #1e293b; --bg: #f8fafc; }
        .bilan-financier-container { padding: 30px; background: var(--bg); min-height: 100vh; font-family: 'Inter', sans-serif; }
        
        .bilan-header { background: white; padding: 30px; border-radius: 24px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); margin-bottom: 35px; }
        .header-title { font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 28px; color: var(--secondary); margin: 0; }
        .agence-pill { background: #eff6ff; color: #2563eb; padding: 6px 16px; border-radius: 50px; font-size: 14px; font-weight: 600; }
        .date-pill { background: #f1f5f9; color: #64748b; padding: 6px 16px; border-radius: 50px; font-size: 14px; font-weight: 600; margin-left: 10px; }
        
        .btn-historique { background: var(--secondary); color: white; padding: 12px 24px; border-radius: 12px; font-weight: 700; text-decoration: none; transition: 0.3s; }
        .btn-historique:hover { background: #000; transform: translateY(-2px); color: white; }

        .filter-card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        .input-group-modern { position: relative; display: flex; align-items: center; }
        .input-group-modern i { position: absolute; left: 15px; color: #94a3b8; }
        .modern-input { width: 100%; padding: 12px 15px 12px 45px; border: 1.5px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-weight: 600; }
        
        .btn-filter { background: var(--primary); color: white; border: none; padding: 12px; border-radius: 12px; font-weight: 700; }
        .btn-reset { width: 48px; background: #f1f5f9; color: #64748b; border: 1.5px solid #e2e8f0; border-radius: 12px; display: flex; align-items: center; justify-content: center; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px; }
        .stat-card { background: white; padding: 25px; border-radius: 24px; display: flex; align-items: center; gap: 20px; transition: 0.3s; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; }
        .bg-indigo { background: #6366f1; } .bg-emerald { background: #10b981; } .bg-rose { background: #f43f5e; } .bg-amber { background: #f59e0b; }
        
        .stat-label { color: #64748b; font-size: 13px; font-weight: 600; margin-bottom: 5px; }
        .stat-value { font-family: 'Outfit'; font-weight: 800; font-size: 24px; margin: 0; }
        
        .method-badge-v2 { display: flex; align-items: center; gap: 12px; padding: 15px; background: #f8fafc; border-radius: 16px; border: 1.5px solid #f1f5f9; transition: 0.2s; }
        .method-badge-v2:hover { border-color: var(--primary); background: white; }
        .m-icon-circle { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; }
        .m-title { font-size: 11px; font-weight: 700; color: #94a3b8; display: block; }
        .m-amount { font-size: 14px; font-weight: 800; color: var(--secondary); }

        .col-md-2-4 { flex: 0 0 20%; max-width: 20%; }
        @media (max-width: 1200px) { .col-md-2-4 { flex: 0 0 33.33%; max-width: 33.33%; } }
        @media (max-width: 768px) { .col-md-2-4 { flex: 0 0 50%; max-width: 50%; } }

        .analytics-card { background: white; padding: 30px; border-radius: 24px; }
        .custom-progress { height: 8px; background: #f1f5f9; border-radius: 10px; margin-top: 15px; }
        .progress-fill { background: var(--primary); height: 100%; border-radius: 10px; }
    </style>
@endsection