@extends('ivoire.layouts.template')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card modern-card">
                <!-- En-tête avec dégradé orange -->
                <div class="card-header modern-header">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="header-text">
                            <h1 class="card-title">Modifier la Livraison</h1>
                            <p class="card-subtitle">Modifiez les informations de la livraison existante</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="livraisonForm" action="{{ route('livraison.update', $livraison->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Section Informations du chauffeur -->
                        <div class="info-section mb-5">
                            <div class="section-header">
                                <i class="fas fa-id-card"></i>
                                <h3>Informations du Chauffeur</h3>
                            </div>
                            <div class="section-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-group modern-form-group">
                                            <label for="chauffeur_id" class="form-label required">
                                                <i class="fas fa-user-tie me-2"></i>Chauffeur
                                            </label>
                                            <select class="modern-select" id="chauffeur_id" name="chauffeur_id" required>
                                                <option value="">Sélectionnez un chauffeur</option>
                                                @foreach($chauffeurs as $chauffeur)
                                                    <option value="{{ $chauffeur->id }}" 
                                                        {{ $livraison->chauffeur_id == $chauffeur->id ? 'selected' : '' }}>
                                                        {{ $chauffeur->name }} {{ $chauffeur->prenom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group modern-form-group">
                                            <label for="date_livraison" class="form-label">
                                                <i class="fas fa-calendar-alt me-2"></i>Date de livraison prévue
                                            </label>
                                            <input type="date" class="modern-input" id="date_livraison" name="date_livraison" 
                                                   value="{{ $livraison->date_livraison ? $livraison->date_livraison->format('Y-m-d') : '' }}" 
                                                   min="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Livraison -->
                        <div class="info-section">
                            <div class="section-header">
                                <i class="fas fa-list-ul"></i>
                                <h3>Livraison à Modifier</h3>
                                <span class="badge bg-info text-center">Livraison #{{ $livraison->id }}</span>
                            </div>

                            <div id="livraisons-container">
                                <!-- Livraison unique à modifier -->
                                <div class="livraison-item modern-card">
                                    <div class="livraison-header">
                                        <div class="livraison-title">
                                            <div class="livraison-number">
                                                <span class="number-badge">1</span>
                                            </div>
                                            <div class="livraison-info">
                                                <h4>Livraison Principale</h4>
                                                <p>Modification des informations de la livraison</p>
                                            </div>
                                        </div>
                                        <div class="livraison-status">
                                            <span class="badge 
                                                @if($livraison->statut == 'en_cours') bg-warning
                                                @elseif($livraison->statut == 'termine') bg-success
                                                @elseif($livraison->statut == 'annule') bg-danger
                                                @else bg-secondary @endif">
                                                {{ ucfirst($livraison->statut) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="livraison-body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Nature de l'objet</label>
                                                    <input type="text" class="modern-input" name="nature_objet" 
                                                           value="{{ $livraison->nature_objet }}"
                                                           placeholder="Ex: Colis, Documents, etc." required>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Quantité</label>
                                                    <input type="number" class="modern-input" name="quantite" 
                                                           value="{{ $livraison->quantite }}"
                                                           min="1" required>
                                                    <div class="input-info">Articles à livrer</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Adresse de livraison</label>
                                                    <input type="text" class="modern-input" name="adresse_livraison" 
                                                           value="{{ $livraison->adresse_livraison }}"
                                                           placeholder="Adresse complète de livraison" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-2">
                                            <div class="col-md-3">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Nom du destinataire</label>
                                                    <input type="text" class="modern-input" name="nom_concerne" 
                                                           value="{{ $livraison->nom_concerne }}"
                                                           placeholder="Nom du destinataire" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Prénom du destinataire</label>
                                                    <input type="text" class="modern-input" name="prenom_concerne" 
                                                           value="{{ $livraison->prenom_concerne }}"
                                                           placeholder="Prénom du destinataire" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Contact</label>
                                                    <input type="text" class="modern-input" name="contact" 
                                                           value="{{ $livraison->contact }}"
                                                           placeholder="Numéro de téléphone" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" class="modern-input" name="email" 
                                                           value="{{ $livraison->email }}"
                                                           placeholder="email@exemple.com">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="action-buttons mt-4">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('livraison.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i>
                                        Retour à la liste
                                    </a>
                                    <div>
                                        <button type="button" class="btn btn-warning me-2" onclick="resetForm()">
                                            <i class="fas fa-redo"></i>
                                            Réinitialiser
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i>
                                            Mettre à jour la livraison
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary-orange: #fea219;
    --primary-orange-dark: #e69100;
    --primary-green: #3a913e;
    --primary-green-dark: #2d7a30;
    --primary-blue: #fea219;
    --primary-blue-dark: #fea219;
    --white: #ffffff;
    --light-bg: #f8f9fa;
    --border-color: #e9ecef;
    --text-dark: #333333;
    --text-muted: #6c757d;
    --border-radius: 16px;
    --box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modern-card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
    margin-top: 20px;
}

.modern-header {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
    color: var(--white);
    border: none;
    padding: 40px;
    position: relative;
    overflow: hidden;
}

.modern-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
    transform: rotate(45deg);
    animation: shine 3s infinite;
}

@keyframes shine {
    0% { transform: translateX(-100%) rotate(45deg); }
    100% { transform: translateX(100%) rotate(45deg); }
}

.header-content {
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
    z-index: 2;
}

.header-icon {
    font-size: 3rem;
    opacity: 0.9;
}

.header-text .card-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 8px;
}

.header-text .card-subtitle {
    font-size: 1.1rem;
    opacity: 0.95;
    margin: 0;
}

.info-section {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 30px;
    margin-bottom: 30px;
    border: 1px solid var(--border-color);
}

.section-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--light-bg);
}

.section-header i {
    color: var(--primary-blue);
    font-size: 1.5rem;
}

.section-header h3 {
    color: var(--text-dark);
    font-weight: 600;
    margin: 0;
    flex: 1;
}

.livraison-item {
    background: var(--white);
    border: 2px solid var(--border-color);
    border-radius: 16px;
    margin-bottom: 20px;
    transition: var(--transition);
    overflow: hidden;
}

.livraison-item:hover {
    border-color: var(--primary-blue);
    box-shadow: 0 8px 25px rgba(33, 150, 243, 0.15);
    transform: translateY(-2px);
}

.livraison-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    background: linear-gradient(135deg, var(--light-bg) 0%, #f1f3f4 100%);
    border-bottom: 1px solid var(--border-color);
}

.livraison-title {
    display: flex;
    align-items: center;
    gap: 15px;
}

.number-badge {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
    color: var(--white);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
}

.livraison-info h4 {
    color: var(--text-dark);
    font-weight: 600;
    margin: 0 0 4px 0;
}

.livraison-info p {
    color: var(--text-muted);
    margin: 0;
    font-size: 0.9rem;
}

.livraison-body {
    padding: 25px;
}

.modern-form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
}

.form-label.required::after {
    content: " *";
    color: #dc3545;
}

.form-label i {
    color: var(--primary-blue);
    margin-right: 8px;
    width: 16px;
}

.modern-input, .modern-select {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid var(--border-color);
    border-radius: 12px;
    font-size: 16px;
    transition: var(--transition);
    background: var(--white);
}

.modern-input:focus, .modern-select:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 4px rgba(33, 150, 243, 0.1);
    transform: translateY(-1px);
}

.input-info {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-top: 6px;
    font-style: italic;
}

.btn {
    padding: 14px 30px;
    border-radius: 50px;
    font-weight: 600;
    border: none;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-secondary {
    background: var(--white);
    color: var(--text-dark);
    border: 2px solid var(--border-color);
}

.btn-secondary:hover {
    background: var(--light-bg);
    transform: translateY(-2px);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
    color: var(--white);
    box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(33, 150, 243, 0.4);
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    color: var(--white);
}

.btn-warning:hover {
    transform: translateY(-2px);
}

.badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8rem;
}

.bg-success {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%) !important;
}

.bg-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
}

.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
}

.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
}

@media (max-width: 768px) {
    .modern-header {
        padding: 30px 20px;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .header-icon {
        font-size: 2.5rem;
    }
    
    .header-text .card-title {
        font-size: 1.6rem;
    }
    
    .livraison-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .action-buttons .d-flex {
        flex-direction: column;
        gap: 15px;
    }
    
    .action-buttons .d-flex > div {
        width: 100%;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Réinitialiser le formulaire
    window.resetForm = function() {
        Swal.fire({
            title: 'Réinitialiser les modifications ?',
            text: "Toutes les modifications non sauvegardées seront perdues.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#fea219',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, réinitialiser',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.reload();
            }
        });
    };

    // Validation du formulaire
    document.getElementById('livraisonForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const chauffeurId = document.getElementById('chauffeur_id').value;
        if (!chauffeurId) {
            Swal.fire({
                title: 'Chauffeur manquant',
                text: 'Veuillez sélectionner un chauffeur',
                icon: 'warning',
                confirmButtonColor: '#fea219'
            });
            return;
        }

        // Confirmation de mise à jour
        Swal.fire({
            title: 'Confirmer la modification',
            html: `Vous allez mettre à jour les informations de cette livraison.<br><br>Confirmer l'opération ?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3a913e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, modifier',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
});
</script>

<!-- Inclure SweetAlert2 pour les belles alertes -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection