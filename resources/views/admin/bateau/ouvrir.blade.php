@extends('admin.layouts.template')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<div class="container-fluid">
    <div class="row mt-4" >
        <div class="col-12">
            <div class="card modern-card">
                <div class="card-header modern-header">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fas fa-ship"></i>
                        </div>
                        <div class="header-text">
                            <h3 class="card-title">Conteneur du Bateau</h3>
                            <p class="card-subtitle">Détails du conteneur associé - {{ $bateau->reference }}</p>
                        </div>
                    </div>
                    <a href="{{ route('bateau.index') }}" class="btn back-btn">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Informations du Bateau -->
                        <div class="col-xl-6 col-lg-12 mb-4">
                            <div class="info-card boat-card">
                                <div class="card-header">
                                    <div class="header-icon">
                                        <i class="fas fa-ship"></i>
                                    </div>
                                    <h5 class="card-title">Informations du Bateau</h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <div class="info-label">Référence</div>
                                            <div class="info-value">{{ $bateau->reference }}</div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Type</div>
                                            <div class="info-value">
                                                <span class="status-badge {{ $bateau->type_transport === 'Bateau' ? 'type-boat' : 'type-plane' }}">
                                                    <i class="fas {{ $bateau->type_transport === 'Bateau' ? 'fa-ship' : 'fa-plane' }} me-2"></i>
                                                    {{ $bateau->type_transport }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Statut</div>
                                            <div class="info-value">
                                                <span class="status-badge status-{{ $bateau->statut }}">
                                                    <i class="fas 
                                                        {{ $bateau->statut === 'depart' ? 'fa-flag' : '' }}
                                                        {{ $bateau->statut === 'en_cours' ? 'fa-spinner fa-spin' : '' }}
                                                        {{ $bateau->statut === 'arrive' ? 'fa-check' : '' }} me-2">
                                                    </i>
                                                    {{ $bateau->statut === 'depart' ? 'Départ' : '' }}
                                                    {{ $bateau->statut === 'en_cours' ? 'En cours' : '' }}
                                                    {{ $bateau->statut === 'arrive' ? 'Arrivé' : '' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Compagnie</div>
                                            <div class="info-value">{{ $bateau->compagnie ?? 'N/A' }}</div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Nom</div>
                                            <div class="info-value">{{ $bateau->nom ?? 'N/A' }}</div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Numéro</div>
                                            <div class="info-value">{{ $bateau->numero ?? 'N/A' }}</div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Date d'arrivée</div>
                                            <div class="info-value">
                                                <i class="fas fa-calendar me-2 text-muted"></i>
                                                {{ \Carbon\Carbon::parse($bateau->date_arrive)->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations du Conteneur -->
                        <div class="col-xl-6 col-lg-12 mb-4">
                            <div class="info-card container-card">
                                <div class="card-header">
                                    <div class="header-icon">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <h5 class="card-title">Informations du Conteneur</h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <div class="info-label">Nom</div>
                                            <div class="info-value">{{ $bateau->conteneur->name_conteneur }}</div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Type</div>
                                            <div class="info-value">
                                                <span class="status-badge {{ $bateau->conteneur->type_conteneur === 'Conteneur' ? 'type-container' : 'type-ballon' }}">
                                                    {{ $bateau->conteneur->type_conteneur }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Numéro</div>
                                            <div class="info-value">{{ $bateau->conteneur->numero_conteneur ?? 'N/A' }}</div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Statut</div>
                                            <div class="info-value">
                                                <span class="status-badge status-{{ $bateau->conteneur->statut }}">
                                                    <i class="fas {{ $bateau->conteneur->statut === 'ouvert' ? 'fa-unlock' : 'fa-lock' }} me-2"></i>
                                                    {{ ucfirst($bateau->conteneur->statut) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Date création</div>
                                            <div class="info-value">
                                                <i class="fas fa-clock me-2 text-muted"></i>
                                                {{ \Carbon\Carbon::parse($bateau->conteneur->created_at)->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations de l'Agence -->
                    <div class="row">
                        <div class="col-12">
                            <div class="info-card agency-card">
                                <div class="card-header">
                                    <div class="header-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <h5 class="card-title">Informations de l'Agence</h5>
                                </div>
                                <div class="card-body">
                                    <div class="agency-grid">
                                        <div class="agency-item">
                                            <div class="agency-icon">
                                                <i class="fas fa-building"></i>
                                            </div>
                                            <div class="agency-content">
                                                <div class="agency-label">Nom</div>
                                                <div class="agency-value">{{ $bateau->agence->name }}</div>
                                            </div>
                                        </div>
                                        <div class="agency-item">
                                            <div class="agency-icon">
                                                <i class="fas fa-location-dot"></i>
                                            </div>
                                            <div class="agency-content">
                                                <div class="agency-label">Adresse</div>
                                                <div class="agency-value">{{ $bateau->agence->adresse }}</div>
                                            </div>
                                        </div>
                                        <div class="agency-item">
                                            <div class="agency-icon">
                                                <i class="fas fa-globe"></i>
                                            </div>
                                            <div class="agency-content">
                                                <div class="agency-label">Pays</div>
                                                <div class="agency-value">{{ $bateau->agence->pays }}</div>
                                            </div>
                                        </div>
                                        <div class="agency-item">
                                            <div class="agency-icon">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </div>
                                            <div class="agency-content">
                                                <div class="agency-label">Devise</div>
                                                <div class="agency-value">{{ $bateau->agence->devise }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="action-section">
                                <div class="action-buttons">
                                    <a href="{{ route('bateau.index') }}" class="btn action-btn btn-back">
                                        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                                    </a>
                                    <a href="{{ route('conteneur.colis.show', $bateau->conteneur->id) }}" class="btn action-btn btn-open">
                                        <i class="fas fa-box-open me-2"></i>Ouvrir le Conteneur
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Afficher les messages flash
@if(session('success'))
    Swal.fire({
        title: 'Succès !',
        text: '{{ session('success') }}',
        icon: 'success',
        confirmButtonColor: '#0d8644',
        background: '#fff',
        customClass: {
            popup: 'modern-swal'
        }
    });
@endif

@if(session('error'))
    Swal.fire({
        title: 'Erreur !',
        text: '{{ session('error') }}',
        icon: 'error',
        confirmButtonColor: '#fda119',
        background: '#fff',
        customClass: {
            popup: 'modern-swal'
        }
    });
@endif
</script>

<style>
:root {
    --primary-color: #fda119;
    --secondary-color: #0d8644;
    --white: #ffffff;
    --light-bg: #f8f9fa;
    --text-dark: #2c3e50;
    --text-light: #6c757d;
    --border-radius: 16px;
    --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    --transition: all 0.3s ease;
}

/* Carte principale */
.modern-card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
    background: var(--white);
}

.modern-header {
    background: linear-gradient(135deg, var(--primary-color), #e8910c);
    color: var(--white);
    border: none;
    padding: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 20px;
}

.header-icon {
    width: 60px;
    height: 60px;
    background: var(--white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--secondary-color);
}

.header-text .card-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.header-text .card-subtitle {
    opacity: 0.9;
    font-size: 1rem;
    margin: 0;
}

.back-btn {
    background: rgba(255, 255, 255, 0.2);
    color: var(--white);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 600;
    transition: var(--transition);
}

.back-btn:hover {
    background: var(--white);
    color: var(--primary-color);
    transform: translateY(-2px);
}

/* Cartes d'information */
.info-card {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
    height: 100%;
    transition: var(--transition);
}

.info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
}

.info-card .card-header {
    background: linear-gradient(135deg, var(--secondary-color), #0a6b35);
    color: var(--white);
    padding: 20px 25px;
    border: none;
    display: flex;
    align-items: center;
    gap: 15px;
}

.info-card .card-header .header-icon {
    width: 45px;
    height: 45px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.info-card .card-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0;
}

.info-card .card-body {
    padding: 25px;
}

/* Grille d'information */
.info-grid {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f1f3f4;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: var(--text-dark);
    font-size: 0.9rem;
}

.info-value {
    color: var(--text-light);
    font-weight: 500;
    text-align: right;
}

/* Badges */
.status-badge {
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.type-boat {
    background: linear-gradient(135deg, #ffd700, #ffa500);
    color: #fff;
}

.type-plane {
    background: linear-gradient(135deg, #87ceeb, #4682b4);
    color: #fff;
}

.type-container {
    background: linear-gradient(135deg, #4ecdc4, #44a08d);
    color: #fff;
}

.type-ballon {
    background: linear-gradient(135deg, #ff9a9e, #fad0c4);
    color: #fff;
}

.status-depart {
    background: linear-gradient(135deg, #6c757d, #495057);
    color: #fff;
}

.status-en_cours {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: #fff;
}

.status-arrive {
    background: linear-gradient(135deg, #28a745, #1e7e34);
    color: #fff;
}

.status-ouvert {
    background: linear-gradient(135deg, #28a745, #1e7e34);
    color: #fff;
}

.status-fermer {
    background: linear-gradient(135deg, #6c757d, #495057);
    color: #fff;
}

/* Grille Agence */
.agency-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.agency-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: var(--light-bg);
    border-radius: 12px;
    transition: var(--transition);
}

.agency-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.agency-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary-color), #e8910c);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 1.2rem;
}

.agency-content {
    flex: 1;
}

.agency-label {
    font-weight: 600;
    color: var(--text-dark);
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.agency-value {
    color: var(--text-light);
    font-weight: 500;
}

/* Section Actions */
.action-section {
    background: var(--light-bg);
    border-radius: var(--border-radius);
    padding: 30px;
}

.action-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.action-btn {
    padding: 12px 30px;
    border-radius: 10px;
    font-weight: 600;
    transition: var(--transition);
    border: 2px solid transparent;
}

.btn-back {
    background: var(--white);
    color: var(--text-dark);
    border-color: #dee2e6;
}

.btn-back:hover {
    background: #6c757d;
    color: var(--white);
    transform: translateY(-2px);
}

.btn-open {
    background: linear-gradient(135deg, var(--secondary-color), #0a6b35);
    color: var(--white);
}

.btn-open:hover {
    background: linear-gradient(135deg, #0a6b35, #085c2d);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(13, 134, 68, 0.3);
    color: var(--white);
}

/* SweetAlert personnalisé */
.modern-swal {
    border-radius: var(--border-radius) !important;
}

/* Responsive */
@media (max-width: 768px) {
    .modern-header {
        flex-direction: column;
        text-align: center;
        padding: 20px;
    }
    
    .header-content {
        justify-content: center;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .info-value {
        text-align: left;
    }
    
    .agency-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .action-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection