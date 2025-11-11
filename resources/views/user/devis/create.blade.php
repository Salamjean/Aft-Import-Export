@extends('user.layouts.template')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-lg border-0" style="border-radius: 20px;">
                <!-- En-tête avec dégradé -->
                <div class="card-header text-white py-4" style="
                    background: linear-gradient(135deg, #0e914b 0%, #0b7a3d 100%);
                    border-radius: 20px 20px 0 0 !important;
                ">
                    <div class="text-center">
                        <div class="bg-white bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 70px; height: 70px;">
                            <i class="fas fa-file-invoice-dollar fa-2x text-white"></i>
                        </div>
                        <h3 class="card-title mb-2 fw-bold">Demande de Tarification</h3>
                        <p class="mb-0 opacity-90">Remplissez les informations pour obtenir votre tarification personnalisée</p>
                    </div>
                </div>

                <div class="card-body p-5">
                    <!-- Barre de progression stylisée -->
                    <div class="progress-container mb-5">
                        <div class="progress" style="height: 10px; border-radius: 10px; background-color: #f8f9fa;">
                            <div class="progress-bar" role="progressbar" style="width: 0%; border-radius: 10px; background: linear-gradient(135deg, #fea219 0%, #e69100 100%);" 
                                 id="progress-bar"></div>
                        </div>
                    </div>

                    <!-- Indicateurs d'étapes modernes -->
                    <div class="steps-indicator mb-5">
                        <div class="d-flex justify-content-between position-relative">
                            <!-- Ligne de connexion -->
                            <div class="step-connector" style="
                                position: absolute;
                                top: 20px;
                                left: 10%;
                                right: 10%;
                                height: 3px;
                                background: #e9ecef;
                                z-index: 1;
                            "></div>
                            
                            @foreach([1 => 'Transport', 2 => 'Informations', 3 => 'Colis', 4 => 'Récapitulatif'] as $step => $label)
                            <div class="text-center step-indicator position-relative z-2 {{ $step == 1 ? 'active' : '' }}" 
                                 data-step="{{ $step }}" style="flex: 1;">
                                <div class="step-number d-inline-flex align-items-center justify-content-center mb-3 rounded-circle"
                                     style="
                                         width: 50px;
                                         height: 50px;
                                         background: {{ $step == 1 ? 'linear-gradient(135deg, #fea219 0%, #e69100 100%)' : '#e9ecef' }};
                                         color: {{ $step == 1 ? 'white' : '#6c757d' }};
                                         font-weight: bold;
                                         font-size: 1.1rem;
                                         border: 3px solid white;
                                         box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                                         transition: all 0.3s ease;
                                     ">
                                    {{ $step }}
                                </div>
                                <div class="step-label small fw-medium" style="
                                    color: {{ $step == 1 ? '#0e914b' : '#6c757d' }};
                                    transition: all 0.3s ease;
                                ">
                                    {{ $label }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <form id="tarificationForm" action="{{ route('user.devis.store') }}" method="POST">
                        @csrf

                        <!-- Champs cachés pour les IDs des agences -->
                        <input type="hidden" id="agence_expedition_id" name="agence_expedition_id">
                        <input type="hidden" id="agence_destination_id" name="agence_destination_id">

                        <!-- Étape 1: Mode de transport -->
                        <div class="step-content" id="step-1">
                            <div class="step-header mb-4 text-center">
                                <div class="icon-container bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-shipping-fast fa-lg" style="color: #fea219;"></i>
                                </div>
                                <h4 class="mb-2 fw-bold" style="color: #0e914b;">Informations de Transport</h4>
                                <p class="text-muted">Sélectionnez le mode de transport et le pays d'expédition</p>
                            </div>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mode_transit" class="form-label fw-bold" style="color: #0e914b;">
                                            Mode de Transit <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-truck" style="color: #fea219;"></i>
                                            </span>
                                            <select class="form-control border-start-0" id="mode_transit" name="mode_transit" 
                                                    style="border-radius: 0 10px 10px 0; height: 50px;" required>
                                                <option value="">Sélectionnez un mode</option>
                                                <option value="Maritime">Maritime</option>
                                                <option value="Aerien">Aérien</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pays_expedition" class="form-label fw-bold" style="color: #0e914b;">
                                            Pays d'Expédition <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-globe" style="color: #fea219;"></i>
                                            </span>
                                            <select class="form-control border-start-0" id="pays_expedition" name="pays_expedition" 
                                                    style="border-radius: 0 10px 10px 0; height: 50px;" required>
                                                <option value="">Sélectionnez un pays</option>
                                                <option value="France">France</option>
                                                <option value="Chine">Chine</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4 mt-2">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="agence_expedition" class="form-label fw-bold" style="color: #0e914b;">
                                           Agence de Destination<span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-building" style="color: #fea219;"></i>
                                            </span>
                                            <input type="text" class="form-control border-start-0" id="agence_expedition" 
                                                   name="agence_expedition" readonly required
                                                   style="border-radius: 0 10px 10px 0; height: 50px; background-color: #f8f9fa;">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="agence_destination" class="form-label fw-bold" style="color: #0e914b;">
                                              Agence d'Expédition <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-map-marker-alt" style="color: #fea219;"></i>
                                            </span>
                                            <input type="text" class="form-control border-start-0" id="agence_destination" 
                                                   name="agence_destination" readonly required
                                                   style="border-radius: 0 10px 10px 0; height: 50px; background-color: #f8f9fa;">
                                        </div>
                                        <small class="text-muted mt-1" id="devise_destination_info"></small>
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
                                        "
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(14, 145, 75, 0.3)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                    Suivant <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Étape 2: Informations client -->
                        <div class="step-content d-none" id="step-2">
                            <div class="step-header mb-4 text-center">
                                <div class="icon-container bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-user fa-lg" style="color: #0e914b;"></i>
                                </div>
                                <h4 class="mb-2 fw-bold" style="color: #0e914b;">Vos Informations</h4>
                                <p class="text-muted">Vos informations personnelles pré-remplies</p>
                            </div>
                            
                            <div class="info-card" style="
                                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                                border-radius: 15px;
                                padding: 2rem;
                                border-left: 4px solid #fea219;
                            ">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold" style="color: #0e914b;">Nom</label>
                                            <div class="info-field" style="
                                                background: white;
                                                padding: 12px 15px;
                                                border-radius: 10px;
                                                border: 1px solid #e9ecef;
                                                color: #495057;
                                            ">
                                                {{ $user->name }}
                                            </div>
                                            <input type="hidden" id="name_client" name="name_client" value="{{ $user->name }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold" style="color: #0e914b;">Prénom</label>
                                            <div class="info-field" style="
                                                background: white;
                                                padding: 12px 15px;
                                                border-radius: 10px;
                                                border: 1px solid #e9ecef;
                                                color: #495057;
                                            ">
                                                {{ $user->prenom }}
                                            </div>
                                            <input type="hidden" id="prenom_client" name="prenom_client" value="{{ $user->prenom }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4 mt-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold" style="color: #0e914b;">Email</label>
                                            <div class="info-field" style="
                                                background: white;
                                                padding: 12px 15px;
                                                border-radius: 10px;
                                                border: 1px solid #e9ecef;
                                                color: #495057;
                                            ">
                                                {{ $user->email }}
                                            </div>
                                            <input type="hidden" id="email_client" name="email_client" value="{{ $user->email }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold" style="color: #0e914b;">Contact</label>
                                            <div class="info-field" style="
                                                background: white;
                                                padding: 12px 15px;
                                                border-radius: 10px;
                                                border: 1px solid #e9ecef;
                                                color: #495057;
                                            ">
                                                {{ $user->contact }}
                                            </div>
                                            <input type="hidden" id="contact_client" name="contact_client" value="{{ $user->contact }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
                                    <label class="form-label fw-bold" style="color: #0e914b;">Adresse</label>
                                    <div class="info-field" style="
                                        background: white;
                                        padding: 12px 15px;
                                        border-radius: 10px;
                                        border: 1px solid #e9ecef;
                                        color: #495057;
                                        min-height: 60px;
                                    ">
                                        {{ $user->adresse }}
                                    </div>
                                    <input type="hidden" id="adresse_client" name="adresse_client" value="{{ $user->adresse }}">
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
                                        "
                                        onmouseover="this.style.backgroundColor='#0e914b'; this.style.color='white';"
                                        onmouseout="this.style.backgroundColor='white'; this.style.color='#0e914b';">
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
                                        "
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(14, 145, 75, 0.3)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                    Suivant <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Étape 3: Informations colis -->
                        <div class="step-content d-none" id="step-3">
                            <div class="step-header mb-4 text-center">
                                <div class="icon-container bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-box fa-lg" style="color: #0e914b;"></i>
                                </div>
                                <h4 class="mb-2 fw-bold" style="color: #0e914b;">Informations du Colis</h4>
                                <p class="text-muted">Ajoutez les informations sur vos colis à expédier</p>
                            </div>
                            
                            <div id="colis-container">
                                <!-- Premier colis -->
                                <div class="colis-item card mb-4 border-0 shadow-sm" data-index="0" 
                                     style="border-radius: 15px; border-left: 4px solid #fea219;">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3"
                                         style="border-radius: 15px 15px 0 0; border-bottom: 1px solid #e9ecef;">
                                        <h6 class="mb-0 fw-bold" style="color: #0e914b;">
                                            <i class="fas fa-box me-2"></i>Colis #1
                                        </h6>
                                        <button type="button" class="btn btn-sm remove-colis d-none"
                                                style="
                                                    background: #dc3545;
                                                    border: none;
                                                    border-radius: 20px;
                                                    color: white;
                                                    width: 30px;
                                                    height: 30px;
                                                    padding: 0;
                                                ">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold" style="color: #0e914b;">
                                                    Quantité <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control" name="colis[0][quantite]" min="1" required
                                                       style="border-radius: 10px; height: 45px; border: 1px solid #e9ecef;">
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold" style="color: #0e914b;">
                                                    Produit <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" name="colis[0][produit]" required
                                                       style="border-radius: 10px; height: 45px; border: 1px solid #e9ecef;">
                                            </div>
                                        </div>

                                        <div class="row g-3 mt-2">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold" style="color: #0e914b;">
                                                    Valeur <span class="text-danger">*</span>
                                                    <small class="text-muted" id="devise_indication">(€)</small>
                                                </label>
                                                <input type="number" class="form-control" name="colis[0][valeur]" step="0.01" min="0" required
                                                       style="border-radius: 10px; height: 45px; border: 1px solid #e9ecef;">
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold" style="color: #0e914b;">Type de Colis</label>
                                                <select class="form-control" name="colis[0][type_colis]"
                                                        style="border-radius: 10px; height: 45px; border: 1px solid #e9ecef;">
                                                    <option value="">Sélectionnez un type</option>
                                                    <option value="Standard">Standard</option>
                                                    <option value="Fragile">Fragile</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row g-3 mt-2">
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold" style="color: #0e914b;">Longueur (cm)</label>
                                                <input type="number" class="form-control" name="colis[0][longueur]" step="0.1" min="0"
                                                       style="border-radius: 10px; height: 45px; border: 1px solid #e9ecef;">
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold" style="color: #0e914b;">Largeur (cm)</label>
                                                <input type="number" class="form-control" name="colis[0][largeur]" step="0.1" min="0"
                                                       style="border-radius: 10px; height: 45px; border: 1px solid #e9ecef;">
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold" style="color: #0e914b;">Hauteur (cm)</label>
                                                <input type="number" class="form-control" name="colis[0][hauteur]" step="0.1" min="0"
                                                       style="border-radius: 10px; height: 45px; border: 1px solid #e9ecef;">
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <label class="form-label fw-bold" style="color: #0e914b;">Description</label>
                                            <textarea class="form-control" name="colis[0][description]" rows="2"
                                                      style="border-radius: 10px; border: 1px solid #e9ecef;"
                                                      placeholder="Description du produit..."></textarea>
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
                                        "
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(254, 162, 25, 0.3)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                    <i class="fas fa-plus me-2"></i>Ajouter un autre colis
                                </button>
                                
                                <div>
                                    <button type="button" class="btn prev-step me-2" data-prev="2"
                                            style="
                                                background: white;
                                                border: 2px solid #0e914b;
                                                border-radius: 25px;
                                                padding: 10px 25px;
                                                font-weight: 600;
                                                color: #0e914b;
                                                transition: all 0.3s ease;
                                            "
                                            onmouseover="this.style.backgroundColor='#0e914b'; this.style.color='white';"
                                            onmouseout="this.style.backgroundColor='white'; this.style.color='#0e914b';">
                                        <i class="fas fa-arrow-left me-2"></i>Précédent
                                    </button>
                                    <button type="button" class="btn next-step" data-next="4"
                                            style="
                                                background: linear-gradient(135deg, #0e914b 0%, #0b7a3d 100%);
                                                border: none;
                                                border-radius: 25px;
                                                padding: 10px 25px;
                                                font-weight: 600;
                                                color: white;
                                                transition: all 0.3s ease;
                                            "
                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(14, 145, 75, 0.3)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                        Suivant <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Étape 4: Récapitulatif -->
                        <div class="step-content d-none" id="step-4">
                            <div class="step-header mb-4 text-center">
                                <div class="icon-container bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-clipboard-check fa-lg" style="color: #0e914b;"></i>
                                </div>
                                <h4 class="mb-2 fw-bold" style="color: #0e914b;">Récapitulatif</h4>
                                <p class="text-muted">Vérifiez les informations avant de soumettre votre demande</p>
                            </div>
                            
                            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                                <div class="card-header bg-white py-3" style="border-radius: 15px 15px 0 0;">
                                    <h5 class="mb-0 fw-bold" style="color: #0e914b;">
                                        <i class="fas fa-file-alt me-2"></i>Résumé de votre demande de tarification
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Transport -->
                                    <div class="mb-4">
                                        <h6 class="text-primary fw-bold" style="color: #0e914b;">
                                            <i class="fas fa-shipping-fast me-2"></i>Transport
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Mode de transit:</strong> <span id="recap_mode_transit"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Pays d'expédition:</strong> <span id="recap_pays_expedition"></span>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <strong>Agence d'expédition:</strong> <span id="recap_agence_expedition"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Agence de destination:</strong> <span id="recap_agence_destination"></span>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <strong>Devise d'expédition:</strong> <span id="recap_devise_expedition" class="badge bg-success"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Devise de destination:</strong> <span id="recap_devise_destination" class="badge bg-info"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Informations client -->
                                    <div class="mb-4">
                                        <h6 class="text-primary fw-bold" style="color: #0e914b;">
                                            <i class="fas fa-user me-2"></i>Informations Client
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Nom:</strong> <span id="recap_name_client"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Prénom:</strong> <span id="recap_prenom_client"></span>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <strong>Email:</strong> <span id="recap_email_client"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Contact:</strong> <span id="recap_contact_client"></span>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <strong>Adresse:</strong> <span id="recap_adresse_client"></span>
                                        </div>
                                    </div>

                                    <!-- Colis -->
                                    <div>
                                        <h6 class="text-primary fw-bold" style="color: #0e914b;">
                                            <i class="fas fa-box me-2"></i>Colis
                                            <small class="text-muted">(Devise: <span id="recap_devise_colis"></span>)</small>
                                        </h6>
                                        <div id="recap-colis-container"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-5 pt-3">
                                <button type="button" class="btn prev-step" data-prev="3"
                                        style="
                                            background: white;
                                            border: 2px solid #0e914b;
                                            border-radius: 25px;
                                            padding: 12px 30px;
                                            font-weight: 600;
                                            color: #0e914b;
                                            transition: all 0.3s ease;
                                        "
                                        onmouseover="this.style.backgroundColor='#0e914b'; this.style.color='white';"
                                        onmouseout="this.style.backgroundColor='white'; this.style.color='#0e914b';">
                                    <i class="fas fa-arrow-left me-2"></i>Précédent
                                </button>
                                <button type="submit" class="btn"
                                        style="
                                            background: linear-gradient(135deg, #fea219 0%, #e69100 100%);
                                            border: none;
                                            border-radius: 25px;
                                            padding: 12px 30px;
                                            font-weight: 600;
                                            color: white;
                                            transition: all 0.3s ease;
                                        "
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(254, 162, 25, 0.3)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                    <i class="fas fa-paper-plane me-2"></i>Soumettre la demande
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
    .step-indicator.active .step-number {
        background: linear-gradient(135deg, #fea219 0%, #e69100 100%) !important;
        color: white !important;
        transform: scale(1.1);
    }
    
    .step-indicator.active .step-label {
        color: #0e914b !important;
        font-weight: bold;
    }
    
    .card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }
    
    .form-control {
        border-radius: 10px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        height: 45px;
    }
    
    .form-control:focus {
        border-color: #0e914b;
        box-shadow: 0 0 0 0.2rem rgba(14, 145, 75, 0.15);
        transform: translateY(-1px);
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-right: none;
    }
    
    .btn {
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    
    .step-content {
        animation: fadeIn 0.5s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .info-field {
        transition: all 0.3s ease;
    }
    
    .info-field:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .devise-badge {
        background: linear-gradient(135deg, #fea219 0%, #e69100 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    let colisCount = 1;
    let currentDevise = 'EUR';
    let currentAgenceExpeditionId = null;
    let currentAgenceDestinationId = null;

    // Données des agences depuis le backend
    const agencesData = @json($agences->mapWithKeys(function($agence) {
        return [$agence->name => [
            'id' => $agence->id,
            'devise' => $agence->devise,
            'pays' => $agence->pays
        ]];
    }));

    // Gestion des étapes
    function showStep(step) {
        document.querySelectorAll('.step-content').forEach(el => {
            el.classList.add('d-none');
        });
        document.getElementById(`step-${step}`).classList.remove('d-none');
        
        const progress = ((step - 1) / 3) * 100;
        document.getElementById('progress-bar').style.width = `${progress}%`;
        
        document.querySelectorAll('.step-indicator').forEach(el => {
            el.classList.remove('active');
            if (parseInt(el.dataset.step) <= step) {
                el.classList.add('active');
            }
        });
        
        currentStep = step;
    }

    // Navigation entre les étapes
    document.querySelectorAll('.next-step').forEach(button => {
        button.addEventListener('click', function() {
            const nextStep = parseInt(this.dataset.next);
            if (validateStep(currentStep)) {
                if (nextStep === 4) {
                    updateRecap();
                }
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

    // Gestion du mode de transit et pays d'expédition
    document.getElementById('mode_transit').addEventListener('change', function() {
        updateAgences();
    });

    document.getElementById('pays_expedition').addEventListener('change', function() {
        updateAgences();
    });

    function updateAgences() {
        const modeTransit = document.getElementById('mode_transit').value;
        const paysExpedition = document.getElementById('pays_expedition').value;
        
        let agenceExpeditionName = '';
        let agenceDestinationName = '';

        if (modeTransit === 'Maritime') {
            agenceExpeditionName = 'DS Translog Carrefour Angré';
        } else if (modeTransit === 'Aerien') {
            agenceExpeditionName = 'DS Translog Angré 8ème Tranche';
        }
        
        if (paysExpedition === 'Chine') {
            agenceDestinationName = 'Agence de Chine';
        } else if (paysExpedition === 'France') {
            agenceDestinationName = 'AFT Agence Louis Bleriot';
        }

        document.getElementById('agence_expedition').value = agenceExpeditionName;
        document.getElementById('agence_destination').value = agenceDestinationName;

        if (agencesData[agenceExpeditionName]) {
            currentAgenceExpeditionId = agencesData[agenceExpeditionName].id;
            const deviseExpedition = agencesData[agenceExpeditionName].devise;
            document.getElementById('agence_expedition_id').value = currentAgenceExpeditionId;
            currentDevise = deviseExpedition;
            updateDeviseIndication();
        }

        if (agencesData[agenceDestinationName]) {
            currentAgenceDestinationId = agencesData[agenceDestinationName].id;
            const deviseDestination = agencesData[agenceDestinationName].devise;
            document.getElementById('agence_destination_id').value = currentAgenceDestinationId;
        }
    }

    function updateDeviseIndication() {
        const deviseSymbols = {
            'EUR': '€',
            'USD': '$',
            'XOF': 'CFA',
            'CNY': '¥',
            'CAD': 'C$',
            'CHF': 'CHF',
            'GBP': '£',
            'JPY': '¥'
        };
        
        const symbol = deviseSymbols[currentDevise] || currentDevise;
        document.getElementById('devise_indication').textContent = `(${symbol})`;
    }

    // Ajout de colis
    document.getElementById('add-colis').addEventListener('click', function() {
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
            }
        }
    });

    // Validation des étapes
    function validateStep(step) {
        let isValid = true;
        
        if (step === 1) {
            const modeTransit = document.getElementById('mode_transit').value;
            const paysExpedition = document.getElementById('pays_expedition').value;
            
            if (!modeTransit || !paysExpedition) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Champs obligatoires',
                    text: 'Veuillez remplir tous les champs obligatoires de l\'étape 1',
                    confirmButtonColor: '#0e914b'
                });
                isValid = false;
            }
        }
        
        if (step === 3) {
            const colisItems = document.querySelectorAll('.colis-item');
            for (let i = 0; i < colisItems.length; i++) {
                const quantite = colisItems[i].querySelector(`input[name="colis[${i}][quantite]"]`).value;
                const produit = colisItems[i].querySelector(`input[name="colis[${i}][produit]"]`).value;
                const valeur = colisItems[i].querySelector(`input[name="colis[${i}][valeur]"]`).value;
                
                if (!quantite || !produit || !valeur) {
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
        
        return isValid;
    }

    // Mise à jour du récapitulatif
    function updateRecap() {
        const modeTransit = document.getElementById('mode_transit').value;
        const paysExpedition = document.getElementById('pays_expedition').value;
        const agenceExpeditionName = document.getElementById('agence_expedition').value;
        const agenceDestinationName = document.getElementById('agence_destination').value;
        
        document.getElementById('recap_mode_transit').textContent = modeTransit;
        document.getElementById('recap_pays_expedition').textContent = paysExpedition;
        document.getElementById('recap_agence_expedition').textContent = agenceExpeditionName;
        document.getElementById('recap_agence_destination').textContent = agenceDestinationName;
        
        if (agencesData[agenceExpeditionName]) {
            document.getElementById('recap_devise_expedition').textContent = agencesData[agenceExpeditionName].devise;
        }
        if (agencesData[agenceDestinationName]) {
            document.getElementById('recap_devise_destination').textContent = agencesData[agenceDestinationName].devise;
        }
        
        document.getElementById('recap_name_client').textContent = document.getElementById('name_client').value;
        document.getElementById('recap_prenom_client').textContent = document.getElementById('prenom_client').value;
        document.getElementById('recap_email_client').textContent = document.getElementById('email_client').value;
        document.getElementById('recap_contact_client').textContent = document.getElementById('contact_client').value;
        document.getElementById('recap_adresse_client').textContent = document.getElementById('adresse_client').value;
        
        const deviseSymbols = {
            'EUR': '€',
            'USD': '$', 
            'XOF': 'CFA',
            'CNY': '¥',
            'CAD': 'C$',
            'CHF': 'CHF',
            'GBP': '£',
            'JPY': '¥'
        };
        const symbol = deviseSymbols[currentDevise] || currentDevise;
        document.getElementById('recap_devise_colis').textContent = symbol;
        
        const recapContainer = document.getElementById('recap-colis-container');
        recapContainer.innerHTML = '';
        
        document.querySelectorAll('.colis-item').forEach((item, index) => {
            const quantite = item.querySelector(`input[name="colis[${index}][quantite]"]`).value;
            const produit = item.querySelector(`input[name="colis[${index}][produit]"]`).value;
            const valeur = item.querySelector(`input[name="colis[${index}][valeur]"]`).value;
            const typeColis = item.querySelector(`select[name="colis[${index}][type_colis]"]`).value;
            const description = item.querySelector(`textarea[name="colis[${index}][description]"]`).value;
            const longueur = item.querySelector(`input[name="colis[${index}][longueur]"]`).value;
            const largeur = item.querySelector(`input[name="colis[${index}][largeur]"]`).value;
            const hauteur = item.querySelector(`input[name="colis[${index}][hauteur]"]`).value;
            
            const colisHTML = `
                <div class="border rounded p-3 mb-3" style="border-left: 4px solid #fea219 !important;">
                    <h6 class="fw-bold" style="color: #0e914b;">
                        <i class="fas fa-box me-2"></i>Colis #${index + 1}
                    </h6>
                    <div class="row">
                        <div class="col-md-3"><strong>Quantité:</strong> ${quantite}</div>
                        <div class="col-md-3"><strong>Produit:</strong> ${produit}</div>
                        <div class="col-md-3"><strong>Valeur:</strong> ${valeur} ${symbol}</div>
                        <div class="col-md-3"><strong>Type:</strong> ${typeColis || 'Non spécifié'}</div>
                    </div>
                    ${longueur || largeur || hauteur ? `
                    <div class="row mt-2">
                        <div class="col-md-4"><strong>Dimensions:</strong> ${longueur || '0'} × ${largeur || '0'} × ${hauteur || '0'} cm</div>
                        <div class="col-md-8"><strong>Description:</strong> ${description || 'Aucune'}</div>
                    </div>
                    ` : `
                    <div class="mt-2">
                        <strong>Description:</strong> ${description || 'Aucune'}
                    </div>
                    `}
                </div>
            `;
            recapContainer.innerHTML += colisHTML;
        });
    }

    // Gestion de la soumission du formulaire
    document.getElementById('tarificationForm').addEventListener('submit', function(e) {
        // Validation finale avant soumission
        if (!validateStep(1) || !validateStep(3)) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Formulaire incomplet',
                text: 'Veuillez remplir tous les champs obligatoires',
                confirmButtonColor: '#0e914b'
            });
            return;
        }

        // Afficher l'indicateur de chargement
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.classList.remove('d-none');
        }
        
        // Désactiver le bouton de soumission
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Soumission...';
        
        // Laisser le formulaire se soumettre normalement
        // this.submit() est appelé automatiquement
    });

    // Initialisation
    showStep(1);
    updateDeviseIndication();
});
</script>

<!-- Inclure Font Awesome et SweetAlert2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection