@extends('ivoire.layouts.template')
@section('content')
<div class="container-fluid">
    <!-- Cartes de statistiques principales -->
    <div class="row mt-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-hover">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Colis Totaux
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalColis ?? '0' }}</div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-arrow-up text-success"></i>
                                Gestion des envois
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon-circle bg-primary">
                                <i class="fas fa-box text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-hover">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Colis Livrés
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $colisLivre ?? '0' }}</div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-check-circle text-success"></i>
                                Mission accomplie
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon-circle bg-success">
                                <i class="fas fa-truck text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-hover">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Transit
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $colisEnTransit ?? '0' }}</div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-shipping-fast text-warning"></i>
                                En cours de route
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon-circle bg-warning">
                                <i class="fas fa-ship text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-hover">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Conteneurs Arrivés
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $conteneursActifs ?? '0' }}</div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-container-storage text-info"></i>
                                En opération
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon-circle bg-info">
                                <i class="fas fa-cubes text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et contenu principal -->
    <div class="row">
        <!-- Graphique de progression -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4 modern-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Progression des Livraisons</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" 
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="deliveryProgressChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques de statut -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4 modern-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition des Statuts</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Livrés
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> En Transit
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> En Attente
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides et liste récente -->
    <div class="row">
        <!-- Actions rapides -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4 modern-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions Rapides</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('ivoire.scan.decharge') }}" class="btn btn-info btn-block btn-action">
                                <i class="fas fa-truck-loading me-2"></i>
                                Décharger Colis
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('ivoire.scan.livrer') }}" class="btn btn-success btn-block btn-action">
                                <i class="fas fa-truck me-2"></i>
                                Livrer Colis
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colis récents -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4 modern-card">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Engins Récents</h6>
                    <a href="{{ route('agent.cote.bateau.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">Type d'engins</th>
                                    <th class="text-center">Conteneur</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Date de départ</th>
                                    <th class="text-center">Date d'arrivée</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentColis as $colis)
                                <tr>
                                    <td class="text-center">
                                        <strong>{{ $colis->type_transport }}</strong>
                                    </td>
                                    <td class="text-center">{{ $colis->conteneur->name_conteneur ?? 'Non defini' }}</td>
                                    <td class="text-center">
                                        <span class="badge status-{{ $colis->statut }}">
                                            @if($colis->statut == 'en_cours')
                                                En cours
                                            @elseif($colis->statut == 'arrive')
                                                Arrivé
                                            @else
                                                {{ $colis->statut }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-center">{{ $colis->created_at->format('Y-d-m') }}</td>
                                    <td class="text-center">{{ $colis->date_arrive}}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        Aucun colis récent
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles CSS -->
<style>
:root {
    --primary-color: #fea219;
    --primary-dark: #e8910c;
    --success-color: #208938;
    --white: #ffffff;
    --light-bg: #f8f9fa;
}

.modern-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.modern-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(254, 162, 25, 0.15);
}

.stat-card {
    border-radius: 15px;
    background: linear-gradient(135deg, var(--white), #f8f9fa);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(254, 162, 25, 0.2);
}

.icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.bg-primary { background-color: var(--primary-color) !important; }
.bg-success { background-color: var(--success-color) !important; }
.bg-warning { background-color: #ffc107 !important; }
.bg-info { background-color: #17a2b8 !important; }

.text-primary { color: var(--primary-color) !important; }
.text-success { color: var(--success-color) !important; }

.btn-action {
    border-radius: 10px;
    padding: 12px 15px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.bateau-card {
    border: 1px solid #e3f2fd;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.bateau-card:hover {
    border-color: var(--primary-color);
    box-shadow: 0 4px 12px rgba(254, 162, 25, 0.15);
}

.status-en_cours { background-color: #d1edff; color: #0c63e4; }
.status-arrive { background-color: #d1f7e4; color: #0d8b5a; }

.shadow-hover {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: box-shadow 0.3s ease;
}

.shadow-hover:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.table-hover tbody tr:hover {
    background-color: rgba(254, 162, 25, 0.05);
}

.chart-area, .chart-pie {
    position: relative;
    height: 300px;
}
</style>

<!-- Scripts JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer les données passées depuis le contrôleur avec des valeurs par défaut
    const deliveryData = <?php echo json_encode($deliveryData ?? [12, 19, 15, 25, 22, 18, 24]); ?>;
    const transitData = <?php echo json_encode($transitData ?? [8, 12, 10, 15, 18, 14, 16]); ?>;
    const chartLabels = <?php echo json_encode($labels ?? ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']); ?>;
    const pieData = <?php echo json_encode($pieData ?? [65, 25, 10]); ?>;

    console.log('Données chargées:', { deliveryData, transitData, chartLabels, pieData });

    // Le reste du code JavaScript reste identique...
    const progressCtx = document.getElementById('deliveryProgressChart').getContext('2d');
    const progressChart = new Chart(progressCtx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Colis Livrés',
                data: deliveryData,
                borderColor: '#208938',
                backgroundColor: 'rgba(32, 137, 56, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Colis en Transit',
                data: transitData,
                borderColor: '#fea219',
                backgroundColor: 'rgba(254, 162, 25, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.parsed.y} colis`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    title: {
                        display: true,
                        text: 'Nombre de colis'
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

    // Graphique circulaire des statuts
    const pieCtx = document.getElementById('statusPieChart').getContext('2d');
    const pieChart = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Livrés', 'En Transit', 'Validés'],
            datasets: [{
                data: pieData,
                backgroundColor: ['#208938', '#fea219', '#17a2b8'],
                hoverBackgroundColor: ['#1a6f2b', '#e8910c', '#138496'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} colis (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Mettre à jour la légende du graphique circulaire
    const pieLegend = document.querySelector('.chart-pie + .text-center');
    if (pieLegend) {
        pieLegend.innerHTML = `
            <span class="mr-2">
                <i class="fas fa-circle text-success"></i> Livrés (${pieData[0]})
            </span>
            <span class="mr-2">
                <i class="fas fa-circle text-primary"></i> En Transit (${pieData[1]})
            </span>
            <span class="mr-2">
                <i class="fas fa-circle text-info"></i> Validés (${pieData[2]})
            </span>
        `;
    }
});
</script>

<!-- Inclure SweetAlert2 pour les notifications -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection