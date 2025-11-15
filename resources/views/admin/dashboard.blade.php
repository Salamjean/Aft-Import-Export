@extends('admin.layouts.template')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <h1 class="header-title">Tableau de Bord</h1>
            <p class="header-subtitle">Aperçu de votre activité logistique</p>
        </div>
        <div class="header-actions">
            <div class="date-display">
                <i class="fas fa-calendar-alt"></i>
                {{ \Carbon\Carbon::now()->translatedFormat('l d F Y') }}
            </div>
        </div>
    </div>

    <!-- Cartes Statistiques -->
    <div class="stats-grid">
        <!-- Carte Utilisateurs -->
        <div class="stat-card user-stat">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $stats['total_users'] }}</h3>
                <p class="stat-label">Utilisateurs Totaux</p>
            </div>
        </div>

        <!-- Carte Colis Validés -->
        <div class="stat-card colis-stat">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $stats['colis_valides'] }}</h3>
                <p class="stat-label">Colis Validés</p>
            </div>
        </div>

        <div class="stat-card demande-stat">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $stats['demandes_recuperation_en_attente'] }}</h3>
                <p class="stat-label">Demandes Récupération</p>
                <div class="stat-badge pending">
                    {{ $stats['demandes_recuperation_traitees'] }} traitées
                </div>
            </div>
        </div>

        <!-- Carte Conteneurs -->
        <div class="stat-card conteneur-stat">
            <div class="stat-icon">
                <i class="fas fa-shipping-fast"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $stats['conteneurs_ouverts'] }}/{{ $stats['conteneurs_ouverts'] + $stats['conteneurs_fermes'] }}</h3>
                <p class="stat-label">Conteneurs Ouverts</p>
                {{-- <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ ($stats['conteneurs_ouverts']/($stats['conteneurs_ouverts'] + $stats['conteneurs_fermes']))*100 }}%"></div>
                </div> --}}
            </div>
        </div>

        <!-- Carte Devis -->
        <div class="stat-card devis-stat">
            <div class="stat-icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value">{{ $stats['devis_en_attente'] }}</h3>
                <p class="stat-label">Devis en Attente</p>
                <div class="stat-badge pending">{{ $stats['devis_traites'] }} traités</div>
            </div>
        </div>
    </div>

    <!-- Graphiques et Analytics -->
    <div class="analytics-grid">
        <!-- Graphique Principal -->
        <div class="analytics-card main-chart">
            <div class="card-header">
                <h3>Activité des Colis</h3>
                <div class="period-selector">
                    <button class="period-btn active" data-period="monthly">Mensuel</button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <!-- Derniers Colis -->
        <div class="analytics-card recent-activity">
            <div class="card-header">
                <h3>Derniers Colis</h3>
                <a href="{{ route('colis.index') }}" class="view-all">Voir tout</a>
            </div>
            <div class="activity-list">
                @foreach($recentColis as $colis)
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">{{ $colis->reference_colis }}</div>
                        <div class="activity-desc">
                            De {{ $colis->agenceExpedition->name ?? 'N/A' }} 
                            vers {{ $colis->agenceDestination->name ?? 'N/A' }}
                        </div>
                        <div class="activity-time">{{ $colis->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="activity-status status-{{ $colis->statut }}">
                        {{ $colis->statut }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Section Inférieures -->
    <div class="bottom-grid">
        <!-- Top Agences -->
        <div class="analytics-card top-agences">
            <div class="card-header">
                <h3>Top Agences</h3>
            </div>
            <div class="agences-list">
                @foreach($topAgences as $index => $agence)
                <div class="agence-item">
                    <div class="agence-rank">{{ $index + 1 }}</div>
                    <div class="agence-info">
                        <div class="agence-name">{{ $agence->name }}</div>
                        <div class="agence-location">{{ $agence->pays }}</div>
                    </div>
                    <div class="agence-stats">
                        <span class="colis-count">{{ $agence->colis_count }} colis</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Actions Rapides -->
        <div class="analytics-card quick-actions">
            <div class="card-header">
                <h3>Actions Rapides</h3>
            </div>
            <div class="actions-grid">
                <a href="{{ route('colis.create') }}" class="action-btn primary">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nouveau Colis</span>
                </a>
                <a href="{{ route('conteneur.index') }}" class="action-btn success">
                    <i class="fas fa-shipping-fast"></i>
                    <span>Conteneurs</span>
                </a>
                <a href="{{route('admin.devis.list.confirmed')}}" class="action-btn warning">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Devis</span>
                </a>
                <a href="{{ route('admin.demande.recuperation') }}" class="action-btn secondary">
                    <i class="fas fa-truck-pickup"></i>
                    <span>Demandes Récup.</span>
                </a>
            </div>
        </div>

        <!-- Statistiques de Paiement -->
        <div class="analytics-card payment-stats">
            <div class="card-header">
                <h3>Statut des Paiements</h3>
            </div>
            <div class="payment-list">
                @php
                    $colisPayes = \App\Models\Colis::where('statut_paiement', 'totalement_paye')->count();
                    $colisPartiels = \App\Models\Colis::where('statut_paiement', 'partiellement_paye')->count();
                    $colisEnAttente = \App\Models\Colis::where('statut_paiement', 'non_paye')->count();
                @endphp
                <div class="payment-item paid">
                    <div class="payment-type">Payés</div>
                    <div class="payment-amount">{{ $colisPayes }}</div>
                </div>
                <div class="payment-item partial">
                    <div class="payment-type">Partiels</div>
                    <div class="payment-amount">{{ $colisPartiels }}</div>
                </div>
                <div class="payment-item pending">
                    <div class="payment-type">En Attente</div>
                    <div class="payment-amount">{{ $colisEnAttente }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles CSS Modernes -->
<style>
.dashboard-container {
    padding: 20px;
    background: #f8fafc;
    min-height: 100vh;
}

/* Header */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    background: white;
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
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

/* Grid des Statistiques */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-left: 5px solid;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.user-stat { border-left-color: #fea219; }
.colis-stat { border-left-color: #0d8644; }
.conteneur-stat { border-left-color: #fea219; }
.devis-stat { border-left-color: #0d8644; }

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

.user-stat .stat-icon { background: #fea219; }
.colis-stat .stat-icon { background: #0d8644; }
.conteneur-stat .stat-icon { background: #fea219; }
.devis-stat .stat-icon { background: #0d8644; }

.stat-value {
    font-size: 32px;
    font-weight: 800;
    color: #1a202c;
    margin: 0;
}

.stat-label {
    color: #718096;
    margin: 5px 0;
    font-size: 14px;
}

.stat-trend {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #0d8644;
    font-size: 12px;
    font-weight: 600;
}

.progress-bar {
    width: 100%;
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    margin-top: 8px;
}

.progress-fill {
    height: 100%;
    background: #fea219;
    border-radius: 3px;
    transition: width 0.3s ease;
}

.demande-stat { border-left-color: #8B5CF6; }
.demande-stat .stat-icon { background: #8B5CF6; }

.status-en_attente { background: #fff3cd; color: #856404; }
.status-traite { background: #d1ecf1; color: #0c5460; }
.status-annule { background: #f8d7da; color: #721c24; }

.stat-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-top: 8px;
}

.stat-badge.pending {
    background: #fff3cd;
    color: #856404;
}

/* Analytics Grid */
.analytics-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
    margin-bottom: 30px;
}

.analytics-card {
    background: white;
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.action-btn.secondary { background: #8B5CF6; }
.card-header h3 {
    margin: 0;
    color: #1a202c;
    font-weight: 700;
}

.period-selector {
    display: flex;
    background: #f7fafc;
    border-radius: 12px;
    padding: 4px;
}

.period-btn {
    padding: 8px 16px;
    border: none;
    background: none;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.period-btn.active {
    background: #0d8644;
    color: white;
}

.view-all {
    color: #fea219;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
}

/* Activity List */
.activity-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #e2e8f0;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 45px;
    height: 45px;
    background: #f7fafc;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fea219;
}

.activity-title {
    font-weight: 600;
    color: #1a202c;
}

.activity-desc {
    color: #718096;
    font-size: 13px;
    margin: 2px 0;
}

.activity-time {
    color: #a0aec0;
    font-size: 12px;
}

.activity-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-valide { background: #c6f6d5; color: #22543d; }
.status-livre { background: #bee3f8; color: #1a365d; }
.status-annule { background: #fed7d7; color: #742a2a; }

/* Bottom Grid */
.bottom-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 25px;
}

.agence-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #e2e8f0;
}

.agence-rank {
    width: 30px;
    height: 30px;
    background: #fea219;
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
}

.agence-name {
    font-weight: 600;
    color: #1a202c;
}

.agence-location {
    color: #718096;
    font-size: 12px;
}

.colis-count {
    background: #0d8644;
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

/* Quick Actions */
.actions-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px;
    border-radius: 15px;
    text-decoration: none;
    color: white;
    font-weight: 600;
    transition: transform 0.3s ease;
    text-align: center;
}

.action-btn:hover {
    transform: translateY(-3px);
    color: white;
}

.action-btn i {
    font-size: 24px;
    margin-bottom: 8px;
}

.action-btn.primary { background: #fea219; }
.action-btn.success { background: #0d8644; }
.action-btn.warning { background: #fea219; }
.action-btn.info { background: #0d8644; }

/* Payment Stats */
.payment-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #e2e8f0;
}

.payment-item:last-child {
    border-bottom: none;
}

.payment-type {
    color: #4a5568;
    font-weight: 600;
}

.payment-amount {
    font-weight: 700;
    font-size: 18px;
}

.payment-item.paid .payment-amount { color: #0d8644; }
.payment-item.partial .payment-amount { color: #fea219; }
.payment-item.pending .payment-amount { color: #e53e3e; }

/* Chart Container */
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}

/* Responsive */
@media (max-width: 1200px) {
    .analytics-grid {
        grid-template-columns: 1fr;
    }
    
    .bottom-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .bottom-grid {
        grid-template-columns: 1fr;
    }
    
    .actions-grid {
        grid-template-columns: 1fr;
    }
    
    .dashboard-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
}
</style>

<!-- Scripts pour les graphiques -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('activityChart').getContext('2d');
    
    const activityChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($monthlyStats['months']),
            datasets: [
                {
                    label: 'Colis Totaux',
                    data: @json($monthlyStats['totals']),
                    borderColor: '#fea219',
                    backgroundColor: 'rgba(254, 162, 25, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Colis Validés',
                    data: @json($monthlyStats['valides']),
                    borderColor: '#0d8644',
                    backgroundColor: 'rgba(13, 134, 68, 0.1)',
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

    // Gestion des périodes
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Ici vous pouvez ajouter la logique pour changer les données du graphique
        });
    });
});
</script>
@endsection