@extends('agent.layouts.template')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card modern-card">
                <div class="card-header modern-header">
                    <div class="header-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <h3 class="card-title">Créer un Nouveau Colis</h3>
                    <p class="card-subtitle">Remplissez les informations du colis étape par étape</p>
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

                    <!-- Affichage du conteneur actif -->
                    <div class="conteneur-info-card mb-4">
                        <div class="card border-0 shadow-sm" style="border-left: 4px solid #fea219;">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="fw-bold mb-1" style="color: #0e914b;">
                                            <i class="fas fa-shipping-container me-2"></i>
                                            Conteneur Actif
                                        </h6>
                                        <p class="mb-0 text-black">
                                            <strong>Nom:</strong> <span id="current_conteneur_name">{{ $conteneur->name_conteneur }}</span> | 
                                            <strong>Type:</strong> <span id="current_conteneur_type">{{ $conteneur->type_conteneur }}</span> | 
                                            <strong>Statut:</strong> 
                                            <span class="badge bg-success text-white">{{ $conteneur->statut }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="badge fs-6 text-white" id="reference_display" style="background-color: #0e914b;font-size:20px">{{ $reference }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('agent.colis.store') }}" method="POST" class="modern-form" id="colisForm">
                        @csrf
                        
                        <!-- Champs cachés -->
                        <input type="hidden" name="conteneur_id" value="{{ $conteneur->id }}" id="conteneur_id_input">
                        <input type="hidden" name="reference_colis" value="{{ $reference }}" id="reference_colis_input">

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
                                                <option value="Maritime">Maritime</option>
                                                <option value="Aerien">Aérien</option>
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
                                                style="background-color: #f8f9fa;">
                                        </div>
                                        <small class="text-muted mt-1">Agence en Côte d'Ivoire</small>
                                        <input type="hidden" id="agence_destination_id" name="agence_destination_id">
                                    </div>
                                </div>
                                

                                <!-- Colonne 3 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label required">Agence d'Expédition</label>
                                        <div class="info-field bg-light rounded p-3 text-center h-100 d-flex flex-column justify-content-center">
                                            <strong id="agence_expedition_display" class="fs-6">{{ $agenceExpedition->name ?? 'Louis Blériot' }}</strong>
                                            <small class="text-muted mt-1">{{ $agenceExpedition->pays ?? 'France' }}</small>
                                        </div>
                                        <input type="hidden" id="agence_expedition_id" name="agence_expedition_id" value="{{ $agenceExpedition->id ?? '' }}">
                                    </div>
                                </div>

                                <!-- Colonne 4 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label required">Devise d'Expédition</label>
                                        <div class="info-field bg-light rounded p-3 text-center h-100 d-flex flex-column justify-content-center">
                                            <strong id="devise_expedition_display" class="fs-5">{{ $agenceExpedition->devise ?? 'EUR' }}</strong>
                                            <small class="text-muted mt-1" id="devise_expedition_info">Devise de l'agence</small>
                                        </div>
                                        <input type="hidden" id="devise" name="devise" value="{{ $agenceExpedition->devise ?? 'EUR' }}">
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
                                                <option value="particulier">Particulier</option>
                                                <option value="societe">Société</option>
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
                                <input type="hidden" name="user_id" id="user_id" value="">
                                <!-- Reste des champs en 4 colonnes -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name_expediteur" class="form-label required">
                                            <span id="label_nom">Nom</span>
                                        </label>
                                        <input type="text" class="modern-input" id="name_expediteur" name="name_expediteur" required>
                                    </div>
                                </div>

                                <div class="col-md-3" id="prenom_field">
                                    <div class="form-group">
                                        <label for="prenom_expediteur" class="form-label">Prénom</label>
                                        <input type="text" class="modern-input" id="prenom_expediteur" name="prenom_expediteur">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="email_expediteur" class="form-label">Email</label>
                                        <input type="email" class="modern-input" id="email_expediteur" name="email_expediteur">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="contact_expediteur" class="form-label required">Contact</label>
                                        <input type="text" class="modern-input" id="contact_expediteur" name="contact_expediteur" required>
                                    </div>
                                </div>

                                <!-- Adresse - Pleine largeur -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="adresse_expediteur" class="form-label required">Adresse</label>
                                        <textarea class="modern-input" id="adresse_expediteur" name="adresse_expediteur" rows="3" required></textarea>
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
                                        <input type="text" class="modern-input" id="name_destinataire" name="name_destinataire" required>
                                    </div>
                                </div>

                                <!-- Colonne 2 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="prenom_destinataire" class="form-label required">Prénom</label>
                                        <input type="text" class="modern-input" id="prenom_destinataire" name="prenom_destinataire" required>
                                    </div>
                                </div>

                                <!-- Colonne 3 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="email_destinataire" class="form-label">Email</label>
                                        <input type="email" class="modern-input" id="email_destinataire" name="email_destinataire">
                                    </div>
                                </div>

                                <!-- Colonne 4 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="indicatif" class="form-label required">Indicatif</label>
                                        <select class="modern-select" id="indicatif" name="indicatif" required>
                                            <option value="">Sélectionnez un indicatif</option>
                                            <option value="+225">+225 (Côte d'Ivoire)</option>
                                            <option value="+33">+33 (France)</option>
                                            <option value="+86">+86 (Chine)</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Colonne 1 - Ligne 2 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="contact_destinataire" class="form-label required">Contact</label>
                                        <input type="text" class="modern-input" id="contact_destinataire" name="contact_destinataire" required>
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
                                        <textarea class="modern-input" id="adresse_destinataire" name="adresse_destinataire" rows="3" required></textarea>
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
                                <p class="text-muted">Ajoutez les informations sur vos colis à expédier</p>
                            </div>

                            <div id="colis-container">
                                <div class="colis-item card mb-4 border-0 shadow-sm" data-index="0" 
                                    style="border-radius: 15px; border-left: 4px solid #fea219;">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3"
                                        style="border-radius: 15px 15px 0 0; border-bottom: 1px solid #e9ecef;">
                                        <h6 class="mb-0 fw-bold" style="color: #0e914b;">
                                            <i class="fas fa-box me-2"></i>Colis #1
                                        </h6>
                                        <button type="button" class="btn btn-sm btn-danger remove-colis d-none">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold required">Quantité</label>
                                                <input type="number" class="form-control" name="colis[0][quantite]" min="1" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold required">Produit</label>
                                                <div class="search-container position-relative">
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0">
                                                            <i class="fas fa-search" style="color: #fea219;"></i>
                                                        </span>
                                                        <input type="text" class="form-control border-start-0 produit-input" 
                                                            name="colis[0][produit]" placeholder="Rechercher un produit..." required>
                                                        <button type="button" class="btn btn-primary add-produit-btn" data-index="0">
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
                                                <input type="number" class="form-control prix-unitaire-input" name="colis[0][prix_unitaire]" 
                                                    step="0.01" min="0" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold required">Type de Colis</label>
                                                <select class="form-control" name="colis[0][type_colis]" required>
                                                    <option value="Standard">Standard</option>
                                                    <option value="Fragile">Fragile</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row g-3 mt-3">
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Longueur (cm)</label>
                                                <input type="number" class="form-control" name="colis[0][longueur]" step="0.1" min="0">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Largeur (cm)</label>
                                                <input type="number" class="form-control" name="colis[0][largeur]" step="0.1" min="0">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Hauteur (cm)</label>
                                                <input type="number" class="form-control" name="colis[0][hauteur]" step="0.1" min="0">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Poids (kg)</label>
                                                <input type="number" class="form-control" name="colis[0][poids]" step="0.1" min="0">
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <label class="form-label fw-bold">Description</label>
                                            <textarea class="form-control" name="colis[0][description]" rows="2" placeholder="Description du produit..."></textarea>
                                        </div>
                                    </div>
                                </div>
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
                            <!-- REMPLACEZ $deviseExpe PAR la devise dynamique -->
                            <option value="{{ $service->id }}" data-prix="{{ $service->prix_unitaire }}">
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
                       readonly step="0.01" min="0" placeholder="Sélectionnez un service">
            </div>

            <div class="mt-4 p-3 bg-light rounded">
                <h6 class="fw-bold" style="color: #0e914b;">Résumé des Coûts</h6>
                <div class="row">
                    <div class="col-6">
                        <strong>Montant Colis:</strong>
                    </div>
                    <div class="col-6 text-end">
                        <span id="montant_colis_display">0</span> <span id="devise_colis_display" class="devise-dynamic">XOF</span>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-6">
                        <strong>Service:</strong>
                    </div>
                    <div class="col-6 text-end">
                        <span id="montant_service_display">0</span> <span id="devise_service_display" class="devise-dynamic">XOF</span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <strong>Total:</strong>
                    </div>
                    <div class="col-6 text-end">
                        <span id="montant_total_display" class="fw-bold">0</span> <span id="devise_total_display" class="devise-dynamic">XOF</span>
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
                                            <option value="espece">Espèce</option>
                                            <option value="virement_bancaire">Virement Bancaire</option>
                                            <option value="cheque">Chèque</option>
                                            <option value="mobile_money">Mobile Money</option>
                                            <option value="livraison">Paiement à la Livraison</option>
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
                                                    step="0.01" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reste_a_payer" class="form-label">Reste à Payer</label>
                                                <input type="number" class="modern-input" id="reste_a_payer" name="reste_a_payer" 
                                                    readonly step="0.01" min="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="statut_paiement" class="form-label required">Statut du Paiement</label>
                                        <select class="modern-select" id="statut_paiement" name="statut_paiement" required>
                                            <option value="non_paye">Non Payé</option>
                                            <option value="partiellement_paye">Partiellement Payé</option>
                                            <option value="totalement_paye">Totalement Payé</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="notes_paiement" class="form-label">Notes de Paiement</label>
                                        <textarea class="modern-input" id="notes_paiement" name="notes_paiement" rows="3" 
                                                placeholder="Notes supplémentaires concernant le paiement..."></textarea>
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
                                <p class="text-muted">Vérifiez toutes les informations avant de finaliser l'enregistrement</p>
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
                                                        <td id="recap_mode_transit">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Agence Expédition:</td>
                                                        <td id="recap_agence_expedition">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Agence Destination:</td>
                                                        <td id="recap_agence_destination">-</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-borderless recap-table">
                                                    <tr>
                                                        <td class="fw-bold" style="width: 40%;">Référence:</td>
                                                        <td id="recap_reference" class="fw-bold text-primary">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Conteneur:</td>
                                                        <td id="recap_conteneur">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Devise:</td>
                                                        <td id="recap_devise">-</td>
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
                                                        <td id="recap_expediteur_nom">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Prénom:</td>
                                                        <td id="recap_expediteur_prenom">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Email:</td>
                                                        <td id="recap_expediteur_email">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Contact:</td>
                                                        <td id="recap_expediteur_contact">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Adresse:</td>
                                                        <td id="recap_expediteur_adresse">-</td>
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
                                                        <td id="recap_destinataire_nom">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Prénom:</td>
                                                        <td id="recap_destinataire_prenom">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Email:</td>
                                                        <td id="recap_destinataire_email">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Contact:</td>
                                                        <td id="recap_destinataire_contact">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Adresse:</td>
                                                        <td id="recap_destinataire_adresse">-</td>
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
                                        <span class="badge bg-primary fs-6" id="recap_nombre_colis">0 colis</span>
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
                                                        <th>Type de colis</th>
                                                        <th>Dimensions</th>
                                                        <th>Poids</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="recap_colis_body">
                                                    <!-- Les lignes des colis seront ajoutées ici dynamiquement -->
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="4" class="fw-bold text-end">Total Colis:</td>
                                                        <td colspan="3" class="fw-bold text-primary" id="recap_total_colis">0.00</td>
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
                                                        <td id="recap_service">Aucun service</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Prix Service:</td>
                                                        <td id="recap_prix_service">0.00</td>
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
                                                        <td id="recap_methode_paiement">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Montant Total:</td>
                                                        <td id="recap_montant_total">0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Montant Payé:</td>
                                                        <td id="recap_montant_paye">0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Reste à Payer:</td>
                                                        <td id="recap_reste_payer">0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Statut:</td>
                                                        <td><span class="badge" id="recap_statut_paiement">-</span></td>
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
                                    <i class="fas fa-save me-2"></i>Enregistrer le Colis
                                </button>
                            </div>
                        </div>
                    <input type="hidden" name="montant_colis" id="montant_colis_input" value="0">
                    <input type="hidden" name="montant_total" id="montant_total_input" value="0">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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
    let currentStep = 1;
    let colisCount = 1;
    let searchTimeout;
    let produitSearchTimeout;
    let currentProduitIndex = 0;

    // Constantes pour le calcul automatique en mode aérien
    const PRIX_PAR_KG_AERIEN_EURO = 15;
    const TAUX_CONVERSION_EURO_XOF = 655;

    // Données des agences de destination en Côte d'Ivoire
    const agencesDestination = {
        'Maritime': {
            name: 'DS Translog Carrefour Angré',
            id: 4
        },
        'Aerien': {
            name: 'DS Translog Angré 8ème Tranche', 
            id: 2
        }
    };

    const paiementTemplates = {
    espece: `
        <div class="paiement-method-fields">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Le montant payé sera enregistré dans le champ "Montant Payé" ci-dessous.
            </div>
        </div>
    `,
    
    virement_bancaire: `
        <div class="paiement-method-fields">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label required">Nom de la Banque</label>
                        <input type="text" class="modern-input" name="nom_banque" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label required">Numéro de Compte</label>
                        <input type="text" class="modern-input" name="numero_compte" required>
                    </div>
                </div>
            </div>
        </div>
    `,
    
    cheque: `
        <div class="paiement-method-fields">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Le montant du chèque sera enregistré dans le champ "Montant Payé" ci-dessous.
            </div>
        </div>
    `,
    
    mobile_money: `
        <div class="paiement-method-fields">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label required">Opérateur</label>
                        <select class="modern-select" name="operateur_mobile_money" required>
                            <option value="">Sélectionnez un opérateur</option>
                            <option value="WAVE">WAVE</option>
                            <option value="ORANGE">ORANGE</option>
                            <option value="MOOV">MOOV</option>
                            <option value="MTN">MTN</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label required">Numéro de Téléphone</label>
                        <input type="text" class="modern-input" name="numero_mobile_money" required>
                    </div>
                </div>
            </div>
        </div>
    `,
    
    livraison: `
        <div class="paiement-method-fields">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Le paiement sera effectué à la livraison du colis. Le montant payé sera de 0.
            </div>
            <input type="hidden" name="montant_livraison" value="0">
        </div>
    `
};

    // Gestion des étapes
    function showStep(step) {
        document.querySelectorAll('.step-content').forEach(el => {
            el.classList.add('d-none');
        });
        document.getElementById(`step-${step}`).classList.remove('d-none');
        
        // Mettre à jour la barre de progression
        const progress = ((step - 1) / 6) * 100;
        document.getElementById('progress-bar').style.width = `${progress}%`;
        
        // Mettre à jour les indicateurs d'étapes
        document.querySelectorAll('.step-indicator').forEach(el => {
            el.classList.remove('active');
            if (parseInt(el.dataset.step) <= step) {
                el.classList.add('active');
            }
        });
        
        currentStep = step;

        // Mettre à jour les montants à chaque changement d'étape
        updateMontants();

        // Si on arrive à l'étape 7, mettre à jour le récapitulatif
        if (step === 7) {
            updateRecap();
        }
    }

    // Navigation entre les étapes
    document.querySelectorAll('.next-step').forEach(button => {
        button.addEventListener('click', function() {
            const nextStep = parseInt(this.dataset.next);
            if (validateStep(currentStep)) {
                showStep(nextStep);
            }
        });
    });

    document.querySelectorAll('.prev-step').forEach(button => {
        button.addEventListener('click', function() {
            const prevStep = parseInt(this.dataset.prev);
            showStep(prevStep);
        });
    });

    // Gestion du mode de transit
    document.getElementById('mode_transit').addEventListener('change', function() {
        updateAgenceDestination();
        updateConteneurEtReference();
        updateTypeCalcul();
        toggleInfoCalculAuto();
        updateMontants(); // Ajouté pour mettre à jour les montants
    });

    // Gestion du type d'expéditeur
    document.getElementById('type_expediteur').addEventListener('change', function() {
        updateExpediteurFields();
    });

    // Gestion de la méthode de paiement
    document.getElementById('methode_paiement').addEventListener('change', function() {
        const method = this.value;
        const fieldsContainer = document.getElementById('paiement-fields');
        
        if (method && paiementTemplates[method]) {
            fieldsContainer.innerHTML = paiementTemplates[method];
            
            // Si c'est le paiement à la livraison, on désactive le montant payé
            if (method === 'livraison') {
                document.getElementById('montant_paye').value = '0';
                document.getElementById('montant_paye').setAttribute('readonly', 'true');
                document.getElementById('statut_paiement').value = 'non_paye';
            } else {
                document.getElementById('montant_paye').removeAttribute('readonly');
            }
            
            updateResteAPayer();
        } else {
            fieldsContainer.innerHTML = '';
        }
    });

    // Gestion du montant payé
    document.getElementById('montant_paye').addEventListener('input', function() {
        updateResteAPayer();
    });

    // Gestion du service
    document.getElementById('service_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const prix = selectedOption.getAttribute('data-prix') || 0;
        
        document.getElementById('prix_service').value = prix;
        updateMontants();
    });

    // Écouteur pour le bouton d'ajout de service
    document.getElementById('add-service-btn').addEventListener('click', function() {
        $('#addServiceModal').modal('show');
    });

    // Sauvegarde d'un nouveau produit
    document.getElementById('saveProduitBtn').addEventListener('click', function() {
        saveProduit();
    });

    // Sauvegarde d'un nouveau service
    document.getElementById('saveServiceBtn').addEventListener('click', function() {
        saveService();
    });

    // Ajout de colis
    document.getElementById('add-colis').addEventListener('click', function() {
        addColis();
    });

    // Recherche d'utilisateurs
    document.getElementById('userSearch').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const searchTerm = e.target.value.trim();
        
        if (searchTerm.length < 2) {
            hideSearchResults();
            return;
        }
        
        searchTimeout = setTimeout(() => {
            searchUsers(searchTerm);
        }, 300);
    });

    function searchUsers(searchTerm) {
        const url = '{{ url("users/search") }}?q=' + encodeURIComponent(searchTerm);
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau: ' + response.status);
            }
            return response.json();
        })
        .then(users => {
            displaySearchResults(users);
        })
        .catch(error => {
            console.error('Erreur de recherche:', error);
            hideSearchResults();
        });
    }

    function displaySearchResults(users) {
        const resultsContainer = document.getElementById('userSearchResults');
        resultsContainer.innerHTML = '';

        if (users.length === 0) {
            resultsContainer.innerHTML = '<div class="no-results">Aucun utilisateur trouvé</div>';
        } else {
            users.forEach(user => {
                const userElement = document.createElement('div');
                userElement.className = 'user-result-item';
                userElement.innerHTML = `
                    <div class="user-name">${user.name} ${user.prenom || ''}</div>
                    <div class="user-details">
                        ${user.email} • ${user.contact || 'Non renseigné'}
                    </div>
                `;
                userElement.addEventListener('click', () => selectUser(user));
                resultsContainer.appendChild(userElement);
            });
        }
        
        resultsContainer.style.display = 'block';
    }

    function hideSearchResults() {
        document.getElementById('userSearchResults').style.display = 'none';
    }
    function selectUser(user) {
            document.getElementById('name_expediteur').value = user.name;
            document.getElementById('prenom_expediteur').value = user.prenom || '';
            document.getElementById('email_expediteur').value = user.email;
            document.getElementById('contact_expediteur').value = user.contact || '';
            document.getElementById('adresse_expediteur').value = user.adresse || '';
            
            // Remplir le champ caché avec l'ID de l'utilisateur
            document.getElementById('user_id').value = user.id;
            
            hideSearchResults();
            document.getElementById('userSearch').value = '';
            
            Swal.fire({
                icon: 'success',
                title: 'Utilisateur sélectionné',
                text: `Les informations de ${user.name} ont été pré-remplies`,
                confirmButtonColor: '#0e914b',
                timer: 2000
            });
        }

    function searchProduits(searchTerm, currentIndex) {
        const agenceDestinationId = document.getElementById('agence_destination_id').value;
        
        fetch(`/produits/search?q=${encodeURIComponent(searchTerm)}&agence_destination_id=${agenceDestinationId}`)
            .then(response => response.json())
            .then(produits => {
                displayProduitResults(produits, currentIndex);
            })
            .catch(error => {
                console.error('Erreur de recherche de produits:', error);
                hideProduitResults();
            });
    }

    function displayProduitResults(produits, currentIndex) {
        const colisItem = document.querySelector(`.colis-item[data-index="${currentIndex}"]`);
        if (!colisItem) return;
        
        const resultsContainer = colisItem.querySelector('.produit-search-results');
        if (!resultsContainer) return;
        
        resultsContainer.innerHTML = '';

        if (produits.length === 0) {
            resultsContainer.innerHTML = '<div class="no-results">Aucun produit trouvé</div>';
        } else {
            produits.forEach(produit => {
                const produitElement = document.createElement('div');
                produitElement.className = 'user-result-item';
                produitElement.innerHTML = `
                    <div class="user-name">${produit.designation}</div>
                    <div class="user-details">
                        ${produit.prix_unitaire} • ${produit.agence_destination?.name || 'Agence non spécifiée'}
                    </div>
                `;
                produitElement.addEventListener('click', () => selectProduit(produit, currentIndex));
                resultsContainer.appendChild(produitElement);
            });
        }
        
        resultsContainer.style.display = 'block';
    }

    function selectProduit(produit, index) {
        const modeTransit = document.getElementById('mode_transit').value;
        
        if (modeTransit === 'Aerien') {
            Swal.fire({
                icon: 'info',
                title: 'Mode Aérien',
                text: 'En mode aérien, le prix est calculé automatiquement basé sur le poids. La sélection de produit est désactivée.',
                confirmButtonColor: '#0e914b'
            });
            return;
        }
        
        const colisItem = document.querySelector(`.colis-item[data-index="${index}"]`);
        if (colisItem) {
            const produitInput = colisItem.querySelector(`[name="colis[${index}][produit]"]`);
            const prixInput = colisItem.querySelector(`[name="colis[${index}][prix_unitaire]"]`);
            
            produitInput.value = produit.designation;
            prixInput.value = produit.prix_unitaire;
            
            hideProduitResults(index);
            updateMontants();
        }
    }

// Fonction identique à celle de l'admin pour charger les agences - VERSION CORRIGÉE
function updateAgencesInModals(agences) {
    console.log('🔄 updateAgencesInModals appelée avec:', agences);
    
    const selects = document.querySelectorAll('#addProduitModal select[name="agence_destination_id"], #addServiceModal select[name="agence_destination_id"]');
    
    console.log('🔍 Nombre de selects trouvés:', selects.length);
    
    selects.forEach((select, index) => {
        console.log(`🔍 Traitement du select ${index + 1}:`, select);
        select.innerHTML = '<option value="">Sélectionnez une agence</option>';
        
        if (agences && agences.length > 0) {
            agences.forEach(agence => {
                const option = document.createElement('option');
                option.value = agence.id;
                option.textContent = `${agence.name} (${agence.pays})`;
                select.appendChild(option);
            });
            console.log(`✅ ${agences.length} agences ajoutées au select ${index + 1}`);
        } else {
            console.warn('⚠️ Aucune agence à ajouter');
        }
    });
}

// Modifier la fonction updateConteneurEtReference pour inclure le chargement des agences
async function updateConteneurEtReference() {
    const modeTransit = document.getElementById('mode_transit').value;
    
    if (!modeTransit) {
        console.log('Mode de transit non sélectionné');
        return;
    }

    try {
        const url = "{{ route('agent.colis.get-conteneur-reference') }}";
        const params = new URLSearchParams({
            mode_transit: modeTransit
        });

        console.log('Envoi requête:', `${url}?${params}`);
        
        const response = await fetch(`${url}?${params}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        console.log('Statut réponse:', response.status);
        
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Données reçues:', data);
        
        if (data.success) {
            // Mettre à jour le conteneur
            document.getElementById('current_conteneur_name').textContent = data.conteneur.name_conteneur;
            document.getElementById('current_conteneur_type').textContent = data.conteneur.type_conteneur;
            document.getElementById('conteneur_id_input').value = data.conteneur.id;
            
            // Mettre à jour la référence
            document.getElementById('reference_display').textContent = data.reference;
            document.getElementById('reference_colis_input').value = data.reference;
            
            // Mettre à jour l'agence d'expédition
            if (data.agenceExpedition) {
                document.getElementById('agence_expedition_display').textContent = data.agenceExpedition.name;
                document.getElementById('agence_expedition_id').value = data.agenceExpedition.id;
                document.getElementById('devise_expedition_display').textContent = data.agenceExpedition.devise;
                document.getElementById('devise').value = data.agenceExpedition.devise;
                updateAllDevises(data.agenceExpedition.devise);
            }
            
            // CHARGER LES AGENCES DANS LES MODALS (comme dans l'admin)
            if (data.agencesDestination) {
                updateAgencesInModals(data.agencesDestination);
            }
            
            console.log('Mise à jour réussie');
            
            // Mettre à jour le type de calcul après la mise à jour de la devise
            setTimeout(() => {
                updateTypeCalcul();
                toggleInfoCalculAuto();
                updatePrixKgDisplay();
            }, 100);
            
            // Mettre à jour le récapitulatif si on est à l'étape 7
            if (currentStep === 7) {
                updateRecap();
            }
            
        } else {
            throw new Error(data.message || 'Erreur inconnue du serveur');
        }
    } catch (error) {
        console.error('Erreur lors de la mise à jour du conteneur:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Impossible de mettre à jour le conteneur et la référence: ' + error.message,
            confirmButtonColor: '#0e914b'
        });
    }
}

    function hideProduitResults(index = null) {
        if (index !== null) {
            const colisItem = document.querySelector(`.colis-item[data-index="${index}"]`);
            if (colisItem) {
                const resultsContainer = colisItem.querySelector('.produit-search-results');
                if (resultsContainer) {
                    resultsContainer.style.display = 'none';
                }
            }
        } else {
            document.querySelectorAll('.produit-search-results').forEach(container => {
                container.style.display = 'none';
            });
        }
    }

    function saveProduit() {
    const form = document.getElementById('addProduitForm');
    const formData = new FormData(form);

    // Utiliser exactement la même route que l'admin
    const url = "{{ route('admin.produits.store') }}";

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const produitInput = document.querySelector(`[name="colis[${currentProduitIndex}][produit]"]`);
            const prixInput = document.querySelector(`[name="colis[${currentProduitIndex}][prix_unitaire]"]`);
            
            produitInput.value = data.produit.designation;
            prixInput.value = data.produit.prix_unitaire;
            
            $('#addProduitModal').modal('hide');
            form.reset();
            
            Swal.fire({
                icon: 'success',
                title: 'Produit ajouté',
                text: 'Le produit a été ajouté avec succès',
                confirmButtonColor: '#0e914b'
            });

            updateMontants();
        } else {
            throw new Error(data.message || 'Erreur lors de l\'ajout du produit');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Une erreur est survenue lors de l\'ajout du produit: ' + error.message,
            confirmButtonColor: '#0e914b'
        });
    });
}

function saveService() {
    const form = document.getElementById('addServiceForm');
    const formData = new FormData(form);

    // Utiliser exactement la même route que l'admin
    const url = "{{ route('admin.services.store') }}";
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const serviceSelect = document.getElementById('service_id');
            const newOption = document.createElement('option');
            newOption.value = data.service.id;
            newOption.textContent = `${data.service.designation} - ${data.service.prix_unitaire} XOF`;
            newOption.setAttribute('data-prix', data.service.prix_unitaire);
            serviceSelect.appendChild(newOption);
            
            $('#addServiceModal').modal('hide');
            form.reset();
            
            Swal.fire({
                icon: 'success',
                title: 'Service ajouté',
                text: 'Le service a été ajouté avec succès',
                confirmButtonColor: '#0e914b'
            });
        } else {
            throw new Error(data.message || 'Erreur lors de l\'ajout du service');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Une erreur est survenue lors de l\'ajout du service: ' + error.message,
            confirmButtonColor: '#0e914b'
        });
    });
}
    // Recherche de produits
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('produit-input')) {
            clearTimeout(produitSearchTimeout);
            const searchTerm = e.target.value.trim();
            const colisItem = e.target.closest('.colis-item');
            const index = colisItem ? colisItem.dataset.index : 0;
            
            if (searchTerm.length < 2) {
                hideProduitResults(index);
                return;
            }
            
            produitSearchTimeout = setTimeout(() => {
                searchProduits(searchTerm, index);
            }, 300);
        }
    });

    // Écouteurs pour recharger les agences à l'ouverture des modals
document.addEventListener('click', function(e) {
    if (e.target.closest('.add-produit-btn')) {
        currentProduitIndex = e.target.closest('.add-produit-btn').getAttribute('data-index');
        console.log('🔄 Ouverture modal produit, rechargement des agences...');
        // Recharger les agences depuis l'API
        loadAgencesFromAPI();
        $('#addProduitModal').modal('show');
    }
});

// Écouteur pour le bouton d'ajout de service
document.getElementById('add-service-btn').addEventListener('click', function() {
    console.log('🔄 Ouverture modal service, rechargement des agences...');
    // Recharger les agences depuis l'API
    loadAgencesFromAPI();
    $('#addServiceModal').modal('show');
});

// Fonction pour charger les agences depuis l'API
async function loadAgencesFromAPI() {
    const modeTransit = document.getElementById('mode_transit').value;
    
    if (!modeTransit) {
        console.warn('⚠️ Mode de transit non sélectionné pour charger les agences');
        return;
    }

    try {
        const url = "{{ route('agent.colis.get-conteneur-reference') }}";
        const params = new URLSearchParams({
            mode_transit: modeTransit
        });

        console.log('🔄 Chargement des agences depuis API...');
        
        const response = await fetch(`${url}?${params}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success && data.agencesDestination) {
            console.log('✅ Agences chargées:', data.agencesDestination);
            updateAgencesInModals(data.agencesDestination);
        } else {
            console.warn('⚠️ Aucune agence dans la réponse');
            // Fallback : utiliser l'agence de destination actuelle
            const agenceDestinationId = document.getElementById('agence_destination_id').value;
            const agenceDestinationName = document.getElementById('agence_destination').value;
            if (agenceDestinationId) {
                const fallbackAgences = [{
                    id: agenceDestinationId,
                    name: agenceDestinationName,
                    pays: 'Côte d\'Ivoire'
                }];
                updateAgencesInModals(fallbackAgences);
            }
        }
    } catch (error) {
        console.error('❌ Erreur lors du chargement des agences:', error);
    }
}

    // Suppression de colis
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-colis')) {
            const colisItem = e.target.closest('.colis-item');
            if (document.querySelectorAll('.colis-item').length > 1) {
                colisItem.remove();
                document.querySelectorAll('.colis-item').forEach((item, index) => {
                    item.dataset.index = index;
                    item.querySelector('.card-header h6').textContent = `Colis #${index + 1}`;
                });
                colisCount = document.querySelectorAll('.colis-item').length;
                updateMontants();
            }
        }
    });

    // Cacher les résultats quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-container')) {
            hideSearchResults();
            hideProduitResults();
        }
    });

    // Gestion de la soumission du formulaire
    document.getElementById('colisForm').addEventListener('submit', function(e) {
        console.log('=== DÉBUT SOUMISSION FORMULAIRE ===');
        
        // Valider toutes les étapes
        if (!validateAllSteps()) {
            console.log('❌ Validation échouée - Soumission bloquée');
            e.preventDefault();
            return;
        }
        
        console.log('✅ Validation réussie - Soumission autorisée');
        
        // Afficher l'indicateur de chargement
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...';
        
        // La soumission se poursuit normalement
        console.log('=== FIN SOUMISSION FORMULAIRE ===');
    });

    // Fonctions principales
    function updateAgenceDestination() {
        const modeTransit = document.getElementById('mode_transit').value;
        
        if (modeTransit && agencesDestination[modeTransit]) {
            const agence = agencesDestination[modeTransit];
            document.getElementById('agence_destination').value = agence.name;
            document.getElementById('agence_destination_id').value = agence.id;
        } else {
            document.getElementById('agence_destination').value = '';
            document.getElementById('agence_destination_id').value = '';
        }
        
        // Mettre à jour le récapitulatif si on est à l'étape 7
        if (currentStep === 7) {
            updateRecap();
        }
    }

    async function updateConteneurEtReference() {
        const modeTransit = document.getElementById('mode_transit').value;
        
        if (!modeTransit) {
            console.log('Mode de transit non sélectionné');
            return;
        }

        try {
            const url = "{{ route('agent.colis.get-conteneur-reference') }}";
            const params = new URLSearchParams({
                mode_transit: modeTransit
            });

            console.log('Envoi requête:', `${url}?${params}`);
            
            const response = await fetch(`${url}?${params}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            console.log('Statut réponse:', response.status);
            
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Données reçues:', data);
            
            if (data.success) {
                // Mettre à jour le conteneur
                document.getElementById('current_conteneur_name').textContent = data.conteneur.name_conteneur;
                document.getElementById('current_conteneur_type').textContent = data.conteneur.type_conteneur;
                document.getElementById('conteneur_id_input').value = data.conteneur.id;
                
                // Mettre à jour la référence
                document.getElementById('reference_display').textContent = data.reference;
                document.getElementById('reference_colis_input').value = data.reference;
                
                // Mettre à jour l'agence d'expédition
                if (data.agenceExpedition) {
                    document.getElementById('agence_expedition_display').textContent = data.agenceExpedition.name;
                    document.getElementById('agence_expedition_id').value = data.agenceExpedition.id;
                    document.getElementById('devise_expedition_display').textContent = data.agenceExpedition.devise;
                    document.getElementById('devise').value = data.agenceExpedition.devise;
                    updateAllDevises(data.agenceExpedition.devise);
                }
                
                console.log('Mise à jour réussie');
                
                // Mettre à jour le type de calcul après la mise à jour de la devise
                setTimeout(() => {
                    updateTypeCalcul();
                    toggleInfoCalculAuto();
                    updatePrixKgDisplay();
                }, 100);
                
                // Mettre à jour le récapitulatif si on est à l'étape 7
                if (currentStep === 7) {
                    updateRecap();
                }
                
            } else {
                throw new Error(data.message || 'Erreur inconnue du serveur');
            }
        } catch (error) {
            console.error('Erreur lors de la mise à jour du conteneur:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de mettre à jour le conteneur et la référence: ' + error.message,
                confirmButtonColor: '#0e914b'
            });
        }
    }

    function updateExpediteurFields() {
        const typeExpediteur = document.getElementById('type_expediteur').value;
        const prenomField = document.getElementById('prenom_field');
        const labelNom = document.getElementById('label_nom');
        
        if (typeExpediteur === 'societe') {
            // Mode Société
            prenomField.style.display = 'none';
            labelNom.innerHTML = 'Nom de la Société <span class="text-danger">*</span>';
            document.getElementById('prenom_expediteur').value = '';
            document.getElementById('prenom_expediteur').removeAttribute('required');
        } else {
            // Mode Particulier
            prenomField.style.display = 'block';
            labelNom.innerHTML = 'Nom <span class="text-danger">*</span>';
            document.getElementById('prenom_expediteur').setAttribute('required', 'required');
        }
        
        // Mettre à jour le récapitulatif si on est à l'étape 7
        if (currentStep === 7) {
            updateRecap();
        }
    }

    // NOUVELLE FONCTION : Mettre à jour le type de calcul basé sur le mode de transit
    function updateTypeCalcul() {
        const modeTransit = document.getElementById('mode_transit').value;
        
        document.querySelectorAll('.colis-item').forEach(item => {
            const prixUnitaireInput = item.querySelector('.prix-unitaire-input');
            const poidsInput = item.querySelector('input[name*="[poids]"]');
            
            if (modeTransit === 'Aerien') {
                // Mode aérien : calcul automatique basé sur le poids
                if (prixUnitaireInput) {
                    prixUnitaireInput.setAttribute('readonly', 'true');
                    prixUnitaireInput.style.backgroundColor = '#f8f9fa';
                    prixUnitaireInput.style.cursor = 'not-allowed';
                    
                    // Calculer le prix automatiquement si le poids est renseigné
                    if (poidsInput && poidsInput.value) {
                        calculerPrixAuto(poidsInput);
                    }
                }
            } else {
                // Mode maritime : saisie manuelle
                if (prixUnitaireInput) {
                    prixUnitaireInput.removeAttribute('readonly');
                    prixUnitaireInput.style.backgroundColor = '';
                    prixUnitaireInput.style.cursor = '';
                }
            }
        });
        
        updateMontants();
    }

    // NOUVELLE FONCTION : Calculer le prix automatiquement basé sur le poids (mode aérien)
    function calculerPrixAuto(poidsInput) {
        const colisItem = poidsInput.closest('.colis-item');
        const prixUnitaireInput = colisItem.querySelector('.prix-unitaire-input');
        const poids = parseFloat(poidsInput.value) || 0;
        
        if (!prixUnitaireInput) return;
        
        const devise = document.getElementById('devise').value;
        
        let prixUnitaire = 0;
        
        if (poids > 0) {
            if (devise === 'EUR') {
                prixUnitaire = poids * PRIX_PAR_KG_AERIEN_EURO;
            } else {
                // Convertir en XOF
                prixUnitaire = poids * PRIX_PAR_KG_AERIEN_EURO * TAUX_CONVERSION_EURO_XOF;
            }
        }
        
        prixUnitaireInput.value = prixUnitaire.toFixed(2);
        
        updateMontants();
        
        if (currentStep === 7) {
            updateRecap();
        }
    }

    // NOUVELLE FONCTION : Gérer l'affichage du message d'information
    function toggleInfoCalculAuto() {
        const modeTransit = document.getElementById('mode_transit').value;
        // Créer un élément d'information si nécessaire
        let infoDiv = document.getElementById('info-calcul-auto');
        
        if (!infoDiv) {
            infoDiv = document.createElement('div');
            infoDiv.className = 'calcul-auto-info mb-4';
            infoDiv.id = 'info-calcul-auto';
            infoDiv.innerHTML = `
                <i class="fas fa-calculator me-2"></i>
                <strong>Calcul automatique :</strong> En mode aérien, le prix est calculé automatiquement à raison de 
                <strong id="prix-kg-display">15 EUR par kg</strong>. Le prix sera converti en XOF si nécessaire.
            `;
            
            const colisContainer = document.getElementById('colis-container');
            colisContainer.parentNode.insertBefore(infoDiv, colisContainer);
        }
        
        if (modeTransit === 'Aerien') {
            infoDiv.classList.remove('d-none');
            updatePrixKgDisplay();
        } else {
            infoDiv.classList.add('d-none');
        }
    }

    // NOUVELLE FONCTION : Mettre à jour l'affichage du prix par kg
    function updatePrixKgDisplay() {
        const modeTransit = document.getElementById('mode_transit').value;
        const prixKgDisplay = document.getElementById('prix-kg-display');
        const devise = document.getElementById('devise').value;
        
        if (modeTransit === 'Aerien' && prixKgDisplay) {
            if (devise === 'EUR') {
                prixKgDisplay.textContent = '15 EUR par kg';
            } else {
                prixKgDisplay.textContent = `${(15 * TAUX_CONVERSION_EURO_XOF).toFixed(0)} XOF par kg`;
            }
        }
    }

    function updateMontants() {
        let montantColis = 0;
        const modeTransit = document.getElementById('mode_transit').value;
        
        // Calculer le montant total des colis
        document.querySelectorAll('.colis-item').forEach(item => {
            const index = item.dataset.index;
            const quantite = parseFloat(item.querySelector(`[name="colis[${index}][quantite]"]`).value) || 0;
            const prixUnitaire = parseFloat(item.querySelector(`[name="colis[${index}][prix_unitaire]"]`).value) || 0;
            
            // MODIFICATION : Calcul différent selon le mode de transit
            if (modeTransit === 'Aerien') {
                // En mode aérien, le prix unitaire est déjà le total pour le colis (calculé par poids)
                montantColis += prixUnitaire;
            } else {
                // En mode maritime, on multiplie par la quantité
                montantColis += quantite * prixUnitaire;
            }
        });
        
        const prixService = parseFloat(document.getElementById('prix_service').value) || 0;
        const montantTotal = montantColis + prixService;
        
        // Mettre à jour l'affichage
        document.getElementById('montant_colis_display').textContent = montantColis.toFixed(2);
        document.getElementById('montant_service_display').textContent = prixService.toFixed(2);
        document.getElementById('montant_total_display').textContent = montantTotal.toFixed(2);
        
        // Mettre à jour les champs cachés pour le formulaire
        document.getElementById('montant_colis_input').value = montantColis;
        document.getElementById('montant_total_input').value = montantTotal;

        // Mettre à jour le reste à payer
        updateResteAPayer();
        
        // Mettre à jour le récapitulatif si on est à l'étape 7
        if (currentStep === 7) {
            updateRecap();
        }
    }

    function updateResteAPayer() {
        const montantTotal = parseFloat(document.getElementById('montant_total_display').textContent) || 0;
        const montantPaye = parseFloat(document.getElementById('montant_paye').value) || 0;
        const resteAPayer = Math.max(0, montantTotal - montantPaye);
        
        document.getElementById('reste_a_payer').value = resteAPayer.toFixed(2);
        
        // Mettre à jour le statut de paiement
        const statutPaiement = document.getElementById('statut_paiement');
        if (montantPaye === 0) {
            statutPaiement.value = 'non_paye';
        } else if (montantPaye < montantTotal) {
            statutPaiement.value = 'partiellement_paye';
        } else {
            statutPaiement.value = 'totalement_paye';
        }
        
        // Mettre à jour le récapitulatif si on est à l'étape 7
        if (currentStep === 7) {
            updateRecap();
        }
    }

    function addColis() {
        const container = document.getElementById('colis-container');
        const newColis = document.querySelector('.colis-item').cloneNode(true);
        
        newColis.dataset.index = colisCount;
        newColis.querySelector('.card-header h6').textContent = `Colis #${colisCount + 1}`;
        
        newColis.querySelectorAll('input, select, textarea').forEach(field => {
            const name = field.getAttribute('name');
            if (name) {
                field.setAttribute('name', name.replace('[0]', `[${colisCount}]`));
                if (field.type !== 'select-one') {
                    field.value = '';
                }
            }
        });
        
        newColis.querySelector('.remove-colis').classList.remove('d-none');
        container.appendChild(newColis);
        colisCount++;
        
        // Mettre à jour le type de calcul pour le nouveau colis
        updateTypeCalcul();
        
        // Mettre à jour les montants
        updateMontants();
    }

    // Fonction pour mettre à jour toutes les devises affichées
    function updateAllDevises(devise) {
        console.log('🔄 Mise à jour des devises:', devise);
        
        // Mettre à jour tous les éléments avec la classe .devise-dynamic
        document.querySelectorAll('.devise-dynamic').forEach(element => {
            element.textContent = devise;
        });
        
        // Mettre à jour les options des services (si nécessaire)
        document.querySelectorAll('#service_id option').forEach(option => {
            if (option.textContent.includes(' - ')) {
                const parts = option.textContent.split(' - ');
                if (parts.length >= 2) {
                    // Garder la désignation et le prix, changer seulement la devise
                    const prixPart = parts[1].split(' ')[0]; // Prendre seulement le nombre
                    option.textContent = `${parts[0]} - ${prixPart} ${devise}`;
                }
            }
        });
        
        // Mettre à jour le récapitulatif si on est à l'étape 7
        if (currentStep === 7) {
            updateRecap();
        }
    }

    function updateRecap() {
        console.log('🔄 Mise à jour du récapitulatif...');
        
        // Récupérer toutes les données du formulaire
        const modeTransit = document.getElementById('mode_transit').value;
        const agenceExpedition = document.getElementById('agence_expedition_display').textContent;
        const agenceDestination = document.getElementById('agence_destination').value;
        const devise = document.getElementById('devise').value;
        const reference = document.getElementById('reference_display').textContent;
        const conteneur = document.getElementById('current_conteneur_name').textContent;

        // Expéditeur
        const typeExpediteur = document.getElementById('type_expediteur').value;
        const nameExpediteur = document.getElementById('name_expediteur').value;
        const prenomExpediteur = document.getElementById('prenom_expediteur').value;
        const emailExpediteur = document.getElementById('email_expediteur').value;
        const contactExpediteur = document.getElementById('contact_expediteur').value;
        const adresseExpediteur = document.getElementById('adresse_expediteur').value;

        // Destinataire
        const nameDestinataire = document.getElementById('name_destinataire').value;
        const prenomDestinataire = document.getElementById('prenom_destinataire').value;
        const emailDestinataire = document.getElementById('email_destinataire').value;
        const indicatif = document.getElementById('indicatif').value;
        const contactDestinataire = document.getElementById('contact_destinataire').value;
        const adresseDestinataire = document.getElementById('adresse_destinataire').value;

        // Services
        const serviceSelect = document.getElementById('service_id');
        const service = serviceSelect.options[serviceSelect.selectedIndex].textContent;
        const prixService = document.getElementById('prix_service').value;

        // Paiement
        const methodePaiement = document.getElementById('methode_paiement').value;
        const montantPaye = document.getElementById('montant_paye').value;
        const resteAPayer = document.getElementById('reste_a_payer').value;
        const statutPaiement = document.getElementById('statut_paiement').value;

        console.log('Données récupérées:', {
            modeTransit, agenceExpedition, agenceDestination, devise, reference, conteneur,
            nameExpediteur, prenomExpediteur, emailExpediteur, contactExpediteur,
            nameDestinataire, prenomDestinataire, emailDestinataire, contactDestinataire
        });

        // Mettre à jour les informations de transport
        document.getElementById('recap_mode_transit').textContent = modeTransit || '-';
        document.getElementById('recap_agence_expedition').textContent = agenceExpedition || '-';
        document.getElementById('recap_agence_destination').textContent = agenceDestination || '-';
        document.getElementById('recap_reference').textContent = reference || '-';
        document.getElementById('recap_conteneur').textContent = conteneur || '-';
        document.getElementById('recap_devise').textContent = devise || '-';

        // Mettre à jour l'expéditeur
        document.getElementById('recap_expediteur_nom').textContent = nameExpediteur || '-';
        document.getElementById('recap_expediteur_prenom').textContent = typeExpediteur === 'societe' ? 'Non applicable' : (prenomExpediteur || '-');
        document.getElementById('recap_expediteur_email').textContent = emailExpediteur || '-';
        document.getElementById('recap_expediteur_contact').textContent = contactExpediteur || '-';
        document.getElementById('recap_expediteur_adresse').textContent = adresseExpediteur || '-';

        // Mettre à jour le destinataire
        document.getElementById('recap_destinataire_nom').textContent = nameDestinataire || '-';
        document.getElementById('recap_destinataire_prenom').textContent = prenomDestinataire || '-';
        document.getElementById('recap_destinataire_email').textContent = emailDestinataire || '-';
        document.getElementById('recap_destinataire_contact').textContent = (indicatif || '') + ' ' + (contactDestinataire || '-');
        document.getElementById('recap_destinataire_adresse').textContent = adresseDestinataire || '-';

        // Mettre à jour les services
        document.getElementById('recap_service').textContent = service.includes('Aucun') ? 'Aucun service' : service;
        document.getElementById('recap_prix_service').textContent = (parseFloat(prixService) || 0).toFixed(2) + ' ' + devise;

        // Mettre à jour le paiement
        document.getElementById('recap_methode_paiement').textContent = getMethodePaiementText(methodePaiement);
        document.getElementById('recap_montant_total').textContent = document.getElementById('montant_total_display').textContent + ' ' + devise;
        document.getElementById('recap_montant_paye').textContent = (parseFloat(montantPaye) || 0).toFixed(2) + ' ' + devise;
        document.getElementById('recap_reste_payer').textContent = (parseFloat(resteAPayer) || 0).toFixed(2) + ' ' + devise;
        
        // Mettre à jour le statut de paiement avec badge coloré
        const statutBadge = document.getElementById('recap_statut_paiement');
        statutBadge.textContent = getStatutPaiementText(statutPaiement);
        statutBadge.className = 'badge ' + getStatutPaiementClass(statutPaiement);

        // Mettre à jour le tableau des colis
        updateColisTable();
        
        console.log('✅ Récapitulatif mis à jour');
    }

    function getMethodePaiementText(methode) {
        const methodes = {
            'espece': 'Espèce',
            'virement_bancaire': 'Virement Bancaire',
            'cheque': 'Chèque',
            'mobile_money': 'Mobile Money',
            'livraison': 'Paiement à la Livraison'
        };
        return methodes[methode] || '-';
    }

    function getStatutPaiementText(statut) {
        const statuts = {
            'non_paye': 'Non Payé',
            'partiellement_paye': 'Partiellement Payé',
            'totalement_paye': 'Totalement Payé'
        };
        return statuts[statut] || '-';
    }

    function getStatutPaiementClass(statut) {
        const classes = {
            'non_paye': 'bg-danger',
            'partiellement_paye': 'bg-warning',
            'totalement_paye': 'bg-success'
        };
        return classes[statut] || 'bg-secondary';
    }

    function updateColisTable() {
        const tbody = document.getElementById('recap_colis_body');
        tbody.innerHTML = '';
        
        let totalGeneral = 0;
        let nombreColis = 0;
        const modeTransit = document.getElementById('mode_transit').value;

        document.querySelectorAll('.colis-item').forEach((item, index) => {
            const quantite = parseFloat(item.querySelector(`[name="colis[${index}][quantite]"]`).value) || 0;
            const produit = item.querySelector(`[name="colis[${index}][produit]"]`).value || '-';
            const prixUnitaire = parseFloat(item.querySelector(`[name="colis[${index}][prix_unitaire]"]`).value) || 0;
            const longueur = item.querySelector(`[name="colis[${index}][longueur]"]`).value || '-';
            const largeur = item.querySelector(`[name="colis[${index}][largeur]"]`).value || '-';
            const hauteur = item.querySelector(`[name="colis[${index}][hauteur]"]`).value || '-';
            const poids = item.querySelector(`[name="colis[${index}][poids]"]`).value || '-';
            const typeColisInput = item.querySelector(`[name="colis[${index}][type_colis]"]`);
            const typeColis = typeColisInput?.value || 'Standard';
            // MODIFICATION : Calcul différent selon le mode de transit
            let totalColis = 0;
            if (modeTransit === 'Aerien') {
                totalColis = prixUnitaire; // En aérien, le prix unitaire est déjà le total pour le colis
            } else {
                totalColis = quantite * prixUnitaire; // En maritime, on multiplie par la quantité
            }
            
            totalGeneral += totalColis;
            nombreColis++;

            const dimensions = (longueur !== '-' && largeur !== '-' && hauteur !== '-') 
                ? `${longueur}x${largeur}x${hauteur} cm` 
                : 'Non spécifié';

            const row = document.createElement('tr');
            
            // MODIFICATION : Affichage différent selon le mode de transit
            if (modeTransit === 'Aerien') {
                row.innerHTML = `
                    <td class="fw-bold">${index + 1}</td>
                    <td>${produit}</td>
                    <td>${quantite}</td>
                    <td>${prixUnitaire.toFixed(2)}</td>
                    <td class="fw-bold">${totalColis.toFixed(2)}</td>
                    <td>${typeColis}</td>
                    <td>${dimensions}</td>
                    <td>${poids !== '-' ? poids + ' kg' : '-'}</td>
                `;
            } else {
                row.innerHTML = `
                    <td class="fw-bold">${index + 1}</td>
                    <td>${produit}</td>
                    <td>${quantite}</td>
                    <td>${prixUnitaire.toFixed(2)}</td>
                    <td class="fw-bold">${totalColis.toFixed(2)}</td>
                    <td>${typeColis}</td>
                    <td>${dimensions}</td>
                    <td>${poids !== '-' ? poids + ' kg' : '-'}</td>
                `;
            }
            
            tbody.appendChild(row);
        });

        // Mettre à jour les totaux
        const devise = document.getElementById('devise').value || 'XOF';
        document.getElementById('recap_total_colis').textContent = totalGeneral.toFixed(2) + ' ' + devise;
        document.getElementById('recap_nombre_colis').textContent = nombreColis + ' colis' + (nombreColis > 1 ? 's' : '');
    }

    function validateStep(step) {
        let isValid = true;
        
        if (step === 1) {
            const modeTransit = document.getElementById('mode_transit').value;
            const agenceExpedition = document.getElementById('agence_expedition_id').value;
            
            if (!modeTransit || !agenceExpedition) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Champs obligatoires',
                    text: 'Veuillez sélectionner le mode de transit et l\'agence d\'expédition',
                    confirmButtonColor: '#0e914b'
                });
                isValid = false;
            }
        }
        
        if (step === 2) {
            const nameExpediteur = document.getElementById('name_expediteur').value;
            const contactExpediteur = document.getElementById('contact_expediteur').value;
            
            if (!nameExpediteur || !contactExpediteur) { // Retirer email_expediteur
                Swal.fire({
                    icon: 'warning',
                    title: 'Champs obligatoires',
                    text: 'Veuillez remplir tous les champs obligatoires de l\'expéditeur',
                    confirmButtonColor: '#0e914b'
                });
                isValid = false;
            }
        }
        
        if (step === 3) {
            const nameDestinataire = document.getElementById('name_destinataire').value;
            const prenomDestinataire = document.getElementById('prenom_destinataire').value;
            const contactDestinataire = document.getElementById('contact_destinataire').value;
            
            if (!nameDestinataire || !prenomDestinataire || !contactDestinataire) { // Retirer email_destinataire
                Swal.fire({
                    icon: 'warning',
                    title: 'Champs obligatoires',
                    text: 'Veuillez remplir tous les champs obligatoires du destinataire',
                    confirmButtonColor: '#0e914b'
                });
                isValid = false;
            }
        }
        
        if (step === 4) {
            const colisItems = document.querySelectorAll('.colis-item');
            for (let i = 0; i < colisItems.length; i++) {
                const quantite = colisItems[i].querySelector(`input[name="colis[${i}][quantite]"]`).value;
                const produit = colisItems[i].querySelector(`input[name="colis[${i}][produit]"]`).value;
                const prixUnitaire = colisItems[i].querySelector(`input[name="colis[${i}][prix_unitaire]"]`).value;
                
                if (!quantite || !produit || !prixUnitaire) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Champs obligatoires',
                        text: 'Veuillez remplir tous les champs obligatoires pour le colis #' + (i + 1),
                        confirmButtonColor: '#0e914b'
                    });
                    isValid = false;
                    break;
                }
            }
        }

        if (step === 6) {
            const methodePaiement = document.getElementById('methode_paiement').value;
            const montantPaye = parseFloat(document.getElementById('montant_paye').value) || 0;
            
            if (!methodePaiement) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Méthode de paiement requise',
                    text: 'Veuillez sélectionner une méthode de paiement',
                    confirmButtonColor: '#0e914b'
                });
                isValid = false;
            }
            
            // Validation du montant payé
            if (montantPaye < 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Montant invalide',
                    text: 'Le montant payé ne peut pas être négatif',
                    confirmButtonColor: '#0e914b'
                });
                isValid = false;
            }
        }
        
        return isValid;
    }

    function validateAllSteps() {
        console.log('🔍 Validation de toutes les étapes...');
        
        const stepsToValidate = [1, 2, 3, 4, 6]; // Étape 5 et 7 sont optionnelles
        
        for (let step of stepsToValidate) {
            if (!validateStep(step)) {
                console.log(`❌ Étape ${step} a échoué`);
                showStep(step);
                return false;
            }
            console.log(`✅ Étape ${step} validée`);
        }
        
        console.log('🎉 Toutes les étapes sont valides');
        return true;
    }

    // Écouter les changements dans les champs de colis pour mettre à jour les montants
    document.addEventListener('input', function(e) {
        // Champs quantité et prix unitaire
        if (e.target.name && (e.target.name.includes('[quantite]') || e.target.name.includes('[prix_unitaire]'))) {
            updateMontants();
        }
        
        // Champs poids en mode aérien
        if (e.target.name && e.target.name.includes('[poids]')) {
            const modeTransit = document.getElementById('mode_transit').value;
            if (modeTransit === 'Aerien') {
                calculerPrixAuto(e.target);
            }
        }
        
        // Champs dimensions
        if (e.target.name && (e.target.name.includes('[longueur]') || e.target.name.includes('[largeur]') || 
            e.target.name.includes('[hauteur]'))) {
            if (currentStep === 7) {
                updateRecap();
            }
        }
    });

    // Initialisation
    showStep(1);
    updateExpediteurFields();
    updateMontants();
    updateTypeCalcul();
    toggleInfoCalculAuto();
    
    // Initialiser les écouteurs pour les champs de formulaire
    document.addEventListener('input', function(e) {
        if (currentStep === 7 && (
            e.target.id === 'name_expediteur' || e.target.id === 'prenom_expediteur' ||
            e.target.id === 'email_expediteur' || e.target.id === 'contact_expediteur' ||
            e.target.id === 'adresse_expediteur' || e.target.id === 'name_destinataire' ||
            e.target.id === 'prenom_destinataire' || e.target.id === 'email_destinataire' ||
            e.target.id === 'contact_destinataire' || e.target.id === 'adresse_destinataire' ||
            e.target.id === 'indicatif'
        )) {
            updateRecap();
        }
    });
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