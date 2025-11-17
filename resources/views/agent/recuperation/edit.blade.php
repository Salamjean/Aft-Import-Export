@extends('agent.layouts.template')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card modern-card">
                <!-- En-tête avec dégradé vert -->
                <div class="card-header modern-header" style="background: linear-gradient(135deg, #0d8644 0%, #0d8644 100%);">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="header-text">
                            <h1 class="card-title">Modifier la Récupération</h1>
                            <p class="card-subtitle">Modifiez les informations de la récupération existante</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="recuperationForm" action="{{ route('agent.recuperation.update', $recuperation->id) }}" method="POST">
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
                                                        {{ $recuperation->chauffeur_id == $chauffeur->id ? 'selected' : '' }}>
                                                        {{ $chauffeur->name }} {{ $chauffeur->prenom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group modern-form-group">
                                            <label for="date_recuperation" class="form-label">
                                                <i class="fas fa-calendar-alt me-2"></i>Date de récupération prévue
                                            </label>
                                            <input type="date" class="modern-input" id="date_recuperation" name="date_recuperation" 
                                                   value="{{ $recuperation->date_recuperation ? $recuperation->date_recuperation->format('Y-m-d') : '' }}" 
                                                   min="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Récupération -->
                        <div class="info-section">
                            <div class="section-header">
                                <i class="fas fa-list-ul"></i>
                                <h3>Récupération à Modifier</h3>
                                <span class="badge bg-info text-center">Récupération #{{ $recuperation->id }}</span>
                            </div>

                            <div id="recuperations-container">
                                <!-- Récupération unique à modifier -->
                                <div class="recuperation-item modern-card">
                                    <div class="recuperation-header">
                                        <div class="recuperation-title">
                                            <div class="recuperation-number">
                                                <span class="number-badge" style="background: linear-gradient(135deg, #0d8644 0%, #0d8644 100%);">1</span>
                                            </div>
                                            <div class="recuperation-info">
                                                <h4>Récupération Principale</h4>
                                                <p>Modification des informations de la récupération</p>
                                            </div>
                                        </div>
                                        <div class="recuperation-status">
                                            <span class="badge 
                                                @if($recuperation->statut == 'en_cours') bg-warning
                                                @elseif($recuperation->statut == 'termine') bg-success
                                                @elseif($recuperation->statut == 'annule') bg-danger
                                                @else bg-secondary @endif">
                                                {{ ucfirst($recuperation->statut) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="recuperation-body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Nature de l'objet</label>
                                                    <input type="text" class="modern-input" name="nature_objet" 
                                                           value="{{ $recuperation->nature_objet }}"
                                                           placeholder="Ex: Colis, Documents, etc." required>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Quantité</label>
                                                    <input type="number" class="modern-input" name="quantite" 
                                                           value="{{ $recuperation->quantite }}"
                                                           min="1" required>
                                                    <div class="input-info">Codes à générer/supprimer</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Adresse de récupération</label>
                                                    <input type="text" class="modern-input" name="adresse_recuperation" 
                                                           value="{{ $recuperation->adresse_recuperation }}"
                                                           placeholder="Adresse complète de récupération" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-2">
                                            <div class="col-md-3">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Nom concerné</label>
                                                    <input type="text" class="modern-input" name="nom_concerne" 
                                                           value="{{ $recuperation->nom_concerne }}"
                                                           placeholder="Nom de la personne" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Prénom concerné</label>
                                                    <input type="text" class="modern-input" name="prenom_concerne" 
                                                           value="{{ $recuperation->prenom_concerne }}"
                                                           placeholder="Prénom de la personne" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Contact</label>
                                                    <input type="text" class="modern-input" name="contact" 
                                                           value="{{ $recuperation->contact }}"
                                                           placeholder="Numéro de téléphone" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" class="modern-input" name="email" 
                                                           value="{{ $recuperation->email }}"
                                                           placeholder="email@exemple.com">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Informations sur les codes existants -->
                            @if($recuperation->code_nature)
                            <div class="info-section mt-4">
                                <div class="section-header">
                                    <i class="fas fa-qrcode"></i>
                                    <h3>Gestion des Codes</h3>
                                </div>
                                <div class="alert alert-warning">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                                        <div>
                                            <strong>Attention :</strong> 
                                            La modification de la quantité va entraîner la régénération des codes QR.
                                            @if($recuperation->statut == 'termine')
                                            <br><span class="text-danger">Cette récupération est déjà terminée. La modification est déconseillée.</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="codes-preview mb-4">
                                    <h5>Codes actuels ({{ $recuperation->quantite }} code(s)) :</h5>
                                    <div class="codes-list">
                                        @php
                                            $codes = explode(',', $recuperation->code_nature);
                                        @endphp
                                        @foreach($codes as $index => $code)
                                            <div class="code-item d-flex justify-content-between align-items-center p-2 border-bottom">
                                                <span class="text-muted">Code {{ $index + 1 }}:</span>
                                                <span class="fw-bold">{{ $code }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Boutons d'action -->
                            <div class="action-buttons mt-4">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('agent.recuperation.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i>
                                        Retour à la liste
                                    </a>
                                    <div>
                                        <button type="button" class="btn btn-warning me-2" onclick="resetForm()">
                                            <i class="fas fa-redo"></i>
                                            Réinitialiser
                                        </button>
                                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #0d8644 0%, #0d8644 100%);">
                                            <i class="fas fa-save"></i>
                                            Mettre à jour la récupération
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
/* Styles similaires à la vue création avec ajustements */
:root {
    --primary-blue: #0d8644;
    --primary-blue-dark: #0d8644;
    --primary-green: #3a913e;
    --primary-green-dark: #2d7a30;
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

.recuperation-item {
    background: var(--white);
    border: 2px solid var(--border-color);
    border-radius: 16px;
    margin-bottom: 20px;
    transition: var(--transition);
    overflow: hidden;
}

.recuperation-item:hover {
    border-color: var(--primary-blue);
    box-shadow: 0 8px 25px rgba(33, 150, 243, 0.15);
    transform: translateY(-2px);
}

.recuperation-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    background: linear-gradient(135deg, var(--light-bg) 0%, #f1f3f4 100%);
    border-bottom: 1px solid var(--border-color);
}

.recuperation-title {
    display: flex;
    align-items: center;
    gap: 15px;
}

.number-badge {
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

.recuperation-info h4 {
    color: var(--text-dark);
    font-weight: 600;
    margin: 0 0 4px 0;
}

.recuperation-info p {
    color: var(--text-muted);
    margin: 0;
    font-size: 0.9rem;
}

.recuperation-body {
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

.bg-primary {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%) !important;
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

.bg-success {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%) !important;
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border: 1px solid #ffeaa7;
    border-radius: 12px;
}

.codes-list {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid var(--border-color);
    border-radius: 8px;
}

.code-item {
    background: var(--white);
    transition: var(--transition);
}

.code-item:hover {
    background: var(--light-bg);
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
    
    .recuperation-header {
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
    const quantiteInput = document.querySelector('input[name="quantite"]');
    const currentQuantite = {{ $recuperation->quantite }};
    
    quantiteInput.addEventListener('change', function() {
        const newQuantite = parseInt(this.value);
        
        if (newQuantite < 1) {
            this.value = currentQuantite;
            Swal.fire({
                title: 'Quantité invalide',
                text: 'La quantité doit être au moins 1',
                icon: 'error',
                confirmButtonColor: '#0d8644'
            });
            return;
        }
        
        if (newQuantite !== currentQuantite) {
            const action = newQuantite > currentQuantite ? 'ajouter' : 'supprimer';
            const difference = Math.abs(newQuantite - currentQuantite);
            
            Swal.fire({
                title: 'Modification de la quantité',
                html: `Vous allez <strong>${action} ${difference} code(s)</strong>.<br>
                      Les codes QR seront ${action === 'ajouter' ? 'générés' : 'supprimés'}.<br><br>
                      Confirmer cette modification ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d8644',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Oui, ${action} les codes`,
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (!result.isConfirmed) {
                    this.value = currentQuantite;
                }
            });
        }
    });

    // Réinitialiser le formulaire
    window.resetForm = function() {
        Swal.fire({
            title: 'Réinitialiser les modifications ?',
            text: "Toutes les modifications non sauvegardées seront perdues.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d8644',
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
    document.getElementById('recuperationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const chauffeurId = document.getElementById('chauffeur_id').value;
        if (!chauffeurId) {
            Swal.fire({
                title: 'Chauffeur manquant',
                text: 'Veuillez sélectionner un chauffeur',
                icon: 'warning',
                confirmButtonColor: '#0d8644'
            });
            return;
        }

        const newQuantite = parseInt(quantiteInput.value);
        const quantiteChange = newQuantite !== currentQuantite;

        let confirmationMessage = `Vous allez mettre à jour les informations de cette récupération.`;
        
        if (quantiteChange) {
            const action = newQuantite > currentQuantite ? 'ajout' : 'suppression';
            const difference = Math.abs(newQuantite - currentQuantite);
            confirmationMessage += `<br><strong>${difference} code(s) seront ${action}és.</strong>`;
        }

        Swal.fire({
            title: 'Confirmer la modification',
            html: confirmationMessage + `<br><br>Confirmer l'opération ?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d8644',
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