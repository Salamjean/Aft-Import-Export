@extends('admin.layouts.template')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card modern-card">
                <div class="card-header modern-header">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="header-text">
                            <h3 class="card-title">Modifier le Colis</h3>
                            <p class="card-subtitle">Modifiez les informations du colis étape par étape</p>
                        </div>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('colis.index') }}" class="btn modern-btn text-white" style="background-color:#6c757d; margin-right: 10px;">
                            <i class="fas fa-arrow-left"></i>
                            Retour
                        </a>
                        <button onclick="downloadColisPDF()" class="btn modern-btn text-white" style="background-color:#dc3545; margin-right: 10px;">
                            <i class="fas fa-file-pdf"></i>
                            Télécharger PDF
                        </button>
                        <a href="{{ route('colis.create') }}" class="btn modern-btn text-white" style="background-color:#0e914b">
                            <i class="fas fa-plus"></i>
                            Nouveau Colis
                        </a>
                    </div>
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
                                            <strong>Nom:</strong> <span id="current_conteneur_name">{{ $colis->conteneur->name_conteneur ?? 'Non spécifié' }}</span> | 
                                            <strong>Type:</strong> <span id="current_conteneur_type">{{ $colis->conteneur->type_conteneur ?? 'Non spécifié' }}</span> | 
                                            <strong>Statut:</strong> 
                                            <span class="badge bg-success text-white">{{ $colis->conteneur->statut ?? 'Actif' }}</span>
                                        </p>
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
                                                style="background-color: #f8f9fa;"
                                                value="{{ $colis->agence_destination }}">
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
                                                    <option value="{{ $agence->id }}" 
                                                            data-devise="{{ $agence->devise }}"
                                                            {{ $colis->agence_expedition_id == $agence->id ? 'selected' : '' }}>
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
                                                <option value="">Sélectionnez un type</option>
                                                <option value="particulier" {{ $colis->prenom_expediteur ? 'selected' : '' }}>Particulier</option>
                                                <option value="societe" {{ !$colis->prenom_expediteur ? 'selected' : '' }}>Société</option>
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
                                        <input type="text" class="modern-input" id="name_expediteur" name="name_expediteur" 
                                               value="{{ $colis->name_expediteur }}" required>
                                    </div>
                                </div>

                                <div class="col-md-3" id="prenom_field">
                                    <div class="form-group">
                                        <label for="prenom_expediteur" class="form-label">Prénom</label>
                                        <input type="text" class="modern-input" id="prenom_expediteur" name="prenom_expediteur"
                                               value="{{ $colis->prenom_expediteur }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="email_expediteur" class="form-label required">Email</label>
                                        <input type="email" class="modern-input" id="email_expediteur" name="email_expediteur" 
                                               value="{{ $colis->email_expediteur }}" required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="contact_expediteur" class="form-label required">Contact</label>
                                        <input type="text" class="modern-input" id="contact_expediteur" name="contact_expediteur" 
                                               value="{{ $colis->contact_expediteur }}" required>
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
                                        <input type="text" class="modern-input" id="name_destinataire" name="name_destinataire" 
                                               value="{{ $colis->name_destinataire }}" required>
                                    </div>
                                </div>

                                <!-- Colonne 2 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="prenom_destinataire" class="form-label required">Prénom</label>
                                        <input type="text" class="modern-input" id="prenom_destinataire" name="prenom_destinataire" 
                                               value="{{ $colis->prenom_destinataire }}" required>
                                    </div>
                                </div>

                                <!-- Colonne 3 -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="email_destinataire" class="form-label required">Email</label>
                                        <input type="email" class="modern-input" id="email_destinataire" name="email_destinataire" 
                                               value="{{ $colis->email_destinataire }}" required>
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
                                        <input type="text" class="modern-input" id="contact_destinataire" name="contact_destinataire" 
                                               value="{{ $colis->contact_destinataire }}" required>
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
                                <p class="text-muted">Ajoutez les informations sur vos colis à expédier</p>
                            </div>

                            <div id="colis-container">
                                @php
                                    $colisDetails = json_decode($colis->colis, true) ?? [];
                                @endphp
                                
                                @foreach($colisDetails as $index => $detail)
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
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold required">Quantité</label>
                                                <input type="number" class="form-control" name="colis[{{ $index }}][quantite]" 
                                                       value="{{ $detail['quantite'] ?? 1 }}" min="1" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold required">Produit</label>
                                                <div class="search-container position-relative">
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0">
                                                            <i class="fas fa-search" style="color: #fea219;"></i>
                                                        </span>
                                                        <input type="text" class="form-control border-start-0 produit-input" 
                                                            name="colis[{{ $index }}][produit]" 
                                                            value="{{ $detail['produit'] ?? '' }}"
                                                            placeholder="Rechercher un produit..." required>
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
                                                <input type="number" class="form-control prix-unitaire-input" 
                                                       name="colis[{{ $index }}][prix_unitaire]" 
                                                       value="{{ $detail['prix_unitaire'] ?? 0 }}"
                                                       step="0.01" min="0" required>
                                            </div>
                                        </div>

                                        <div class="row g-3 mt-3">
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Longueur (cm)</label>
                                                <input type="number" class="form-control" name="colis[{{ $index }}][longueur]" 
                                                       value="{{ $detail['longueur'] ?? '' }}" step="0.1" min="0">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Largeur (cm)</label>
                                                <input type="number" class="form-control" name="colis[{{ $index }}][largeur]" 
                                                       value="{{ $detail['largeur'] ?? '' }}" step="0.1" min="0">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Hauteur (cm)</label>
                                                <input type="number" class="form-control" name="colis[{{ $index }}][hauteur]" 
                                                       value="{{ $detail['hauteur'] ?? '' }}" step="0.1" min="0">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Poids (kg)</label>
                                                <input type="number" class="form-control" name="colis[{{ $index }}][poids]" 
                                                       value="{{ $detail['poids'] ?? '' }}" step="0.1" min="0">
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <label class="form-label fw-bold">Description</label>
                                            <textarea class="form-control" name="colis[{{ $index }}][description]" 
                                                      rows="2" placeholder="Description du produit...">{{ $detail['description'] ?? '' }}</textarea>
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
                                                    <option value="{{ $service->id }}" 
                                                            data-prix="{{ $service->prix_unitaire }}"
                                                            {{ $colis->service_id == $service->id ? 'selected' : '' }}>
                                                        {{ $service->designation }} - {{ $service->prix_unitaire }} XOF
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
                                               value="{{ $colis->prix_service ?? 0 }}"
                                               readonly step="0.01" min="0" placeholder="Sélectionnez un service">
                                    </div>

                                    <div class="mt-4 p-3 bg-light rounded">
                                        <h6 class="fw-bold" style="color: #0e914b;">Résumé des Coûts</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Montant Colis:</strong>
                                            </div>
                                            <div class="col-6 text-end">
                                                <span id="montant_colis_display">{{ number_format($colis->montant_colis ?? 0, 2) }}</span> <span id="devise_colis_display">{{ $colis->devise }}</span>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-6">
                                                <strong>Service:</strong>
                                            </div>
                                            <div class="col-6 text-end">
                                                <span id="montant_service_display">{{ number_format($colis->prix_service ?? 0, 2) }}</span> <span id="devise_service_display">XOF</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Total:</strong>
                                            </div>
                                            <div class="col-6 text-end">
                                                <span id="montant_total_display" class="fw-bold">{{ number_format($colis->montant_total ?? 0, 2) }}</span> <span id="devise_total_display">{{ $colis->devise }}</span>
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
                                                    value="{{ $colis->montant_paye ?? 0 }}"
                                                    step="0.01" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reste_a_payer" class="form-label">Reste à Payer</label>
                                                <input type="number" class="modern-input" id="reste_a_payer" name="reste_a_payer" 
                                                    value="{{ $colis->reste_a_payer ?? 0 }}"
                                                    readonly step="0.01" min="0">
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
                                                placeholder="Notes supplémentaires concernant le paiement...">{{ $colis->notes_paiement ?? '' }}</textarea>
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
                            <!-- Même contenu que la page create -->
                            <!-- ... -->
                            
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
                                    <i class="fas fa-save me-2"></i>Mettre à jour le Colis
                                </button>
                            </div>
                        </div>

                    <input type="hidden" name="montant_colis" id="montant_colis_input" value="{{ $colis->montant_colis ?? 0 }}">
                    <input type="hidden" name="montant_total" id="montant_total_input" value="{{ $colis->montant_total ?? 0 }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inclure les mêmes modales que dans create.blade.php -->
<!-- ... -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    let colisCount = {{ count($colisDetails ?? []) }};
    let searchTimeout;
    let produitSearchTimeout;
    let currentProduitIndex = 0;

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

    // Templates pour les différentes méthodes de paiement
    const paiementTemplates = {
        espece: `
            <div class="paiement-method-fields">
                <div class="form-group">
                    <label class="form-label required">Montant en Espèce</label>
                    <input type="number" class="modern-input" name="montant_espece" 
                           value="{{ $colis->montant_espece ?? 0 }}"
                           step="0.01" min="0" required>
                    <small class="text-muted">Entrez le montant reçu en espèces</small>
                </div>
            </div>
        `,
        
        virement_bancaire: `
            <div class="paiement-method-fields">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required">Nom de la Banque</label>
                            <input type="text" class="modern-input" name="nom_banque" 
                                   value="{{ $colis->nom_banque ?? '' }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required">Numéro de Compte</label>
                            <input type="text" class="modern-input" name="numero_compte" 
                                   value="{{ $colis->numero_compte ?? '' }}" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label required">Montant du Virement</label>
                    <input type="number" class="modern-input" name="montant_virement" 
                           value="{{ $colis->montant_virement ?? 0 }}"
                           step="0.01" min="0" required>
                </div>
            </div>
        `,
        
        cheque: `
            <div class="paiement-method-fields">
                <div class="form-group">
                    <label class="form-label required">Montant du Chèque</label>
                    <input type="number" class="modern-input" name="montant_cheque" 
                           value="{{ $colis->montant_cheque ?? 0 }}"
                           step="0.01" min="0" required>
                    <small class="text-muted">Entrez le montant du chèque</small>
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
                                <option value="WAVE" {{ ($colis->operateur_mobile_money ?? '') == 'WAVE' ? 'selected' : '' }}>WAVE</option>
                                <option value="ORANGE" {{ ($colis->operateur_mobile_money ?? '') == 'ORANGE' ? 'selected' : '' }}>ORANGE</option>
                                <option value="MOOV" {{ ($colis->operateur_mobile_money ?? '') == 'MOOV' ? 'selected' : '' }}>MOOV</option>
                                <option value="MTN" {{ ($colis->operateur_mobile_money ?? '') == 'MTN' ? 'selected' : '' }}>MTN</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required">Numéro de Téléphone</label>
                            <input type="text" class="modern-input" name="numero_mobile_money" 
                                   value="{{ $colis->numero_mobile_money ?? '' }}" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label required">Montant Mobile Money</label>
                    <input type="number" class="modern-input" name="montant_mobile_money" 
                           value="{{ $colis->montant_mobile_money ?? 0 }}"
                           step="0.01" min="0" required>
                </div>
            </div>
        `,
        
        livraison: `
            <div class="paiement-method-fields">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Le paiement sera effectué à la livraison du colis.
                </div>
                <input type="hidden" name="montant_livraison" value="{{ $colis->montant_livraison ?? 0 }}">
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
    });

    // Gestion de l'agence d'expédition (devise)
    document.getElementById('agence_expedition_id').addEventListener('change', function() {
        updateDeviseExpedition();
        updateAgenceDestination();
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

    // Gestion des boutons d'ajout de produit
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-produit-btn')) {
            currentProduitIndex = e.target.closest('.add-produit-btn').getAttribute('data-index');
            $('#addProduitModal').modal('show');
        }
    });

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
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mise à jour...';
        
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
    }

    function updateDeviseExpedition() {
        const agenceSelect = document.getElementById('agence_expedition_id');
        const selectedOption = agenceSelect.options[agenceSelect.selectedIndex];
        const devise = selectedOption.getAttribute('data-devise');
        
        if (devise) {
            document.getElementById('devise_expedition_display').textContent = devise;
            document.getElementById('devise_expedition_info').textContent = 'Devise de l\'agence sélectionnée';
            document.getElementById('devise').value = devise;
        } else {
            document.getElementById('devise_expedition_display').textContent = '-';
            document.getElementById('devise_expedition_info').textContent = 'Sélectionnez une agence';
            document.getElementById('devise').value = '';
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
    }

    function updateMontants() {
        let montantColis = 0;
        
        // Calculer le montant total des colis
        document.querySelectorAll('.colis-item').forEach(item => {
            const index = item.dataset.index;
            const quantite = parseFloat(item.querySelector(`[name="colis[${index}][quantite]"]`).value) || 0;
            const prixUnitaire = parseFloat(item.querySelector(`[name="colis[${index}][prix_unitaire]"]`).value) || 0;
            
            montantColis += quantite * prixUnitaire;
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
    }

    function searchUsers(searchTerm) {
        fetch(`/admin/parcel/search?q=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
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
        // Pré-remplir les champs de l'expéditeur
        document.getElementById('name_expediteur').value = user.name;
        document.getElementById('prenom_expediteur').value = user.prenom || '';
        document.getElementById('email_expediteur').value = user.email;
        document.getElementById('contact_expediteur').value = user.contact || '';
        document.getElementById('adresse_expediteur').value = user.adresse || '';
        
        // Cacher les résultats de recherche
        hideSearchResults();
        
        // Vider le champ de recherche
        document.getElementById('userSearch').value = '';
        
        // Afficher un message de confirmation
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
        
        fetch(`/admin/produits/search?q=${encodeURIComponent(searchTerm)}&agence_destination_id=${agenceDestinationId}`)
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
                        ${produit.prix_unitaire} XOF • ${produit.agence_destination?.name || 'Agence non spécifiée'}
                    </div>
                `;
                produitElement.addEventListener('click', () => selectProduit(produit, currentIndex));
                resultsContainer.appendChild(produitElement);
            });
        }
        
        resultsContainer.style.display = 'block';
    }

    function selectProduit(produit, index) {
        const colisItem = document.querySelector(`.colis-item[data-index="${index}"]`);
        if (colisItem) {
            const produitInput = colisItem.querySelector(`[name="colis[${index}][produit]"]`);
            const prixInput = colisItem.querySelector(`[name="colis[${index}][prix_unitaire]"]`);
            
            produitInput.value = produit.designation;
            prixInput.value = produit.prix_unitaire;
            
            hideProduitResults(index);
            
            // Mettre à jour les montants
            updateMontants();
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

    function validateStep(step) {
        let isValid = true;
        let errorMessage = '';
        
        if (step === 1) {
            const modeTransit = document.getElementById('mode_transit').value;
            const agenceExpedition = document.getElementById('agence_expedition_id').value;
            
            if (!modeTransit) {
                errorMessage = 'Veuillez sélectionner le mode de transit';
                isValid = false;
            } else if (!agenceExpedition) {
                errorMessage = 'Veuillez sélectionner l\'agence d\'expédition';
                isValid = false;
            }
        }
        
        if (step === 2) {
            const nameExpediteur = document.getElementById('name_expediteur').value;
            const emailExpediteur = document.getElementById('email_expediteur').value;
            const contactExpediteur = document.getElementById('contact_expediteur').value;
            
            if (!nameExpediteur) {
                errorMessage = 'Veuillez renseigner le nom de l\'expéditeur';
                isValid = false;
            } else if (!emailExpediteur) {
                errorMessage = 'Veuillez renseigner l\'email de l\'expéditeur';
                isValid = false;
            } else if (!contactExpediteur) {
                errorMessage = 'Veuillez renseigner le contact de l\'expéditeur';
                isValid = false;
            }
        }
        
        if (step === 3) {
            const nameDestinataire = document.getElementById('name_destinataire').value;
            const prenomDestinataire = document.getElementById('prenom_destinataire').value;
            const emailDestinataire = document.getElementById('email_destinataire').value;
            const contactDestinataire = document.getElementById('contact_destinataire').value;
            
            if (!nameDestinataire) {
                errorMessage = 'Veuillez renseigner le nom du destinataire';
                isValid = false;
            } else if (!prenomDestinataire) {
                errorMessage = 'Veuillez renseigner le prénom du destinataire';
                isValid = false;
            } else if (!emailDestinataire) {
                errorMessage = 'Veuillez renseigner l\'email du destinataire';
                isValid = false;
            } else if (!contactDestinataire) {
                errorMessage = 'Veuillez renseigner le contact du destinataire';
                isValid = false;
            }
        }
        
        if (step === 4) {
            const colisItems = document.querySelectorAll('.colis-item');
            let hasError = false;
            
            for (let i = 0; i < colisItems.length; i++) {
                const quantite = colisItems[i].querySelector(`input[name="colis[${i}][quantite]"]`).value;
                const produit = colisItems[i].querySelector(`input[name="colis[${i}][produit]"]`).value;
                const prixUnitaire = colisItems[i].querySelector(`input[name="colis[${i}][prix_unitaire]"]`).value;
                
                if (!quantite || quantite <= 0) {
                    errorMessage = `Veuillez renseigner une quantité valide pour le colis #${i + 1}`;
                    hasError = true;
                    break;
                } else if (!produit) {
                    errorMessage = `Veuillez renseigner le produit pour le colis #${i + 1}`;
                    hasError = true;
                    break;
                } else if (!prixUnitaire || prixUnitaire <= 0) {
                    errorMessage = `Veuillez renseigner un prix unitaire valide pour le colis #${i + 1}`;
                    hasError = true;
                    break;
                }
            }
            
            isValid = !hasError;
        }

        if (step === 6) {
            const methodePaiement = document.getElementById('methode_paiement').value;
            const montantPaye = parseFloat(document.getElementById('montant_paye').value) || 0;
            
            if (!methodePaiement) {
                errorMessage = 'Veuillez sélectionner une méthode de paiement';
                isValid = false;
            } else if (montantPaye < 0) {
                errorMessage = 'Le montant payé ne peut pas être négatif';
                isValid = false;
            }
        }
        
        if (!isValid && errorMessage) {
            Swal.fire({
                icon: 'warning',
                title: 'Champs obligatoires',
                text: errorMessage,
                confirmButtonColor: '#0e914b'
            });
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

    // Fonction pour pré-remplir les données en mode édition
    function prefillFormData() {
        console.log('🔄 Pré-remplissage des données en mode édition...');
        
        // Étape 1: Transport
        if (document.getElementById('mode_transit')) {
            document.getElementById('mode_transit').value = '{{ $colis->mode_transit }}';
            document.getElementById('agence_expedition_id').value = '{{ $colis->agence_expedition_id }}';
            document.getElementById('agence_destination').value = '{{ $colis->agence_destination }}';
            document.getElementById('devise').value = '{{ $colis->devise }}';
            document.getElementById('devise_expedition_display').textContent = '{{ $colis->devise }}';
            
            // Déclencher les événements de changement
            const modeTransitEvent = new Event('change');
            document.getElementById('mode_transit').dispatchEvent(modeTransitEvent);
            
            const agenceEvent = new Event('change');
            document.getElementById('agence_expedition_id').dispatchEvent(agenceEvent);
        }
        
        // Étape 2: Expéditeur
        if (document.getElementById('name_expediteur')) {
            document.getElementById('name_expediteur').value = '{{ $colis->name_expediteur }}';
            document.getElementById('prenom_expediteur').value = '{{ $colis->prenom_expediteur ?? '' }}';
            document.getElementById('email_expediteur').value = '{{ $colis->email_expediteur }}';
            document.getElementById('contact_expediteur').value = '{{ $colis->contact_expediteur }}';
            document.getElementById('adresse_expediteur').value = '{{ $colis->adresse_expediteur }}';
            
            // Déterminer le type d'expéditeur
            const typeExpediteur = '{{ $colis->prenom_expediteur ? 'particulier' : 'societe' }}';
            document.getElementById('type_expediteur').value = typeExpediteur;
            updateExpediteurFields();
        }
        
        // Étape 3: Destinataire
        if (document.getElementById('name_destinataire')) {
            document.getElementById('name_destinataire').value = '{{ $colis->name_destinataire }}';
            document.getElementById('prenom_destinataire').value = '{{ $colis->prenom_destinataire }}';
            document.getElementById('email_destinataire').value = '{{ $colis->email_destinataire }}';
            document.getElementById('indicatif').value = '{{ $colis->indicatif }}';
            document.getElementById('contact_destinataire').value = '{{ $colis->contact_destinataire }}';
            document.getElementById('adresse_destinataire').value = '{{ $colis->adresse_destinataire }}';
        }
        
        // Étape 5: Services
        if (document.getElementById('service_id')) {
            document.getElementById('service_id').value = '{{ $colis->service_id ?? '' }}';
            document.getElementById('prix_service').value = '{{ $colis->prix_service ?? 0 }}';
            
            // Déclencher l'événement change pour le service
            const serviceEvent = new Event('change');
            document.getElementById('service_id').dispatchEvent(serviceEvent);
        }
        
        // Étape 6: Paiement
        if (document.getElementById('methode_paiement')) {
            document.getElementById('methode_paiement').value = '{{ $colis->methode_paiement }}';
            document.getElementById('montant_paye').value = '{{ $colis->montant_paye ?? 0 }}';
            document.getElementById('reste_a_payer').value = '{{ $colis->reste_a_payer ?? 0 }}';
            document.getElementById('statut_paiement').value = '{{ $colis->statut_paiement }}';
            document.getElementById('notes_paiement').value = '{{ $colis->notes_paiement ?? '' }}';
            
            // Déclencher l'événement change pour afficher les champs de paiement
            const methodePaiementEvent = new Event('change');
            document.getElementById('methode_paiement').dispatchEvent(methodePaiementEvent);
        }
        
        console.log('✅ Pré-remplissage terminé');
    }

    // Écouter les changements dans les champs de colis pour mettre à jour les montants
    document.addEventListener('input', function(e) {
        if (e.target.name && (e.target.name.includes('[quantite]') || e.target.name.includes('[prix_unitaire]'))) {
            updateMontants();
        }
    });

    // Initialisation
    showStep(1);
    updateExpediteurFields();
    updateMontants();

    // En mode édition, pré-remplir les données après un délai
    setTimeout(() => {
        prefillFormData();
        updateMontants(); // Recalculer les montants après pré-remplissage
    }, 500);
});
</script>

<style>
.header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .header-actions {
        justify-content: center;
        margin-top: 15px;
    }
    
    .header-actions .btn {
        margin-bottom: 5px;
    }
}
</style>
@endsection