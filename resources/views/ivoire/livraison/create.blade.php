@extends('ivoire.layouts.template')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card modern-card">
                <!-- En-tête avec dégradé vert -->
                <div class="card-header modern-header">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <div class="header-text">
                            <h1 class="card-title">Programmation de Livraisons</h1>
                            <p class="card-subtitle">Ajoutez une ou plusieurs livraisons pour le même chauffeur</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="livraisonForm" action="{{ route('livraison.store') }}" method="POST">
                        @csrf
                        
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
                                                    <option value="{{ $chauffeur->id }}">
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
                                            <input type="date" class="modern-input" id="date_livraison" name="date_livraison" min="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Livraisons -->
                        <div class="info-section">
                            <div class="section-header">
                                <i class="fas fa-list-ul"></i>
                                <h3>Livraisons à Programmer</h3>
                                <span class="badge bg-success text-center" id="total-livraisons">1 livraison</span>
                            </div>

                            <div id="livraisons-container">
                                <!-- Première livraison -->
                                <div class="livraison-item modern-card" data-index="0">
                                    <div class="livraison-header">
                                        <div class="livraison-title">
                                            <div class="livraison-number">
                                                <span class="number-badge">1</span>
                                            </div>
                                            <div class="livraison-info">
                                                <h4>Livraison Principale</h4>
                                                <p>Informations de la première livraison</p>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-remove-livraison" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="livraison-body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Nature de l'objet</label>
                                                    <input type="text" class="modern-input" name="livraisons[0][nature_objet]" 
                                                           placeholder="Ex: Colis, Documents, etc." required>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Quantité</label>
                                                    <input type="number" class="modern-input" name="livraisons[0][quantite]" 
                                                           min="1" value="1" required>
                                                    <div class="input-info">Articles à livrer</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Adresse de livraison</label>
                                                    <input type="text" class="modern-input" name="livraisons[0][adresse_livraison]" 
                                                           placeholder="Adresse complète de livraison" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-2">
                                            <div class="col-md-3">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Nom du destinataire</label>
                                                    <input type="text" class="modern-input" name="livraisons[0][nom_concerne]" 
                                                           placeholder="Nom du destinataire" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Prénom du destinataire</label>
                                                    <input type="text" class="modern-input" name="livraisons[0][prenom_concerne]" 
                                                           placeholder="Prénom du destinataire" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Contact</label>
                                                    <input type="text" class="modern-input" name="livraisons[0][contact]" 
                                                           placeholder="Numéro de téléphone" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" class="modern-input" name="livraisons[0][email]" 
                                                           placeholder="email@exemple.com">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bouton Ajouter Livraison -->
                            <div style="display: flex; justify-content:space-between">
                                 <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                    <i class="fas fa-redo"></i>
                                    Réinitialiser
                                </button>
                                <button type="button" id="add-livraison" class="btn-add-livraison">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>Ajouter une autre livraison</span>
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                    Programmer les livraisons
                                </button>
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

/* Carte principale */
.modern-card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
    margin-top: 20px;
}

/* En-tête */
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

/* Sections */
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

.section-description {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 15px 20px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
    color: var(--text-muted);
    border-left: 4px solid var(--primary-green);
}

.section-description i {
    color: var(--primary-green);
    font-size: 1.2rem;
}

/* Éléments de livraison */
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

.btn-remove-livraison {
    background: #dc3545;
    color: var(--white);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    cursor: pointer;
}

.btn-remove-livraison:hover:not(:disabled) {
    background: #c82333;
    transform: scale(1.1);
}

.btn-remove-livraison:disabled {
    background: var(--text-muted);
    cursor: not-allowed;
    opacity: 0.5;
}

.livraison-body {
    padding: 25px;
}

/* Formulaire */
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

/* Boutons */
.btn-add-livraison {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color: var(--white);
    border: none;
    padding: 15px 30px;
    border-radius: 50px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: var(--transition);
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(58, 145, 62, 0.3);
}

.btn-add-livraison:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(58, 145, 62, 0.4);
}

.action-section {
    background: var(--light-bg);
    border-radius: var(--border-radius);
    padding: 30px;
    border: 1px solid var(--border-color);
}

.summary-card {
    background: var(--white);
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    border: 1px solid var(--border-color);
}

.summary-content {
    display: flex;
    justify-content: space-around;
    gap: 20px;
}

.summary-item {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
}

.summary-item i {
    font-size: 2rem;
    color: var(--primary-blue);
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.summary-text {
    display: flex;
    flex-direction: column;
}

.summary-label {
    font-size: 0.9rem;
    color: var(--text-muted);
    font-weight: 500;
}

.summary-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--primary-green);
}

.action-buttons {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
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

/* Badges */
.badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8rem;
}

.bg-success {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%) !important;
}

/* Responsive */
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
    
    .summary-content {
        flex-direction: column;
        gap: 20px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .livraison-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let livraisonCount = 1;
    
    // Mettre à jour les compteurs
    function updateCounters() {
        document.getElementById('total-livraisons').textContent = livraisonCount + ' livraison' + (livraisonCount > 1 ? 's' : '');
        
        // Calculer le total des articles
        let totalArticles = 0;
        document.querySelectorAll('input[name*="quantite"]').forEach(input => {
            totalArticles += parseInt(input.value) || 0;
        });
    }
    
    // Créer un nouvel élément livraison
    function createLivraisonItem(index) {
        const livraisonItem = document.createElement('div');
        livraisonItem.className = 'livraison-item modern-card';
        livraisonItem.setAttribute('data-index', index);
        livraisonItem.innerHTML = `
            <div class="livraison-header">
                <div class="livraison-title">
                    <div class="livraison-number">
                        <span class="number-badge">${index + 1}</span>
                    </div>
                    <div class="livraison-info">
                        <h4>Livraison #${index + 1}</h4>
                        <p>Livraison supplémentaire</p>
                    </div>
                </div>
                <button type="button" class="btn-remove-livraison">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="livraison-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-group modern-form-group">
                            <label class="form-label required">Nature de l'objet</label>
                            <input type="text" class="modern-input" name="livraisons[${index}][nature_objet]" 
                                   placeholder="Ex: Colis, Documents, etc." required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group modern-form-group">
                            <label class="form-label required">Quantité</label>
                            <input type="number" class="modern-input" name="livraisons[${index}][quantite]" 
                                   min="1" value="1" required>
                            <div class="input-info">Articles à livrer</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group modern-form-group">
                            <label class="form-label required">Adresse de livraison</label>
                            <input type="text" class="modern-input" name="livraisons[${index}][adresse_livraison]" 
                                   placeholder="Adresse complète de livraison" required>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-3">
                        <div class="form-group modern-form-group">
                            <label class="form-label required">Nom du destinataire</label>
                            <input type="text" class="modern-input" name="livraisons[${index}][nom_concerne]" 
                                   placeholder="Nom du destinataire" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group modern-form-group">
                            <label class="form-label required">Prénom du destinataire</label>
                            <input type="text" class="modern-input" name="livraisons[${index}][prenom_concerne]" 
                                   placeholder="Prénom du destinataire" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group modern-form-group">
                            <label class="form-label required">Contact</label>
                            <input type="text" class="modern-input" name="livraisons[${index}][contact]" 
                                   placeholder="Numéro de téléphone" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group modern-form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="modern-input" name="livraisons[${index}][email]" 
                                   placeholder="email@exemple.com">
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Ajouter l'événement de suppression
        const removeBtn = livraisonItem.querySelector('.btn-remove-livraison');
        removeBtn.addEventListener('click', function() {
            livraisonItem.remove();
            livraisonCount--;
            updateRemoveButtons();
            updateCounters();
        });
        
        // Ajouter l'événement pour mettre à jour les compteurs quand la quantité change
        const quantiteInput = livraisonItem.querySelector('input[name*="quantite"]');
        quantiteInput.addEventListener('input', updateCounters);
        
        return livraisonItem;
    }
    
    // Mettre à jour l'état des boutons de suppression
    function updateRemoveButtons() {
        const removeButtons = document.querySelectorAll('.btn-remove-livraison');
        if (removeButtons.length === 1) {
            removeButtons[0].disabled = true;
        } else {
            removeButtons.forEach(button => button.disabled = false);
        }
    }
    
    // Ajouter une nouvelle livraison
    document.getElementById('add-livraison').addEventListener('click', function() {
        const newLivraison = createLivraisonItem(livraisonCount);
        document.getElementById('livraisons-container').appendChild(newLivraison);
        livraisonCount++;
        updateRemoveButtons();
        updateCounters();
        
        // Animation d'apparition
        newLivraison.style.opacity = '0';
        newLivraison.style.transform = 'translateY(20px)';
        setTimeout(() => {
            newLivraison.style.transition = 'all 0.5s ease';
            newLivraison.style.opacity = '1';
            newLivraison.style.transform = 'translateY(0)';
        }, 10);
    });
    
    // Réinitialiser le formulaire
    window.resetForm = function() {
        Swal.fire({
            title: 'Réinitialiser le formulaire ?',
            text: "Toutes les données saisies seront perdues.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2196F3',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, réinitialiser',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('livraisonForm').reset();
                const container = document.getElementById('livraisons-container');
                while (container.children.length > 1) {
                    container.removeChild(container.lastChild);
                }
                livraisonCount = 1;
                updateRemoveButtons();
                updateCounters();
                
                Swal.fire({
                    title: 'Réinitialisé !',
                    text: 'Le formulaire a été réinitialisé.',
                    icon: 'success',
                    confirmButtonColor: '#3a913e'
                });
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
                confirmButtonColor: '#2196F3'
            });
            return;
        }
        
        // Calculer le total des articles
        let totalArticles = 0;
        document.querySelectorAll('input[name*="quantite"]').forEach(input => {
            totalArticles += parseInt(input.value) || 0;
        });
        
        if (totalArticles === 0) {
            Swal.fire({
                title: 'Quantité invalide',
                text: 'Veuillez saisir au moins une quantité valide',
                icon: 'warning',
                confirmButtonColor: '#2196F3'
            });
            return;
        }
        
        // Confirmation finale
        Swal.fire({
            title: 'Confirmer la programmation',
            html: `Vous allez programmer <strong> ${livraisonCount} livraison(s)</strong>.<br><br>Confirmer l'opération ?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3a913e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, programmer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                // Soumission du formulaire
                this.submit();
            }
        });
    });
    
    // Initialisation
    updateRemoveButtons();
    updateCounters();
    
    // Mettre à jour les compteurs quand les quantités changent
    document.addEventListener('input', function(e) {
        if (e.target.name && e.target.name.includes('quantite')) {
            updateCounters();
        }
    });
});
</script>

<!-- Inclure SweetAlert2 pour les belles alertes -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection