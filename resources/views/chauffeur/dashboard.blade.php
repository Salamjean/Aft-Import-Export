@extends('chauffeur.layouts.template')
@section('content')

<style>
:root {
    --primary-color: #fea219;
    --primary-light: #ffb84d;
    --primary-dark: #e69100;
    --success-color: #28a745;
    --success-light: #34ce57;
    --text-dark: #2d3748;
    --text-light: #718096;
    --bg-light: #f7fafc;
    --border-color: #e2e8f0;
}

.dashboard-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.stat-card {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    color: white;
    border: none;
}

.stat-card-success {
    background: linear-gradient(135deg, var(--success-color), var(--success-light));
    color: white;
    border: none;
}

.stat-card .stat-icon {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    padding: 12px;
}

.quick-action-btn {
    background: white;
    border: 2px solid var(--primary-color);
    color: var(--primary-color);
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    transition: all 0.3s ease;
    text-decoration: none;
    display: block;
}

.quick-action-btn:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
    text-decoration: none;
}

.quick-action-btn-success {
    background: white;
    border: 2px solid var(--success-color);
    color: var(--success-color);
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    transition: all 0.3s ease;
    text-decoration: none;
    display: block;
}

.quick-action-btn-success:hover {
    background: var(--success-color);
    color: white;
    transform: translateY(-2px);
    text-decoration: none;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.bg-status-en_cours { background: var(--primary-color); color: white; }
.bg-status-termine { background: var(--success-color); color: white; }
.bg-status-programme { background: #6c757d; color: white; }
.bg-status-annule { background: #dc3545; color: white; }

.type-badge-depot { background: #ffc107; color: #000; }
.type-badge-recuperation { background: #17a2b8; color: white; }
.type-badge-livraison { background: var(--success-color); color: white; }

.progress-bar-custom {
    background: var(--primary-color);
    border-radius: 10px;
}

.progress-bar-success {
    background: var(--success-color);
    border-radius: 10px;
}

.activity-timeline {
    position: relative;
    padding-left: 30px;
}

.activity-timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--primary-color);
}

.activity-item {
    position: relative;
    margin-bottom: 20px;
}

.activity-item::before {
    content: '';
    position: absolute;
    left: -25px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--primary-color);
    border: 2px solid white;
    box-shadow: 0 0 0 2px var(--primary-color);
}

.activity-item.success::before {
    background: var(--success-color);
    box-shadow: 0 0 0 2px var(--success-color);
}
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-card p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-2" style="color: var(--text-dark);">
                            Bonjour, {{ Auth::guard('chauffeur')->user()->name }} 👋
                        </h1>
                        <p class="mb-0" style="color: var(--text-light);">
                            Bienvenue sur votre tableau de bord chauffeur AFT Import Export
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="text-muted">
                            {{ now()->format('l d F Y') }}
                        </div>
                        <div class="badge bg-success">
                            <i class="fas fa-circle me-1"></i>
                            En service
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4">
        <!-- Total en cours -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card stat-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-0">{{ $totalEnCours }}</h3>
                        <p class="mb-0 opacity-90">Missions en cours</p>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-tasks fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terminés aujourd'hui -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card stat-card-success p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-0">{{ $totalTermines }}</h3>
                        <p class="mb-0 opacity-90">Missions terminées</p>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Programmes aujourd'hui -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-0" style="color: var(--primary-color);">{{ $totalAujourdhui }}</h3>
                        <p class="mb-0 text-muted">Aujourd'hui</p>
                    </div>
                    <div>
                        <i class="fas fa-calendar-day fa-2x" style="color: var(--primary-color);"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Répartition -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-0" style="color: var(--success-color);">{{ $livraisonsEnCours }}</h3>
                        <p class="mb-0 text-muted">Livraisons</p>
                    </div>
                    <div>
                        <i class="fas fa-truck fa-2x" style="color: var(--success-color);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deuxième ligne de statistiques -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-0" style="color: #ffc107;">{{ $depotsEnCours }}</h3>
                        <p class="mb-0 text-muted">Dépôts</p>
                    </div>
                    <div>
                        <i class="fas fa-box fa-2x" style="color: #ffc107;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-0" style="color: #17a2b8;">{{ $recuperationsEnCours }}</h3>
                        <p class="mb-0 text-muted">Récupérations</p>
                    </div>
                    <div>
                        <i class="fas fa-undo fa-2x" style="color: #17a2b8;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-0" style="color: var(--success-color);">{{ $livraisonsEnCours }}</h3>
                        <p class="mb-0 text-muted">En livraison</p>
                    </div>
                    <div>
                        <i class="fas fa-shipping-fast fa-2x" style="color: var(--success-color);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Actions rapides -->
        <div class="col-lg-4 mb-4">
            <div class="dashboard-card p-4 h-100">
                <h5 class="mb-4" style="color: var(--text-dark);">
                    <i class="fas fa-bolt me-2" style="color: var(--primary-color);"></i>
                    Actions Rapides
                </h5>
                
                <div class="row g-3">
                    <div class="col-12 mb-2">
                        <a href="{{route('chauffeur.programme')}}" class="quick-action-btn-success">
                            <i class="fas fa-list fa-2x mb-2"></i>
                            <div>Mes Missions</div>
                        </a>
                    </div>
                    <div class="col-12">
                        <a href="{{route('chauffeur.history')}}" class="quick-action-btn">
                            <i class="fas fa-history fa-2x mb-2"></i>
                            <div>Historique</div>
                        </a>
                    </div>
                </div>

                <!-- Progression du jour -->
                <div class="mt-4 pt-3 border-top">
                    <h6 class="mb-3">Progression du jour</h6>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Missions terminées</small>
                            <small>{{ $totalTermines }}/{{ $totalEnCours + $totalTermines }}</small>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 10px;">
                            <div class="progress-bar progress-bar-success" 
                                 style="width: {{ ($totalEnCours + $totalTermines) > 0 ? ($totalTermines/($totalEnCours + $totalTermines))*100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dernières missions -->
        <div class="col-lg-8 mb-4">
            <div class="dashboard-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 style="color: var(--text-dark);">
                        <i class="fas fa-clock me-2" style="color: var(--primary-color);"></i>
                        Dernières Missions
                    </h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Référence</th>
                                <th>Adresse</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programmesRecents as $programme)
                            <tr>
                                <td>
                                    <span class="badge type-badge-{{ $programme->type }}">
                                        <i class="fas {{ $programme->icon }} me-1"></i>
                                        {{ ucfirst($programme->type) }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $programme->reference ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    @if($programme->type === 'depot')
                                        {{ Str::limit($programme->adresse_depot, 30) }}
                                    @elseif($programme->type === 'recuperation')
                                        {{ Str::limit($programme->adresse_recuperation, 30) }}
                                    @else
                                        {{ Str::limit($programme->adresse_livraison, 30) }}
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge bg-status-{{ $programme->statut }}">
                                        {{ ucfirst($programme->statut) }}
                                    </span>
                                </td>
                                <td>
                                    @if($programme->type === 'depot')
                                        {{ $programme->date_depot ? \Carbon\Carbon::parse($programme->date_depot)->format('d/m H:i') : 'N/A' }}
                                    @elseif($programme->type === 'recuperation')
                                        {{ $programme->date_recuperation ? \Carbon\Carbon::parse($programme->date_recuperation)->format('d/m H:i') : 'N/A' }}
                                    @else
                                        {{ $programme->date_livraison ? \Carbon\Carbon::parse($programme->date_livraison)->format('d/m H:i') : 'N/A' }}
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <br>
                                    Aucune mission en cours
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

<!-- Modal Planning du jour -->
<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-day me-2" style="color: var(--primary-color);"></i>
                    Planning du Jour - {{ now()->format('d/m/Y') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Votre planning pour aujourd'hui sera affiché ici.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script pour les animations -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des cartes au chargement
    const cards = document.querySelectorAll('.dashboard-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Mise à jour de l'heure en temps réel
    function updateTime() {
        const now = new Date();
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        document.querySelector('.text-muted').textContent = now.toLocaleDateString('fr-FR', options);
    }

    setInterval(updateTime, 60000);
});

function showTodaySchedule() {
    const modal = new bootstrap.Modal(document.getElementById('scheduleModal'));
    modal.show();
}
</script>

@endsection