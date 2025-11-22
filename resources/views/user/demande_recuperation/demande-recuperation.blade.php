@extends('user.layouts.template')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header bg-gradient-orange rounded-3 p-4 shadow">
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <h1 class="text-white mb-2">📦 Demande de Dépôt ou Récupération</h1>
                        <p class="text-white-50 mb-0">Formulaire de demande de récupération d'objets</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-12">
            <div class="card modern-card shadow border-0">
                <div class="card-header bg-white py-4 border-bottom">
                    <h5 class="card-title mb-0 text-orange">
                        <i class="fas fa-truck-pickup me-2"></i>Informations de la Récupération
                    </h5>
                </div>

                <div class="card-body p-5">
                    <form id="demandeRecuperationForm" action="{{ route('demande-recuperation.store') }}" method="POST">
                        @csrf

                        <!-- Informations sur l'objet -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="section-title mb-4">
                                    <i class="fas fa-box me-2 text-orange"></i>Informations sur l'objet à déposer/récupérer
                                </h6>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Nature de l'objet <span class="text-danger">*</span></label>
                                <input type="text" class="modern-input @error('nature_objet') is-invalid @enderror" 
                                       name="nature_objet" value="{{ old('nature_objet') }}" 
                                       placeholder="Ex: Colis, Document, Paquet, Carton..." required>
                                @error('nature_objet')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Décrivez la nature de l'objet à déposer/récupérer
                                </small>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Quantité <span class="text-danger">*</span></label>
                                <input type="number" class="modern-input @error('quantite') is-invalid @enderror" 
                                       name="quantite" value="{{ old('quantite', 1) }}" 
                                       min="1" max="100" required>
                                @error('quantite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-3 mb-3">
                                    <label class="form-label fw-semibold">Agence de destination <span class="text-danger">*</span></label>
                                    <select class="modern-select @error('agence_id') is-invalid @enderror" name="agence_id" required>
                                        <option value="">Sélectionnez une agence</option>
                                        @foreach($agences as $agence)
                                            <option value="{{ $agence->id }}" {{ old('agence_id') == $agence->id ? 'selected' : '' }}>
                                                {{ $agence->name }} - {{ $agence->pays }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('agence_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Choisissez l'agence vers laquelle l'objet sera envoyé après déposer/récupération
                                    </small>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-semibold">Type de demande <span class="text-danger">*</span></label>
                                    <select class="modern-select @error('type_recuperation') is-invalid @enderror" name="type_recuperation" required>
                                        <option value="">Sélectionnez de demande</option>
                                        <option value="depot" {{ old('type_recuperation') == 'depot' ? 'selected' : '' }}>
                                            🚚 Dépôt
                                        </option>
                                        <option value="recuperation" {{ old('type_recuperation') == 'recuperation' ? 'selected' : '' }}>
                                            ⚡ Récuperation
                                        </option>
                                    </select>
                                    @error('type_recuperation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                        </div>

                        <!-- Informations personnelles -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="section-title mb-4">
                                    <i class="fas fa-user me-2 text-orange"></i>Informations personnelles
                                </h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="modern-input @error('nom_concerne') is-invalid @enderror" 
                                       name="nom_concerne" value="{{Auth::user()->name ?? 'Non defini'}}" 
                                       placeholder="Votre nom" readonly>
                                @error('nom_concerne')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="modern-input @error('prenom_concerne') is-invalid @enderror" 
                                       name="prenom_concerne" value="{{Auth::user()->prenom ?? 'Non defini'}}" 
                                       placeholder="Votre prénom" readonly>
                                @error('prenom_concerne')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Téléphone <span class="text-danger">*</span></label>
                                <input type="tel" class="modern-input @error('contact') is-invalid @enderror" 
                                       name="contact" value="{{Auth::user()->contact ?? 'Non defini'}}" 
                                       placeholder="Votre numéro de téléphone" readonly>
                                @error('contact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="modern-input @error('email') is-invalid @enderror" 
                                       name="email" value="{{Auth::user()->email ?? 'Non defini'}}" 
                                       placeholder="votre@email.com" readonly>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Adresse de récupération -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="section-title mb-4">
                                    <i class="fas fa-map-marker-alt me-2 text-orange"></i>Adresse de déposer/récupération
                                </h6>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Adresse complète <span class="text-danger">*</span></label>
                                <textarea class="modern-input @error('adresse_recuperation') is-invalid @enderror" 
                                          name="adresse_recuperation" rows="4" 
                                          placeholder="Adresse complète où se fera la récupération..." required>{{ old('adresse_recuperation') }}</textarea>
                                @error('adresse_recuperation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Date de déposer/récupération souhaitée</label>
                                <input type="date" class="modern-input @error('date_recuperation') is-invalid @enderror" 
                                       name="date_recuperation" value="{{ old('date_recuperation') }}"
                                       min="{{ date('Y-m-d') }}">
                                @error('date_recuperation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Laissez vide pour une récupération dès que possible
                                </small>
                            </div>
                        </div>

                        <!-- Boutons de soumission -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ url()->previous() }}" class="btn btn-outline-orange btn-lg rounded-pill px-4">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-orange btn-lg rounded-pill px-5" id="submitBtn">
                                        <i class="fas fa-paper-plane me-2"></i>Soumettre la demande
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inclure SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
:root {
    --primary-orange: #fea219;
    --dark-orange: #e8910c;
    --light-orange: #ffb74d;
}

/* En-tête */
.page-header {
    background: linear-gradient(135deg, var(--primary-orange), var(--dark-orange)) !important;
}

.bg-gradient-orange {
    background: linear-gradient(135deg, var(--primary-orange), var(--dark-orange)) !important;
}

/* Style pour les selects */
.modern-select {
    border-radius: 10px;
    border: 1px solid #e0e0e0;
    padding: 0.75rem;
    transition: all 0.3s ease;
    width: 100%;
    background-color: #fafafa;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23fea219' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 16px 12px;
}

.modern-select:focus {
    border-color: var(--primary-orange);
    box-shadow: 0 0 0 0.2rem rgba(254, 162, 25, 0.25);
    outline: none;
    background-color: white;
}

.modern-select.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.modern-select:hover {
    border-color: var(--light-orange);
}
/* Carte principale */
.modern-card {
    border-radius: 15px;
    border: none;
    border-top: 4px solid var(--primary-orange);
}

/* Couleurs texte */
.text-orange {
    color: var(--primary-orange) !important;
}

/* Sections du formulaire */
.section-title {
    color: var(--primary-orange);
    font-weight: 600;
    font-size: 1.1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f0f0f0;
}

/* Champs de formulaire */
.modern-input {
    border-radius: 10px;
    border: 1px solid #e0e0e0;
    padding: 0.75rem;
    transition: all 0.3s ease;
    width: 100%;
    background-color: #fafafa;
}

.modern-input:focus {
    border-color: var(--primary-orange);
    box-shadow: 0 0 0 0.2rem rgba(254, 162, 25, 0.25);
    outline: none;
    background-color: white;
}

.modern-input.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Boutons */
.btn-orange {
    background: linear-gradient(135deg, var(--primary-orange), var(--dark-orange));
    border: none;
    font-weight: 600;
    color: white;
    transition: all 0.3s ease;
}

.btn-orange:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(254, 162, 25, 0.4);
    color: white;
    background: linear-gradient(135deg, var(--dark-orange), var(--primary-orange));
}

.btn-outline-orange {
    border: 2px solid var(--primary-orange);
    color: var(--primary-orange);
    font-weight: 600;
    transition: all 0.3s ease;
    background: transparent;
}

.btn-outline-orange:hover {
    background-color: var(--primary-orange);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(254, 162, 25, 0.3);
}

/* Labels */
.form-label {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

/* Textes d'aide */
.form-text {
    font-size: 0.875rem;
    color: #6c757d;
}

/* Icônes dans les sections */
.section-title i {
    background: linear-gradient(135deg, var(--primary-orange), var(--dark-orange));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Card header */
.card-header {
    background: linear-gradient(to right, white, #fff9f0) !important;
}

/* Animation de chargement */
.loading-spinner {
    display: none;
    width: 20px;
    height: 20px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s ease-in-out infinite;
    margin-right: 8px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Effets de hover sur les champs */
.modern-input:hover {
    border-color: var(--light-orange);
}

/* Responsive */
@media (max-width: 768px) {
    .card-body {
        padding: 2rem !important;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 0.9rem;
    }
    
    .page-header {
        text-align: center;
    }
    
    .page-header .text-end {
        text-align: center !important;
        margin-top: 1rem;
    }
}

/* Style pour les messages d'erreur */
.invalid-feedback {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Amélioration de l'apparence des sections */
.row.mb-4 {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border-left: 3px solid var(--primary-orange);
}

/* Style pour le textarea */
textarea.modern-input {
    resize: vertical;
    min-height: 100px;
}

/* Placeholder stylisé */
.modern-input::placeholder {
    color: #a0a0a0;
    font-style: italic;
}

/* Focus state amélioré */
.modern-input:focus::placeholder {
    color: #d0d0d0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('demandeRecuperationForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Validation en temps réel
    const inputs = form.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });
    
    // Soumission du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            showLoading();
            this.submit();
        }
    });
    
    function validateField(field) {
        if (field.value.trim() === '' && field.hasAttribute('required')) {
            field.classList.add('is-invalid');
            return false;
        } else {
            field.classList.remove('is-invalid');
            return true;
        }
    }
    
    function validateForm() {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
                // Scroll vers le premier champ invalide
                if (isValid === false) {
                    field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    field.focus();
                }
            }
        });
        
        return isValid;
    }
    
    function showLoading() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <div class="loading-spinner" style="display: inline-block;"></div>
            Traitement en cours...
        `;
        submitBtn.querySelector('.loading-spinner').style.display = 'inline-block';
    }
    
    // Afficher les messages flash
    @if(session('success'))
        Swal.fire({
            title: 'Succès !',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#fea219',
            timer: 3000
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            title: 'Erreur !',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#fea219'
        });
    @endif

    // Animation d'entrée des champs
    const formSections = document.querySelectorAll('.row.mb-4');
    formSections.forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        section.style.transition = `all 0.5s ease ${index * 0.1}s`;
        
        setTimeout(() => {
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
        }, 100);
    });
});
</script>
@endsection