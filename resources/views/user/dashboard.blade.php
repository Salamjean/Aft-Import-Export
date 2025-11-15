@extends('user.layouts.template')
@section('content')

<style>
:root {
    --primary-color: #fea219;
    --primary-light: #ffb84d;
    --primary-dark: #e69100;
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

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.bg-status-valide { background: #48bb78; color: white; }
.bg-status-charge { background: #ed8936; color: white; }
.bg-status-entrepot { background: #4299e1; color: white; }
.bg-status-decharge { background: #9f7aea; color: white; }
.bg-status-livre { background: #38b2ac; color: white; }
.bg-status-annule { background: #f56565; color: white; }

.timeline-item {
    border-left: 3px solid var(--primary-color);
    padding-left: 20px;
    margin-bottom: 20px;
    position: relative;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -8px;
    top: 0;
    width: 13px;
    height: 13px;
    background: var(--primary-color);
    border-radius: 50%;
}

.progress-bar-custom {
    background: var(--primary-color);
    border-radius: 10px;
}

.chart-container {
    position: relative;
    height: 200px;
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
                            Bonjour, {{ $user->name }} {{ $user->prenom }} 👋
                        </h1>
                        <p class="mb-0" style="color: var(--text-light);">
                            Bienvenue sur votre tableau de bord AFT Import Export
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="text-muted">
                            {{ now()->format('l d F Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4">
        <!-- Colis -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card stat-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-0">{{ $totalColis }}</h3>
                        <p class="mb-0 opacity-90">Total Colis</p>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-0" style="color: var(--primary-color);">{{ $colisValides }}</h3>
                        <p class="mb-0 text-muted">Colis Validés</p>
                    </div>
                    <div>
                        <i class="fas fa-check-circle fa-2x" style="color: var(--primary-color);"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-0" style="color: var(--primary-color);">{{ $colisEnTransit }}</h3>
                        <p class="mb-0 text-muted">En Transit</p>
                    </div>
                    <div>
                        <i class="fas fa-shipping-fast fa-2x" style="color: var(--primary-color);"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-0" style="color: var(--primary-color);">{{ $colisLivre }}</h3>
                        <p class="mb-0 text-muted">Livrés</p>
                    </div>
                    <div>
                        <i class="fas fa-home fa-2x" style="color: var(--primary-color);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deuxième ligne de statistiques -->
    <div class="row mb-4">
        <!-- Devis -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-0" style="color: var(--primary-color);">{{ $totalDevis }}</h3>
                        <p class="mb-0 text-muted">Total Devis</p>
                    </div>
                    <div>
                        <i class="fas fa-file-invoice-dollar fa-2x" style="color: var(--primary-color);"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-0" style="color: var(--primary-color);">{{ $devisEnAttente }}</h3>
                        <p class="mb-0 text-muted">Devis en Attente</p>
                    </div>
                    <div>
                        <i class="fas fa-clock fa-2x" style="color: var(--primary-color);"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h2 mb-0" style="color: var(--primary-color);">{{ $devisConfirmes }}</h3>
                        <p class="mb-0 text-muted">Devis Confirmés</p>
                    </div>
                    <div>
                        <i class="fas fa-check-double fa-2x" style="color: var(--primary-color);"></i>
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
                    <div class="col-6">
                        <a href="{{ route('user.devis.create') }}" class="quick-action-btn">
                            <i class="fas fa-plus-circle fa-2x mb-2"></i>
                            <div>Nouveau Devis</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('user.colis.index') }}" class="quick-action-btn">
                            <i class="fas fa-box-open fa-2x mb-2"></i>
                            <div>Mes Colis</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('demande-recuperation.create') }}" class="quick-action-btn">
                            <i class="fas fa-list-alt fa-2x mb-2"></i>
                            <div>Demande Récup.</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('user.devis.confirmed') }}" class="quick-action-btn">
                            <i class="fas fa-file-signature fa-2x mb-2"></i>
                            <div>Devis Confirmés</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Derniers colis -->
        <div class="col-lg-8 mb-4">
            <div class="dashboard-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 style="color: var(--text-dark);">
                        <i class="fas fa-boxes me-2" style="color: var(--primary-color);"></i>
                        Derniers Colis
                    </h5>
                    <a href="{{ route('user.colis.index') }}" class="btn btn-sm" style="background: var(--primary-color); color: white;">
                        Voir tout
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">Référence</th>
                                <th class="text-center">Destination</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentColis as $colis)
                            <tr>
                                <td class="text-center">
                                    <strong>{{ $colis->reference_colis ?? 'N/A' }}</strong>
                                </td>
                                <td class="text-center">{{ $colis->agence_destination }}</td>
                                <td class="text-center">
                                    <span class="status-badge bg-status-{{ $colis->statut }}">
                                        {{ ucfirst($colis->statut) }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $colis->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                    <br>
                                    Aucun colis pour le moment
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Derniers devis et progression -->
    <div class="row">
        <!-- Derniers devis -->
        <div class="col-lg-6 mb-4">
            <div class="dashboard-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 style="color: var(--text-dark);">
                        <i class="fas fa-file-invoice me-2" style="color: var(--primary-color);"></i>
                        Derniers Devis
                    </h5>
                    <a href="{{ route('user.devis.attente') }}" class="btn btn-sm" style="background: var(--primary-color); color: white;">
                        Voir tout
                    </a>
                </div>

                <div class="list-group list-group-flush">
                    @forelse($recentDevis as $devis)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $devis->mode_transit }} - {{ $devis->agence_destination }}</h6>
                                <small class="text-muted">
                                    {{ $devis->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            <span class="badge" style="background: var(--primary-color); color:white">
                                {{ ucfirst($devis->statut) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-file-invoice-dollar fa-2x mb-2"></i>
                        <br>
                        Aucun devis pour le moment
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Progression des colis -->
        <div class="col-lg-6 mb-4">
            <div class="dashboard-card p-4 h-100">
                <h5 class="mb-4" style="color: var(--text-dark);">
                    <i class="fas fa-chart-line me-2" style="color: var(--primary-color);"></i>
                    Progression des Colis
                </h5>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Validés</span>
                        <span>{{ $colisValides }}/{{ $totalColis }}</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 10px;">
                        <div class="progress-bar progress-bar-custom" 
                             style="width: {{ $totalColis > 0 ? ($colisValides/$totalColis)*100 : 0 }}%">
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>En transit</span>
                        <span>{{ $colisEnTransit }}/{{ $totalColis }}</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 10px;">
                        <div class="progress-bar progress-bar-custom" 
                             style="width: {{ $totalColis > 0 ? ($colisEnTransit/$totalColis)*100 : 0 }}%">
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Livrés</span>
                        <span>{{ $colisLivre }}/{{ $totalColis }}</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 10px;">
                        <div class="progress-bar progress-bar-custom" 
                             style="width: {{ $totalColis > 0 ? ($colisLivre/$totalColis)*100 : 0 }}%">
                        </div>
                    </div>
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
</script>

@endsection