@extends('agent.layouts.template')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card modern-card">
                <!-- En-tête avec dégradé bleu -->
                <div class="card-header modern-header" style="background: linear-gradient(135deg, #0d8644 0%, #0d8644 100%);">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fas fa-truck-pickup"></i>
                        </div>
                        <div class="header-text">
                            <h1 class="card-title">Programmation de Récupérations</h1>
                            <p class="card-subtitle">Ajoutez une ou plusieurs récupérations pour le même chauffeur</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="recuperationForm" action="{{ route('agent.recuperation.store') }}" method="POST">
                        @csrf
                        
                        <!-- Section Informations du chauffeur -->
                        <div class="info-section mb-5">
                            <div class="section-header">
                                <i class="fas fa-id-card"></i>
                                <h3>Informations du Chauffeur</h3>
                            </div>
                            <div class="section-body">
                                <!-- Ligne 1 : Recherche de dépôt -->
                                <div class="row g-4 mb-4">
                                    <div class="col-md-8">
                                        <div class="form-group modern-form-group">
                                            <label class="form-label">
                                                <i class="fas fa-barcode me-2"></i>Référence du dépôt existant
                                            </label>
                                            <input type="text" class="modern-input" id="search-depot" 
                                                placeholder="Entrez la référence d'un dépôt (ex: DEP-20241201-XXXX) pour pré-remplir">
                                            <div class="input-info">Facultatif - Permet de convertir un dépôt en récupération</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group modern-form-group">
                                            <label class="form-label" style="opacity: 0;">Action</label>
                                            <button type="button" class="btn btn-primary w-100" onclick="searchDepot()" 
                                                    style="background: linear-gradient(135deg, #0d8644 0%, #0d8644 100%); height: 48px;">
                                                <i class="fas fa-search me-2"></i>Rechercher
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ligne 2 : Chauffeur et Date -->
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
                                            <label for="date_recuperation" class="form-label">
                                                <i class="fas fa-calendar-alt me-2"></i>Date de récupération prévue
                                            </label>
                                            <input type="date" class="modern-input" id="date_recuperation" name="date_recuperation" min="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Résultats de recherche (affichés en dessous) -->
                            <div id="search-results" class="mt-3" style="display: none;">
                                <!-- Résultats de recherche -->
                            </div>
                        </div>

                        <!-- Section Récupérations -->
                        <div class="info-section">
                            <div class="section-header">
                                <i class="fas fa-list-ul"></i>
                                <h3>Récupérations à Programmer</h3>
                                <span class="badge bg-primary text-center" id="total-recuperations">1 récupération</span>
                            </div>

                            <div id="recuperations-container">
                                <!-- Première récupération -->
                                <div class="recuperation-item modern-card" data-index="0">
                                    <div class="recuperation-header">
                                        <div class="recuperation-title">
                                            <div class="recuperation-number">
                                                <span class="number-badge" style="background: linear-gradient(135deg, #0d8644 0%, #0d8644 100%);">1</span>
                                            </div>
                                            <div class="recuperation-info">
                                                <h4>Récupération Principale</h4>
                                                <p>Informations de la première récupération</p>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-remove-recuperation" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="recuperation-body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Nature de l'objet</label>
                                                    <input type="text" class="modern-input" name="recuperations[0][nature_objet]" 
                                                           placeholder="Ex: Colis, Documents, etc." required>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Quantité</label>
                                                    <input type="number" class="modern-input" name="recuperations[0][quantite]" 
                                                           min="1" value="1" required>
                                                    <div class="input-info">Codes à générer</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Adresse de récupération</label>
                                                    <input type="text" class="modern-input" name="recuperations[0][adresse_recuperation]" 
                                                           placeholder="Adresse complète de récupération" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-2">
                                            <div class="col-md-3">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Nom concerné</label>
                                                    <input type="text" class="modern-input" name="recuperations[0][nom_concerne]" 
                                                           placeholder="Nom de la personne" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Prénom concerné</label>
                                                    <input type="text" class="modern-input" name="recuperations[0][prenom_concerne]" 
                                                           placeholder="Prénom de la personne" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label required">Contact</label>
                                                    <input type="text" class="modern-input" name="recuperations[0][contact]" 
                                                           placeholder="Numéro de téléphone" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group modern-form-group">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" class="modern-input" name="recuperations[0][email]" 
                                                           placeholder="email@exemple.com">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bouton Ajouter Récupération -->
                            <div style="display: flex; justify-content:space-between">
                                 <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                    <i class="fas fa-redo"></i>
                                    Réinitialiser
                                </button>
                                <button type="button" id="add-recuperation" class="btn-add-recuperation" style="background: linear-gradient(135deg, #0d8644 0%, #0d8644 100%);">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>Ajouter une autre récupération</span>
                                </button>
                                <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #0d8644 0%, #0d8644 100%);">
                                    <i class="fas fa-paper-plane"></i>
                                    Programmer les récupérations
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
/* Styles similaires à la vue dépôt avec ajustements de couleurs */
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

/* Éléments de récupération */
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

.btn-remove-recuperation {
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

.btn-remove-recuperation:hover:not(:disabled) {
    background: #c82333;
    transform: scale(1.1);
}

.btn-remove-recuperation:disabled {
    background: var(--text-muted);
    cursor: not-allowed;
    opacity: 0.5;
}

.recuperation-body {
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
.btn-add-recuperation {
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
    box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
}

.btn-add-recuperation:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(33, 150, 243, 0.4);
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

/* Badges */
.badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8rem;
}

.bg-primary {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%) !important;
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
    
    .recuperation-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
// Variables globales
let recuperationCount = 1;

// Vérifier si la première récupération est vide (sans inclure la quantité)
function isFirstRecuperationEmpty() {
    const firstRecuperation = document.querySelector('.recuperation-item[data-index="0"]');
    if (!firstRecuperation) return false;
    
    const inputs = firstRecuperation.querySelectorAll('input');
    let isEmpty = true;
    
    inputs.forEach(input => {
        const name = input.getAttribute('name');
        // Exclure le champ quantité de la vérification
        if (!name.includes('[quantite]') && input.value.trim() !== '') {
            isEmpty = false;
        }
    });
    
    return isEmpty;
}

// Vérifier si un dépôt a déjà été ajouté
function isDepotAlreadyAdded(depotId) {
    const existingDepots = document.querySelectorAll('[data-depot-id]');
    for (let element of existingDepots) {
        if (element.getAttribute('data-depot-id') == depotId) {
            return true;
        }
    }
    return false;
}

// Pré-remplir la première récupération
function fillFirstRecuperation(depot) {
    const firstRecuperation = document.querySelector('.recuperation-item[data-index="0"]');
    if (!firstRecuperation) return;
    
    const inputs = firstRecuperation.querySelectorAll('input');
    inputs.forEach(input => {
        const name = input.getAttribute('name');
        if (name.includes('[nature_objet]')) {
            input.value = depot.nature_objet;
        } else if (name.includes('[quantite]')) {
            input.value = depot.quantite;
        } else if (name.includes('[nom_concerne]')) {
            input.value = depot.nom_concerne;
        } else if (name.includes('[prenom_concerne]')) {
            input.value = depot.prenom_concerne;
        } else if (name.includes('[contact]')) {
            input.value = depot.contact;
        } else if (name.includes('[email]')) {
            input.value = depot.email || '';
        } else if (name.includes('[adresse_recuperation]')) {
            input.value = depot.adresse_depot;
        }
    });
    
    // Ajouter l'attribut data-depot-id pour référence
    firstRecuperation.setAttribute('data-depot-id', depot.id);
    
    // Ajouter un badge indiquant que c'est un dépôt converti
    const header = firstRecuperation.querySelector('.recuperation-info');
    const originalHTML = header.innerHTML;
    header.innerHTML = originalHTML + `<div class="mt-1"><span class="badge bg-success"><i class="fas fa-sync me-1"></i>Converti depuis dépôt</span></div>`;
    
    // Mettre à jour les compteurs
    updateCounters();
    
    // Cacher les résultats de recherche
    document.getElementById('search-results').style.display = 'none';
    document.getElementById('search-depot').value = '';
}

// Afficher les résultats de recherche
function displaySearchResult(depot) {
    const resultsDiv = document.getElementById('search-results');
    resultsDiv.innerHTML = `
        <div class="depot-result modern-card p-3" data-depot-id="${depot.id}">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="mb-1 text-primary">${depot.reference}</h6>
                    <p class="mb-1"><strong>Client:</strong> ${depot.nom_concerne} ${depot.prenom_concerne}</p>
                    <p class="mb-1"><strong>Adresse:</strong> ${depot.adresse_depot}</p>
                    <p class="mb-1"><strong>Nature:</strong> ${depot.nature_objet} (${depot.quantite} unité(s))</p>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" class="btn btn-success" onclick="addDepotToRecuperation(${depot.id})">
                        <i class="fas fa-plus me-2"></i>Ajouter comme nouvelle récupération
                    </button>
                </div>
            </div>
            <div class="mt-2 text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                La récupération principale contient déjà des données, cette action créera une nouvelle récupération.
            </div>
        </div>
    `;
    resultsDiv.style.display = 'block';
}

// Ajouter un dépôt comme nouvelle récupération
function addDepotToRecuperation(depotId) {
    fetch(`/admin/depots/${depotId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const depot = data.data;
                
                const newRecuperation = createRecuperationItem(recuperationCount);
                
                const inputs = newRecuperation.querySelectorAll('input');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name.includes('[nature_objet]')) {
                        input.value = depot.nature_objet;
                    } else if (name.includes('[quantite]')) {
                        input.value = depot.quantite;
                    } else if (name.includes('[nom_concerne]')) {
                        input.value = depot.nom_concerne;
                    } else if (name.includes('[prenom_concerne]')) {
                        input.value = depot.prenom_concerne;
                    } else if (name.includes('[contact]')) {
                        input.value = depot.contact;
                    } else if (name.includes('[email]')) {
                        input.value = depot.email || '';
                    } else if (name.includes('[adresse_recuperation]')) {
                        input.value = depot.adresse_depot;
                    }
                });
                
                newRecuperation.setAttribute('data-depot-id', depotId);
                
                const header = newRecuperation.querySelector('.recuperation-info');
                const originalHTML = header.innerHTML;
                header.innerHTML = originalHTML + `<div class="mt-1"><span class="badge bg-success"><i class="fas fa-sync me-1"></i>Converti depuis dépôt</span></div>`;
                
                document.getElementById('recuperations-container').appendChild(newRecuperation);
                recuperationCount++;
                updateRemoveButtons();
                updateCounters();
                
                newRecuperation.style.opacity = '0';
                newRecuperation.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    newRecuperation.style.transition = 'all 0.5s ease';
                    newRecuperation.style.opacity = '1';
                    newRecuperation.style.transform = 'translateY(0)';
                }, 10);
                
                document.getElementById('search-results').style.display = 'none';
                document.getElementById('search-depot').value = '';
                
                Swal.fire({
                    title: 'Nouvelle récupération ajoutée !',
                    text: 'Le dépôt a été ajouté comme nouvelle récupération',
                    icon: 'success',
                    confirmButtonColor: '#0d8644',
                    timer: 2000
                });
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            Swal.fire({
                title: 'Erreur',
                text: 'Erreur lors de l\'ajout du dépôt: ' + error.message,
                icon: 'error',
                confirmButtonColor: '#0d8644'
            });
        });
}

// Recherche de dépôt
async function searchDepot() {
    const reference = document.getElementById('search-depot').value.trim();
    
    if (!reference) {
        Swal.fire({
            title: 'Champ vide',
            text: 'Veuillez entrer une référence de dépôt',
            icon: 'warning',
            confirmButtonColor: '#0d8644'
        });
        return;
    }
    
    if (!reference.startsWith('DEP-')) {
        Swal.fire({
            title: 'Format invalide',
            text: 'La référence doit commencer par "DEP-"',
            icon: 'warning',
            confirmButtonColor: '#0d8644'
        });
        return;
    }
    
    Swal.fire({
        title: 'Recherche en cours...',
        text: 'Recherche du dépôt ' + reference,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const response = await fetch(`/admin/depots/search?reference=${encodeURIComponent(reference)}`);
        const data = await response.json();
        
        Swal.close();
        
        if (data.success && data.depot) {
            // Vérifier si la récupération principale est vide
            if (isFirstRecuperationEmpty()) {
                // Pré-remplir la récupération principale
                fillFirstRecuperation(data.depot);
                Swal.fire({
                    title: 'Récupération principale pré-remplie !',
                    text: 'Les informations du dépôt ont été ajoutées à la récupération principale',
                    icon: 'success',
                    confirmButtonColor: '#0d8644',
                    timer: 2000
                });
            } else {
                // Vérifier si ce dépôt a déjà été ajouté comme récupération supplémentaire
                if (isDepotAlreadyAdded(data.depot.id)) {
                    Swal.fire({
                        title: 'Déjà ajouté',
                        text: 'Ce dépôt a déjà été ajouté à la liste des récupérations',
                        icon: 'warning',
                        confirmButtonColor: '#0d8644'
                    });
                    return;
                }
                
                // Afficher les résultats pour ajouter une nouvelle récupération
                displaySearchResult(data.depot);
            }
        } else {
            Swal.fire({
                title: 'Non trouvé',
                text: data.error || 'Aucun dépôt trouvé avec cette référence',
                icon: 'error',
                confirmButtonColor: '#0d8644'
            });
        }
    } catch (error) {
        Swal.fire({
            title: 'Erreur',
            text: 'Erreur lors de la recherche: ' + error.message,
            icon: 'error',
            confirmButtonColor: '#0d8644'
        });
    }
}

// Fonctions principales
function createRecuperationItem(index) {
    const recuperationItem = document.createElement('div');
    recuperationItem.className = 'recuperation-item modern-card';
    recuperationItem.setAttribute('data-index', index);
    recuperationItem.innerHTML = `
        <div class="recuperation-header">
            <div class="recuperation-title">
                <div class="recuperation-number">
                    <span class="number-badge" style="background: linear-gradient(135deg, #0d8644 0%, #0d8644 100%);">${index + 1}</span>
                </div>
                <div class="recuperation-info">
                    <h4>Récupération #${index + 1}</h4>
                    <p>Récupération supplémentaire</p>
                </div>
            </div>
            <button type="button" class="btn-remove-recuperation">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="recuperation-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="form-group modern-form-group">
                        <label class="form-label required">Nature de l'objet</label>
                        <input type="text" class="modern-input" name="recuperations[${index}][nature_objet]" 
                               placeholder="Ex: Colis, Documents, etc." required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group modern-form-group">
                        <label class="form-label required">Quantité</label>
                        <input type="number" class="modern-input" name="recuperations[${index}][quantite]" 
                               min="1" value="1" required>
                        <div class="input-info">Codes à générer</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group modern-form-group">
                        <label class="form-label required">Adresse de récupération</label>
                        <input type="text" class="modern-input" name="recuperations[${index}][adresse_recuperation]" 
                               placeholder="Adresse complète de récupération" required>
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <div class="form-group modern-form-group">
                        <label class="form-label required">Nom concerné</label>
                        <input type="text" class="modern-input" name="recuperations[${index}][nom_concerne]" 
                               placeholder="Nom de la personne" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group modern-form-group">
                        <label class="form-label required">Prénom concerné</label>
                        <input type="text" class="modern-input" name="recuperations[${index}][prenom_concerne]" 
                               placeholder="Prénom de la personne" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group modern-form-group">
                        <label class="form-label required">Contact</label>
                        <input type="text" class="modern-input" name="recuperations[${index}][contact]" 
                               placeholder="Numéro de téléphone" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group modern-form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="modern-input" name="recuperations[${index}][email]" 
                               placeholder="email@exemple.com">
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Ajouter l'événement de suppression
    const removeBtn = recuperationItem.querySelector('.btn-remove-recuperation');
    removeBtn.addEventListener('click', function() {
        recuperationItem.remove();
        recuperationCount--;
        updateRemoveButtons();
        updateCounters();
    });
    
    // Ajouter l'événement pour mettre à jour les compteurs quand la quantité change
    const quantiteInput = recuperationItem.querySelector('input[name*="quantite"]');
    quantiteInput.addEventListener('input', updateCounters);
    
    return recuperationItem;
}

function updateRemoveButtons() {
    const removeButtons = document.querySelectorAll('.btn-remove-recuperation');
    if (removeButtons.length === 1) {
        removeButtons[0].disabled = true;
    } else {
        removeButtons.forEach(button => button.disabled = false);
    }
}

function updateCounters() {
    document.getElementById('total-recuperations').textContent = recuperationCount + ' récupération' + (recuperationCount > 1 ? 's' : '');
    
    let totalCodes = 0;
    document.querySelectorAll('input[name*="quantite"]').forEach(input => {
        totalCodes += parseInt(input.value) || 0;
    });
}

function resetForm() {
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
            document.getElementById('recuperationForm').reset();
            const container = document.getElementById('recuperations-container');
            while (container.children.length > 1) {
                container.removeChild(container.lastChild);
            }
            recuperationCount = 1;
            updateRemoveButtons();
            updateCounters();
            
            Swal.fire({
                title: 'Réinitialisé !',
                text: 'Le formulaire a été réinitialisé.',
                icon: 'success',
                confirmButtonColor: '#2196F3'
            });
        }
    });
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Système de récupérations initialisé');
    
    updateRemoveButtons();
    updateCounters();
    
    document.getElementById('add-recuperation').addEventListener('click', function() {
        const newRecuperation = createRecuperationItem(recuperationCount);
        document.getElementById('recuperations-container').appendChild(newRecuperation);
        recuperationCount++;
        updateRemoveButtons();
        updateCounters();
        
        newRecuperation.style.opacity = '0';
        newRecuperation.style.transform = 'translateY(20px)';
        setTimeout(() => {
            newRecuperation.style.transition = 'all 0.5s ease';
            newRecuperation.style.opacity = '1';
            newRecuperation.style.transform = 'translateY(0)';
        }, 10);
    });
    
    document.getElementById('recuperationForm').addEventListener('submit', function(e) {
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
        
        let totalCodes = 0;
        document.querySelectorAll('input[name*="quantite"]').forEach(input => {
            totalCodes += parseInt(input.value) || 0;
        });
        
        if (totalCodes === 0) {
            Swal.fire({
                title: 'Quantité invalide',
                text: 'Veuillez saisir au moins une quantité valide',
                icon: 'warning',
                confirmButtonColor: '#2196F3'
            });
            return;
        }
        
        Swal.fire({
            title: 'Confirmer la programmation',
            html: `Vous allez programmer <strong> ${recuperationCount} récupération(s)</strong>.<br><br>Confirmer l'opération ?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d8644',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, programmer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
    
    document.getElementById('search-depot').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchDepot();
        }
    });
    
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