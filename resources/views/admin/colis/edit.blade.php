@extends('admin.layouts.template')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card modern-card">
                <div class="card-header modern-header">
                    <div class="header-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <h3 class="card-title">Modifier le Colis</h3>
                    <p class="card-subtitle">Modifiez les informations du colis étape par étape</p>
                </div>
                <div class="card-body">
                    <!-- Barre de progression -->
                    <div class="progress-container mb-4">
                        <div class="progress" style="height: 8px; border-radius: 10px; background-color: #f8f9fa;">
                            <div class="progress-bar" role="progressbar" style="width: 14.28%; border-radius: 10px; background: linear-gradient(135deg, #fea219 0%, #e69100 100%);" 
                                 id="progress-bar"></div>
                        </div>
                    </div>

                    <!-- Indicateurs d'étapes -->
                    <div class="steps-indicator mb-5">
                        <div class="d-flex justify-content-between position-relative">
                            <div class="step-connector"></div>
                            @foreach([1 => 'Transport', 2 => 'Expéditeur', 3 => 'Destinataire', 4 => 'Colis', 5 => 'Services', 6 => 'Paiement', 7 => 'Récapitulatif'] as $step => $label)
                            <div class="text-center step-indicator position-relative {{ $step == 1 ? 'active' : '' }}" 
                                 data-step="{{ $step }}">
                                <div class="step-number">
                                    {{ $step }}
                                </div>
                                <div class="step-label">
                                    {{ $label }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Message d'information -->
                    <div class="alert alert-info mb-4" id="conteneur_info_message">
                        <i class="fas fa-info-circle me-2"></i>
                        Veuillez sélectionner une agence d'expédition.
                    </div>
                    
                    <!-- Affichage du conteneur actif -->
                    <div class="conteneur-info-card mb-4" id="conteneur_info_section" style="display: none;">
                        <div class="card border-0 shadow-sm" style="border-left: 4px solid #fea219;">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="fw-bold mb-1" style="color: #0e914b;">
                                            <i class="fas fa-shipping-container me-2"></i>
                                            Conteneur Actif
                                        </h6>
                                        <div id="conteneur_details">
                                            <!-- Les détails du conteneur seront chargés dynamiquement -->
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="badge fs-6 text-white" id="reference_display" style="background-color: #0e914b;font-size:20px">{{ $colis->reference_colis }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('colis.update', $colis->id) }}" method="POST" class="modern-form" id="colisForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Champs cachés -->
                        <input type="hidden" name="conteneur_id" value="{{ $colis->conteneur_id }}" id="conteneur_id_input">
                        <input type="hidden" name="reference_colis" value="{{ $colis->reference_colis }}" id="reference_colis_input">

                        <!-- Étape 1: Transport -->
                        <div class="step-content" id="step-1">
                            <div class="step-header mb-4 text-center">
                                <h4 class="mb-2 fw-bold" style="color: #0e914b;">Informations de Transport</h4>
                                <p class="text-muted">Sélectionnez le mode de transport et l'agence d'expédition</p>
                            </div>

                            <div class="row g-4">
                                <!-- Colonne 1 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="mode_transit" class="form-label required">Mode de Transit</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-truck" style="color: #fea219;"></i>
                                            </span>
                                            <select class=" border-start-0 modern-select" id="mode_transit" name="mode_transit" required>
                                                <option value="">Sélectionnez un mode</option>
                                                <option value="Maritime" {{ $colis->mode_transit == 'Maritime' ? 'selected' : '' }}>Maritime</option>
                                                <option value="Aerien" {{ $colis->mode_transit == 'Aerien' ? 'selected' : '' }}>Aérien</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Colonne 2 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="agence_destination" class="form-label required">Agence de Destination</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-map-marker-alt" style="color: #fea219;"></i>
                                            </span>
                                            <input type="text" class=" border-start-0 modern-input" id="agence_destination" 
                                                name="agence_destination" readonly required
                                                style="background-color: #f8f9fa;" value="{{ $colis->agence_destination }}">
                                        </div>
                                        <small class="text-muted mt-1">Agence en Côte d'Ivoire</small>
                                        <input type="hidden" id="agence_destination_id" name="agence_destination_id" value="{{ $colis->agence_destination_id }}">
                                    </div>
                                </div>
                                

                                <!-- Colonne 3 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="agence_expedition_id" class="form-label required">Agence d'Expédition</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-plane-departure" style="color: #fea219;"></i>
                                            </span>
                                            <select class=" border-start-0 modern-select" id="agence_expedition_id" name="agence_expedition_id" required>
                                                <option value="">Sélectionnez une agence</option>
                                                @foreach($agencesExpedition as $agence)
                                                    <option value="{{ $agence->id }}" data-devise="{{ $agence->devise }}" {{ $colis->agence_expedition_id == $agence->id ? 'selected' : '' }}>
                                                        {{ $agence->name }} ({{ $agence->pays }} - {{ $agence->devise }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <small class="text-muted mt-1">Agences hors Côte d'Ivoire</small>
                                    </div>
                                </div>

                                <!-- Colonne 4 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label required">Devise d'Expédition</label>
                                        <div class="info-field bg-light rounded p-3 text-center h-100 d-flex flex-column justify-content-center">
                                            <strong id="devise_expedition_display" class="fs-5">{{ $colis->devise }}</strong>
                                            <small class="text-muted mt-1" id="devise_expedition_info">Devise de l'agence sélectionnée</small>
                                        </div>
                                        <input type="hidden" id="devise" name="devise" value="{{ $colis->devise }}">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-5 pt-3">
                                <div></div>
                                <button type="button" class="btn next-step" data-next="2"
                                        style="
                                            background: linear-gradient(135deg, #0e914b 0%, #0b7a3d 100%);
                                            border: none;
                                            border-radius: 25px;
                                            padding: 12px 30px;
                                            font-weight: 600;
                                            color: white;
                                            transition: all 0.3s ease;
                                        ">
                                    Suivant <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                       <!-- Étape 2: Informations de l'expéditeur -->
                        <div class="step-content d-none" id="step-2">
                            <div class="step-header mb-4 text-center">
                                <h4 class="mb-2 fw-bold" style="color: #0e914b;">Informations de l'Expéditeur</h4>
                                <p class="text-muted">Sélectionnez le type d'expéditeur et renseignez les informations</p>
                            </div>

                            <div class="row g-4">
                                <!-- Type d'expéditeur et Recherche sur la même ligne -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type_expediteur" class="form-label required">Type d'Expéditeur</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-users" style="color: #fea219;"></i>
                                            </span>
                                            <select class="border-start-0 modern-select" id="type_expediteur" name="type_expediteur" required>
                                                <option value="particulier" {{ $colis->type_expediteur == 'particulier' ? 'selected' : '' }}>Particulier</option>
                                                <option value="societe" {{ $colis->type_expediteur == 'societe' ? 'selected' : '' }}>Société</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Rechercher un utilisateur</label>
                                        <div class="search-container position-relative">
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0">
                                                    <i class="fas fa-search" style="color: #fea219;"></i>
                                                </span>
                                                <input type="text" class="border-start-0 modern-input" 
                                                    id="userSearch" placeholder="Tapez le nom, email ou contact d'un utilisateur...">
                                            </div>
                                            <div class="search-results dropdown-menu w-100" id="userSearchResults" 
                                                style="display: none; max-height: 300px; overflow-y: auto;">
                                                <!-- Les résultats de recherche apparaîtront ici -->
                                            </div>
                                        </div>
                                        <small class="text-muted">La sélection pré-remplira automatiquement les champs</small>
                                    </div>
                                </div>

                                <!-- Reste des champs en 4 colonnes -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name_expediteur" class="form-label required">
                                            <span id="label_nom">Nom</span>
                                        </label>
                                        <input type="text" class="modern-input" id="name_expediteur" name="name_expediteur" value="{{ $colis->name_expediteur }}" required>
                                    </div>
                                </div>

                                <div class="col-md-3" id="prenom_field">
                                    <div class="form-group">
                                        <label for="prenom_expediteur" class="form-label">Prénom</label>
                                        <input type="text" class="modern-input" id="prenom_expediteur" name="prenom_expediteur" value="{{ $colis->prenom_expediteur }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="email_expediteur" class="form-label required">Email</label>
                                        <input type="email" class="modern-input" id="email_expediteur" name="email_expediteur" value="{{ $colis->email_expediteur }}" required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="contact_expediteur" class="form-label required">Contact</label>
                                        <input type="text" class="modern-input" id="contact_expediteur" name="contact_expediteur" value="{{ $colis->contact_expediteur }}" required>
                                    </div>
                                </div>

                                <!-- Adresse - Pleine largeur -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="adresse_expediteur" class="form-label required">Adresse</label>
                                        <textarea class="modern-input" id="adresse_expediteur" name="adresse_expediteur" rows="3" required>{{ $colis->adresse_expediteur }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-5 pt-3">
                                <button type="button" class="btn prev-step" data-prev="1"
                                        style="
                                            background: white;
                                            border: 2px solid #0e914b;
                                            border-radius: 25px;
                                            padding: 12px 30px;
                                            font-weight: 600;
                                            color: #0e914b;
                                            transition: all 0.3s ease;
                                        ">
                                    <i class="fas fa-arrow-left me-2"></i>Précédent
                                </button>
                                <button type="button" class="btn next-step" data-next="3"
                                        style="
                                            background: linear-gradient(135deg, #0e914b 0%, #0b7a3d 100%);
                                            border: none;
                                            border-radius: 25px;
                                            padding: 12px 30px;
                                            font-weight: 600;
                                            color: white;
                                            transition: all 0.3s ease;
                                        ">
                                    Suivant <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                       <!-- Étape 3: Informations du destinataire -->
                        <div class="step-content d-none" id="step-3">
                            <div class="step-header mb-4 text-center">
                                <h4 class="mb-2 fw-bold" style="color: #0e914b;">Informations du Destinataire</h4>
                                <p class="text-muted">Renseignez les informations du destinataire en Côte d'Ivoire</p>
                            </div>

                            <div class="row g-4">
                                <!-- Colonne 1 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name_destinataire" class="form-label required">Nom</label>
                                        <input type="text" class="modern-input" id="name_destinataire" name="name_destinataire" value="{{ $colis->name_destinataire }}" required>
                                    </div>
                                </div>

                                <!-- Colonne 2 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="prenom_destinataire" class="form-label required">Prénom</label>
                                        <input type="text" class="modern-input" id="prenom_destinataire" name="prenom_destinataire" value="{{ $colis->prenom_destinataire }}" required>
                                    </div>
                                </div>

                                <!-- Colonne 3 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="email_destinataire" class="form-label required">Email</label>
                                        <input type="email" class="modern-input" id="email_destinataire" name="email_destinataire" value="{{ $colis->email_destinataire }}" required>
                                    </div>
                                </div>

                                <!-- Colonne 4 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="indicatif" class="form-label required">Indicatif</label>
                                        <select class="modern-select" id="indicatif" name="indicatif" required>
                                            <option value="">Sélectionnez un indicatif</option>
                                            <option value="+225" {{ $colis->indicatif == '+225' ? 'selected' : '' }}>+225 (Côte d'Ivoire)</option>
                                            <option value="+33" {{ $colis->indicatif == '+33' ? 'selected' : '' }}>+33 (France)</option>
                                            <option value="+1" {{ $colis->indicatif == '+1' ? 'selected' : '' }}>+1 (USA/Canada)</option>
                                            <option value="+44" {{ $colis->indicatif == '+44' ? 'selected' : '' }}>+44 (UK)</option>
                                            <option value="+86" {{ $colis->indicatif == '+86' ? 'selected' : '' }}>+86 (Chine)</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Colonne 1 - Ligne 2 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="contact_destinataire" class="form-label required">Contact</label>
                                        <input type="text" class="modern-input" id="contact_destinataire" name="contact_destinataire" value="{{ $colis->contact_destinataire }}" required>
                                    </div>
                                </div>

                                <!-- Colonne 2 - Ligne 2 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label required">Devise de Destination</label>
                                        <div class="info-field bg-light rounded p-3 text-center h-100 d-flex flex-column justify-content-center">
                                            <strong class="fs-5">XOF</strong>
                                            <small class="text-muted mt-1">Devise de la Côte d'Ivoire</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Adresse - Pleine largeur -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="adresse_destinataire" class="form-label required">Adresse en Côte d'Ivoire</label>
                                        <textarea class="modern-input" id="adresse_destinataire" name="adresse_destinataire" rows="3" required>{{ $colis->adresse_destinataire }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-5 pt-3">
                                <button type="button" class="btn prev-step" data-prev="2"
                                        style="
                                            background: white;
                                            border: 2px solid #0e914b;
                                            border-radius: 25px;
                                            padding: 12px 30px;
                                            font-weight: 600;
                                            color: #0e914b;
                                            transition: all 0.3s ease;
                                        ">
                                    <i class="fas fa-arrow-left me-2"></i>Précédent
                                </button>
                                <button type="button" class="btn next-step" data-next="4"
                                        style="
                                            background: linear-gradient(135deg, #0e914b 0%, #0b7a3d 100%);
                                            border: none;
                                            border-radius: 25px;
                                            padding: 12px 30px;
                                            font-weight: 600;
                                            color: white;
                                            transition: all 0.3s ease;
                                        ">
                                    Suivant <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Étape 4: Informations des colis -->
                        <div class="step-content d-none" id="step-4">
                            <div class="step-header mb-4 text-center">
                                <h4 class="mb-2 fw-bold" style="color: #0e914b;">Informations des Colis</h4>
                                <p class="text-muted">Modifiez les informations sur vos colis à expédier</p>
                            </div>

                            <!-- Message d'information pour le mode aérien -->
                            <div class="calcul-auto-info d-none" id="info-calcul-auto">
                                <i class="fas fa-calculator me-2"></i>
                                <strong>Calcul automatique :</strong> En mode aérien, le prix est calculé automatiquement à raison de 
                                <strong id="prix-kg-display">15 EUR par kg</strong>. Le prix sera converti en XOF si nécessaire.
                            </div>

                            <div id="colis-container">
                                @php
                                    $colisData = json_decode($colis->colis, true);
                                @endphp
                                @foreach($colisData as $index => $item)
                                <div class="colis-item card mb-4 border-0 shadow-sm" data-index="{{ $index }}" 
                                    style="border-radius: 15px; border-left: 4px solid #fea219;">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3"
                                        style="border-radius: 15px 15px 0 0; border-bottom: 1px solid #e9ecef;">
                                        <h6 class="mb-0 fw-bold" style="color: #0e914b;">
                                            <i class="fas fa-box me-2"></i>Colis #{{ $index + 1 }}
                                        </h6>
                                        @if($index > 0)
                                        <button type="button" class="btn btn-sm btn-danger remove-colis">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @else
                                        <button type="button" class="btn btn-sm btn-danger remove-colis d-none">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold required">Quantité</label>
                                                <input type="number" class="form-control" name="colis[{{ $index }}][quantite]" value="{{ $item['quantite'] }}" min="1" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold required">Produit</label>
                                                <div class="search-container position-relative">
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0">
                                                            <i class="fas fa-search" style="color: #fea219;"></i>
                                                        </span>
                                                        <input type="text" class="form-control border-start-0 produit-input" 
                                                            name="colis[{{ $index }}][produit]" value="{{ $item['produit'] }}" placeholder="Rechercher un produit..." required>
                                                        <button type="button" class="btn btn-primary add-produit-btn" data-index="{{ $index }}">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    <div class="search-results dropdown-menu w-100 produit-search-results" 
                                                        style="display: none; max-height: 300px; overflow-y: auto;">
                                                        <!-- Les résultats de recherche de produits apparaîtront ici -->
                                                    </div>
                                                </div>
                                                <small class="text-muted">Commencez à taper pour rechercher un produit</small>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold required">Prix Unitaire</label>
                                                <input type="number" class="form-control prix-unitaire-input" name="colis[{{ $index }}][prix_unitaire]" 
                                                    value="{{ $item['prix_unitaire'] }}" step="0.01" min="0" required>
                                            </div>
                                        </div>
                                        <input type="hidden" class="type-calcul-input" name="colis[{{ $index }}][type_calcul]" value="{{ $item['type_calcul'] ?? 'manuel' }}">
                                        <div class="row g-3 mt-3">
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Longueur (cm)</label>
                                                <input type="number" class="form-control" name="colis[{{ $index }}][longueur]" value="{{ $item['longueur'] ?? '' }}" step="0.1" min="0">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Largeur (cm)</label>
                                                <input type="number" class="form-control" name="colis[{{ $index }}][largeur]" value="{{ $item['largeur'] ?? '' }}" step="0.1" min="0">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Hauteur (cm)</label>
                                                <input type="number" class="form-control" name="colis[{{ $index }}][hauteur]" value="{{ $item['hauteur'] ?? '' }}" step="0.1" min="0">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Poids (kg)</label>
                                                <input type="number" class="form-control" name="colis[{{ $index }}][poids]" value="{{ $item['poids'] ?? '' }}" step="0.1" min="0">
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <label class="form-label fw-bold">Description</label>
                                            <textarea class="form-control" name="colis[{{ $index }}][description]" rows="2" placeholder="Description du produit...">{{ $item['description'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3">
                                <button type="button" class="btn" id="add-colis"
                                        style="
                                            background: linear-gradient(135deg, #fea219 0%, #e69100 100%);
                                            border: none;
                                            border-radius: 25px;
                                            padding: 10px 25px;
                                            font-weight: 600;
                                            color: white;
                                            transition: all 0.3s ease;
                                        ">
                                    <i class="fas fa-plus me-2"></i>Ajouter un autre colis
                                </button>
                                
                                <div>
                                    <button type="button" class="btn prev-step me-2" data-prev="3"
                                            style="
                                                background: white;
                                                border: 2px solid #0e914b;
                                                border-radius: 25px;
                                                padding: 10px 25px;
                                                font-weight: 600;
                                                color: #0e914b;
                                                transition: all 0.3s ease;
                                            ">
                                        <i class="fas fa-arrow-left me-2"></i>Précédent
                                    </button>
                                    <button type="button" class="btn next-step" data-next="5"
                                            style="
                                                background: linear-gradient(135deg, #0e914b 0%, #0b7a3d 100%);
                                                border: none;
                                                border-radius: 25px;
                                                padding: 10px 25px;
                                                font-weight: 600;
                                                color: white;
                                                transition: all 0.3s ease;
                                            ">
                                        Suivant <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Étape 5: Services -->
                        <div class="step-content d-none" id="step-5">
                            <div class="step-header mb-4 text-center">
                                <h4 class="mb-2 fw-bold" style="color: #0e914b;">Services Optionnels</h4>
                                <p class="text-muted">Sélectionnez les services supplémentaires pour votre colis</p>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="service_id" class="form-label">Service Optionnel</label>
                                        <div class="input-group">
                                            <select class=" modern-select" id="service_id" name="service_id">
                                                <option value="">Aucun service supplémentaire</option>
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" data-prix="{{ $service->prix_unitaire }}" {{ $colis->service_id == $service->id ? 'selected' : '' }}>
                                                        {{ $service->designation }} - {{ $service->prix_unitaire }} <span class="devise-dynamic">XOF</span>
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-primary" id="add-service-btn">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="prix_service" class="form-label">Prix du Service</label>
                                        <input type="number" class=" modern-input" id="prix_service" name="prix_service" 
                                               value="{{ $colis->prix_service }}" readonly step="0.01" min="0" placeholder="Sélectionnez un service">
                                    </div>

                                    <div class="mt-4 p-3 bg-light rounded">
                                        <h6 class="fw-bold" style="color: #0e914b;">Résumé des Coûts</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Montant Colis:</strong>
                                            </div>
                                            <div class="col-6 text-end">
                                                <span id="montant_colis_display">{{ $colis->montant_colis }}</span> <span id="devise_colis_display" class="devise-dynamic">{{ $colis->devise }}</span>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-6">
                                                <strong>Service:</strong>
                                            </div>
                                            <div class="col-6 text-end">
                                                <span id="montant_service_display">{{ $colis->prix_service ?? 0 }}</span> <span id="devise_service_display" class="devise-dynamic">{{ $colis->devise }}</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Total:</strong>
                                            </div>
                                            <div class="col-6 text-end">
                                                <span id="montant_total_display" class="fw-bold">{{ $colis->montant_total }}</span> <span id="devise_total_display" class="devise-dynamic">{{ $colis->devise }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-5 pt-3">
                                <button type="button" class="btn prev-step" data-prev="4">
                                    <i class="fas fa-arrow-left me-2"></i>Précédent
                                </button>
                                <button type="button" class="btn next-step" data-next="6">
                                    Suivant <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Étape 6: Paiement -->
                        <div class="step-content d-none" id="step-6">
                            <div class="step-header mb-4 text-center">
                                <h4 class="mb-2 fw-bold" style="color: #0e914b;">Informations de Paiement</h4>
                                <p class="text-muted">Sélectionnez la méthode de paiement et renseignez les informations</p>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <!-- Méthode de paiement -->
                                    <div class="form-group mb-4">
                                        <label for="methode_paiement" class="form-label required">Méthode de Paiement</label>
                                        <select class="modern-select" id="methode_paiement" name="methode_paiement" required>
                                            <option value="">Sélectionnez une méthode</option>
                                            <option value="espece" {{ $colis->methode_paiement == 'espece' ? 'selected' : '' }}>Espèce</option>
                                            <option value="virement_bancaire" {{ $colis->methode_paiement == 'virement_bancaire' ? 'selected' : '' }}>Virement Bancaire</option>
                                            <option value="cheque" {{ $colis->methode_paiement == 'cheque' ? 'selected' : '' }}>Chèque</option>
                                            <option value="mobile_money" {{ $colis->methode_paiement == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                            <option value="livraison" {{ $colis->methode_paiement == 'livraison' ? 'selected' : '' }}>Paiement à la Livraison</option>
                                        </select>
                                    </div>

                                    <!-- Champs conditionnels selon la méthode de paiement -->
                                    <div id="paiement-fields">
                                        <!-- Les champs spécifiques à chaque méthode apparaîtront ici -->
                                    </div>

                                    <!-- Informations générales de paiement -->
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="montant_paye" class="form-label required">Montant Payé</label>
                                                <input type="number" class="modern-input" id="montant_paye" name="montant_paye" 
                                                    value="{{ $colis->montant_paye }}" step="0.01" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reste_a_payer" class="form-label">Reste à Payer</label>
                                                <input type="number" class="modern-input" id="reste_a_payer" name="reste_a_payer" 
                                                    value="{{ $colis->reste_a_payer }}" readonly step="0.01" min="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="statut_paiement" class="form-label required">Statut du Paiement</label>
                                        <select class="modern-select" id="statut_paiement" name="statut_paiement" required>
                                            <option value="non_paye" {{ $colis->statut_paiement == 'non_paye' ? 'selected' : '' }}>Non Payé</option>
                                            <option value="partiellement_paye" {{ $colis->statut_paiement == 'partiellement_paye' ? 'selected' : '' }}>Partiellement Payé</option>
                                            <option value="totalement_paye" {{ $colis->statut_paiement == 'totalement_paye' ? 'selected' : '' }}>Totalement Payé</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="notes_paiement" class="form-label">Notes de Paiement</label>
                                        <textarea class="modern-input" id="notes_paiement" name="notes_paiement" rows="3" 
                                                placeholder="Notes supplémentaires concernant le paiement...">{{ $colis->notes_paiement }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-5 pt-3">
                                <button type="button" class="btn prev-step" data-prev="5">
                                    <i class="fas fa-arrow-left me-2"></i>Précédent
                                </button>
                                <button type="button" class="btn next-step" data-next="7">
                                    Suivant <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Étape 7: Récapitulatif -->
                        <div class="step-content d-none" id="step-7">
                            <div class="step-header mb-4 text-center">
                                <h4 class="mb-2 fw-bold" style="color: #0e914b;">Récapitulatif Final</h4>
                                <p class="text-muted">Vérifiez toutes les informations avant de finaliser la modification</p>
                            </div>

                            <div class="recap-container">
                                <!-- Section Transport et Référence -->
                                <div class="card recap-card mb-4">
                                    <div class="card-header recap-header">
                                        <h5 class="mb-0">
                                            <i class="fas fa-shipping-fast me-2"></i>
                                            Informations de Transport
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-borderless recap-table">
                                                    <tr>
                                                        <td class="fw-bold" style="width: 40%;">Mode de Transit:</td>
                                                        <td id="recap_mode_transit">{{ $colis->mode_transit }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Agence Expédition:</td>
                                                        <td id="recap_agence_expedition">{{ $colis->agence_expedition }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Agence Destination:</td>
                                                        <td id="recap_agence_destination">{{ $colis->agence_destination }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-borderless recap-table">
                                                    <tr>
                                                        <td class="fw-bold" style="width: 40%;">Référence:</td>
                                                        <td id="recap_reference" class="fw-bold text-primary">{{ $colis->reference_colis }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Conteneur:</td>
                                                        <td id="recap_conteneur">{{ $colis->conteneur->name_conteneur ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Devise:</td>
                                                        <td id="recap_devise">{{ $colis->devise }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section Expéditeur et Destinataire -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card recap-card h-100">
                                            <div class="card-header recap-header">
                                                <h5 class="mb-0">
                                                    <i class="fas fa-user me-2"></i>
                                                    Expéditeur
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-borderless recap-table">
                                                    <tr>
                                                        <td class="fw-bold" style="width: 40%;">Nom:</td>
                                                        <td id="recap_expediteur_nom">{{ $colis->name_expediteur }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Prénom:</td>
                                                        <td id="recap_expediteur_prenom">{{ $colis->prenom_expediteur ?? 'Non applicable' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Email:</td>
                                                        <td id="recap_expediteur_email">{{ $colis->email_expediteur }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Contact:</td>
                                                        <td id="recap_expediteur_contact">{{ $colis->contact_expediteur }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Adresse:</td>
                                                        <td id="recap_expediteur_adresse">{{ $colis->adresse_expediteur }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card recap-card h-100">
                                            <div class="card-header recap-header">
                                                <h5 class="mb-0">
                                                    <i class="fas fa-user-tag me-2"></i>
                                                    Destinataire
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-borderless recap-table">
                                                    <tr>
                                                        <td class="fw-bold" style="width: 40%;">Nom:</td>
                                                        <td id="recap_destinataire_nom">{{ $colis->name_destinataire }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Prénom:</td>
                                                        <td id="recap_destinataire_prenom">{{ $colis->prenom_destinataire }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Email:</td>
                                                        <td id="recap_destinataire_email">{{ $colis->email_destinataire }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Contact:</td>
                                                        <td id="recap_destinataire_contact">{{ $colis->contact_destinataire }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Adresse:</td>
                                                        <td id="recap_destinataire_adresse">{{ $colis->adresse_destinataire }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section Colis avec tableau -->
                                <div class="card recap-card mb-4">
                                    <div class="card-header recap-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="fas fa-boxes me-2"></i>
                                            Détails des Colis
                                        </h5>
                                        <span class="badge bg-primary fs-6" id="recap_nombre_colis">{{ count($colisData) }} colis</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover" id="recap_colis_table">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Produit</th>
                                                        <th>Quantité</th>
                                                        <th>Prix Unitaire</th>
                                                        <th>Total</th>
                                                        <th>Dimensions</th>
                                                        <th>Poids</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="recap_colis_body">
                                                    @php
                                                        $totalGeneral = 0;
                                                    @endphp
                                                    @foreach($colisData as $index => $item)
                                                    @php
                                                        $totalColis = $item['quantite'] * $item['prix_unitaire'];
                                                        $totalGeneral += $totalColis;
                                                        $dimensions = ($item['longueur'] ?? false && $item['largeur'] ?? false && $item['hauteur'] ?? false) 
                                                            ? $item['longueur'] . '×' . $item['largeur'] . '×' . $item['hauteur'] . ' cm' 
                                                            : 'Non spécifié';
                                                    @endphp
                                                    <tr>
                                                        <td class="fw-bold">{{ $index + 1 }}</td>
                                                        <td>{{ $item['produit'] }}</td>
                                                        <td>{{ $item['quantite'] }}</td>
                                                        <td>{{ number_format($item['prix_unitaire'], 2) }}</td>
                                                        <td class="fw-bold">{{ number_format($totalColis, 2) }}</td>
                                                        <td>{{ $dimensions }}</td>
                                                        <td>{{ $item['poids'] ? $item['poids'] . ' kg' : '-' }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="4" class="fw-bold text-end">Total Colis:</td>
                                                        <td colspan="3" class="fw-bold text-primary" id="recap_total_colis">{{ number_format($totalGeneral, 2) }} {{ $colis->devise }}</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section Services et Paiement -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card recap-card h-100">
                                            <div class="card-header recap-header">
                                                <h5 class="mb-0">
                                                    <i class="fas fa-concierge-bell me-2"></i>
                                                    Services
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-borderless recap-table">
                                                    <tr>
                                                        <td class="fw-bold" style="width: 50%;">Service:</td>
                                                        <td id="recap_service">{{ $colis->service->designation ?? 'Aucun service' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Prix Service:</td>
                                                        <td id="recap_prix_service">{{ number_format($colis->prix_service ?? 0, 2) }} {{ $colis->devise }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card recap-card h-100">
                                            <div class="card-header recap-header">
                                                <h5 class="mb-0">
                                                    <i class="fas fa-money-bill-wave me-2"></i>
                                                    Paiement
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-borderless recap-table">
                                                    <tr>
                                                        <td class="fw-bold" style="width: 50%;">Méthode:</td>
                                                        <td id="recap_methode_paiement">{{ ucfirst(str_replace('_', ' ', $colis->methode_paiement)) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Montant Total:</td>
                                                        <td id="recap_montant_total">{{ number_format($colis->montant_total, 2) }} {{ $colis->devise }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Montant Payé:</td>
                                                        <td id="recap_montant_paye">{{ number_format($colis->montant_paye, 2) }} {{ $colis->devise }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Reste à Payer:</td>
                                                        <td id="recap_reste_payer">{{ number_format($colis->reste_a_payer, 2) }} {{ $colis->devise }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Statut:</td>
                                                        <td>
                                                            <span class="badge {{ $colis->statut_paiement == 'totalement_paye' ? 'bg-success' : ($colis->statut_paiement == 'partiellement_paye' ? 'bg-warning' : 'bg-danger') }}">
                                                                {{ ucfirst(str_replace('_', ' ', $colis->statut_paiement)) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-5 pt-3">
                                <button type="button" class="btn prev-step" data-prev="6"
                                        style="
                                            background: white;
                                            border: 2px solid #0e914b;
                                            border-radius: 25px;
                                            padding: 12px 30px;
                                            font-weight: 600;
                                            color: #0e914b;
                                            transition: all 0.3s ease;
                                        ">
                                    <i class="fas fa-arrow-left me-2"></i>Précédent
                                </button>
                                <button type="submit" class="btn btn-success" id="submit-btn"
                                        style="
                                            background: linear-gradient(135deg, #0e914b 0%, #0b7a3d 100%);
                                            border: none;
                                            border-radius: 25px;
                                            padding: 12px 30px;
                                            font-weight: 600;
                                            color: white;
                                            transition: all 0.3s ease;
                                        ">
                                    <i class="fas fa-save me-2"></i>Modifier le Colis
                                </button>
                            </div>
                        </div>
                        
                        <input type="hidden" name="montant_colis" id="montant_colis_input" value="{{ $colis->montant_colis }}">
                        <input type="hidden" name="montant_total" id="montant_total_input" value="{{ $colis->montant_total }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Les modaux (identique à la vue de création) -->
<!-- Modal pour ajouter un produit -->
<div class="modal fade" id="addProduitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un Nouveau Produit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addProduitForm">
                    @csrf
                    <div class="form-group">
                        <label class="form-label required">Désignation</label>
                        <input type="text" class="form-control" name="designation" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Prix Unitaire</label>
                        <input type="number" class="form-control" name="prix_unitaire" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Agence de Destination</label>
                        <select class="form-control" name="agence_destination_id" required>
                            <option value="">Sélectionnez une agence</option>
                            <!-- Les options seront remplies par JavaScript -->
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="saveProduitBtn">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour ajouter un service -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un Nouveau Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addServiceForm">
                    @csrf
                    <div class="form-group">
                        <label class="form-label required">Désignation</label>
                        <input type="text" class="form-control" name="designation" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Prix Unitaire</label>
                        <input type="number" class="form-control" name="prix_unitaire" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Agence de Destination</label>
                        <select class="form-control" name="agence_destination_id" required>
                            <option value="">Sélectionnez une agence</option>
                            <!-- Les options seront remplies par JavaScript -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Description du service..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Type de Service</label>
                        <select class="form-control" name="type_service" required>
                            <option value="optionnel">Optionnel</option>
                            <option value="obligatoire">Obligatoire</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="saveServiceBtn">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<!-- Inclure Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Inclure SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les données existantes
    const existingColisData = @json($colisData);
    let colisCount = existingColisData.length;
    
    // Le reste du JavaScript reste identique à la vue de création
    // ... (tout le code JavaScript de la vue création)
    
    // Initialisation spécifique pour l'édition
    setTimeout(() => {
        // Mettre à jour les champs de paiement spécifiques
        updatePaiementFields();
        
        // Initialiser le type de calcul
        updateTypeCalcul();
        toggleInfoCalculAuto();
    }, 1000);
    
    function updatePaiementFields() {
        const method = document.getElementById('methode_paiement').value;
        const fieldsContainer = document.getElementById('paiement-fields');
        
        if (method && paiementTemplates[method]) {
            fieldsContainer.innerHTML = paiementTemplates[method];
            
            // Remplir les champs existants
            switch(method) {
                case 'virement_bancaire':
                    if (document.querySelector('input[name="nom_banque"]')) {
                        document.querySelector('input[name="nom_banque"]').value = '{{ $colis->nom_banque }}';
                    }
                    if (document.querySelector('input[name="numero_compte"]')) {
                        document.querySelector('input[name="numero_compte"]').value = '{{ $colis->numero_compte }}';
                    }
                    if (document.querySelector('input[name="montant_virement"]')) {
                        document.querySelector('input[name="montant_virement"]').value = '{{ $colis->montant_virement }}';
                    }
                    break;
                case 'mobile_money':
                    if (document.querySelector('select[name="operateur_mobile_money"]')) {
                        document.querySelector('select[name="operateur_mobile_money"]').value = '{{ $colis->operateur_mobile_money }}';
                    }
                    if (document.querySelector('input[name="numero_mobile_money"]')) {
                        document.querySelector('input[name="numero_mobile_money"]').value = '{{ $colis->numero_mobile_money }}';
                    }
                    if (document.querySelector('input[name="montant_mobile_money"]')) {
                        document.querySelector('input[name="montant_mobile_money"]').value = '{{ $colis->montant_mobile_money }}';
                    }
                    break;
                case 'espece':
                    if (document.querySelector('input[name="montant_espece"]')) {
                        document.querySelector('input[name="montant_espece"]').value = '{{ $colis->montant_espece }}';
                    }
                    break;
                case 'cheque':
                    if (document.querySelector('input[name="montant_cheque"]')) {
                        document.querySelector('input[name="montant_cheque"]').value = '{{ $colis->montant_cheque }}';
                    }
                    break;
            }
        }
    }
});
</script>

<style>
:root {
    --primary-color: #fea219;
    --primary-dark: #e8910c;
    --white: #ffffff;
    --light-gray: #f8f9fa;
    --medium-gray: #e9ecef;
    --dark-gray: #6c757d;
    --text-color: #333333;
    --border-radius: 12px;
    --box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}

.modern-card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
    margin-top: 30px;
}

.modern-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: var(--white);
    border: none;
    padding: 30px;
    text-align: center;
}

.modern-header .header-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.modern-header .card-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.modern-header .card-subtitle {
    opacity: 0.9;
    font-size: 1rem;
}

.card-body {
    padding: 40px;
}

/* Styles pour les étapes */
.steps-indicator {
    position: relative;
}

.step-connector {
    position: absolute;
    top: 25px;
    left: 10%;
    right: 10%;
    height: 3px;
    background: #e9ecef;
    z-index: 1;
}

.step-indicator {
    flex: 1;
    z-index: 2;
}

.step-number {
    width: 50px;
    height: 50px;
    background: #e9ecef;
    color: #6c757d;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.1rem;
    border: 3px solid white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: var(--transition);
    margin-bottom: 10px;
}

.step-indicator.active .step-number {
    background: linear-gradient(135deg, #fea219 0%, #e69100 100%);
    color: white;
    transform: scale(1.1);
}

.step-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #6c757d;
    transition: var(--transition);
}

.step-indicator.active .step-label {
    color: #0e914b;
}

.step-content {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Autres styles */
.conteneur-info-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: var(--border-radius);
    padding: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    font-weight: 600;
    color: var(--text-color);
    margin-bottom: 8px;
    display: block;
}

.form-label.required::after {
    content: " *";
    color: #e74c3c;
}

.modern-input, .modern-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--medium-gray);
    border-radius: 8px;
    font-size: 16px;
    transition: var(--transition);
    background-color: var(--white);
}

.modern-input:focus, .modern-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(254, 162, 25, 0.2);
}

.modern-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236c757d' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 16px;
}

.info-field {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    border: 1px solid #e9ecef;
}

.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-right: none;
}

/* Styles pour la recherche d'utilisateurs */
.search-container {
    position: relative;
}

.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.user-result-item {
    padding: 12px 15px;
    border-bottom: 1px solid #f8f9fa;
    cursor: pointer;
    transition: all 0.3s ease;
}

.user-result-item:hover {
    background-color: #f8f9fa;
}

.user-result-item:last-child {
    border-bottom: none;
}

.user-name {
    font-weight: 600;
    color: #333;
}

.user-details {
    font-size: 0.85rem;
    color: #6c757d;
}

.no-results {
    padding: 15px;
    text-align: center;
    color: #6c757d;
    font-style: italic;
}

/* Styles pour les colis */
.colis-item {
    border: 1px solid var(--medium-gray);
    border-radius: var(--border-radius);
    overflow: hidden;
}

.colis-item .card-header {
    background: var(--light-gray);
    border-bottom: 1px solid var(--medium-gray);
}

.add-produit-btn, #add-service-btn {
    border-radius: 0 8px 8px 0;
    border-left: none;
}

.input-group . {
    border-radius: 8px 0 0 8px;
}

/* Styles pour le calcul automatique */
.prix-unitaire-input:read-only {
    background-color: #f8f9fa !important;
    cursor: not-allowed !important;
    color: #6c757d;
}

.calcul-auto-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border: 1px solid #90caf9;
    border-radius: 8px;
    padding: 12px 15px;
    margin-bottom: 20px;
    font-size: 0.9rem;
    color: #1976d2;
}

.calcul-auto-info i {
    color: #1976d2;
    margin-right: 8px;
}

.calcul-auto-info strong {
    color: #0d47a1;
}

/* Responsive */
@media (max-width: 768px) {
    .step-indicator {
        margin-bottom: 15px;
    }
    
    .step-connector {
        display: none;
    }
    
    .card-body {
        padding: 25px;
    }
    
    .modern-header {
        padding: 20px;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 10px;
    }
}

/* Styles pour la recherche de produits */
.produit-search-results {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    width: 100%;
}

/* Ajustement pour les champs sur la même ligne */
.col-md-6 .form-group {
    margin-bottom: 1rem;
}

/* Responsive pour les champs sur la même ligne */
@media (max-width: 768px) {
    .col-md-6 {
        margin-bottom: 1rem;
    }
}
</style>
@endsection