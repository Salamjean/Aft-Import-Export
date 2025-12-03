@extends('admin.layouts.template')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-10 mt-4">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-gradient-primary text-white py-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-white rounded-circle p-2 me-3">
                                <i class="fas fa-shipping-fast fa-lg" style="color: #0d8644"></i>
                            </div>
                            <div>
                                <h3 class="card-title mb-0">Planifier un Transport</h3>
                                <p class="mb-0 opacity-75">Remplissez les informations pour planifier un nouveau transport
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-5">
                        <form id="transportForm" action="{{ route('admin.bateau.store') }}" method="POST"
                            class="needs-validation" novalidate>
                            @csrf

                            <div class="row">
                                <!-- Type de transport -->
                                <div class="col-md-4 mb-4">
                                    <div class="form-group">
                                        <label for="type_transport" class="form-label fw-bold text-dark">Type de Transport
                                            *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-truck-loading" style="color: #0d8644"></i>
                                            </span>
                                            <select class="form-control border-start-0 ps-3" id="type_transport"
                                                name="type_transport" required style="height: 50px;">
                                                <option value="">Sélectionnez le type de transport</option>
                                                <option value="Bateau">Bateau</option>
                                                <option value="Avion">Avion</option>
                                            </select>
                                            <div class="invalid-feedback">
                                                Veuillez sélectionner un type de transport.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Conteneur (tous les conteneurs affichés sans filtre) -->
                                <div class="col-md-4 mb-4">
                                    <div class="form-group">
                                        <label for="conteneur" id="label_conteneur"
                                            class="form-label fw-bold text-dark">Conteneur *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-box" style="color: #0d8644"></i>
                                            </span>
                                            <select class="form-control border-start-0 ps-3" id="conteneur" name="conteneur"
                                                required style="height: 50px;">
                                                <option value="">Sélectionnez un conteneur</option>
                                                <!-- Les options seront chargées par JavaScript -->
                                            </select>
                                            <div class="invalid-feedback">
                                                Veuillez sélectionner un conteneur.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Date d'arrivée -->
                                <div class="col-md-4 mb-4">
                                    <div class="form-group">
                                        <label for="date_arrive" class="form-label fw-bold text-dark">Date d'Arrivée
                                            *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-calendar-alt" style="color: #0d8644"></i>
                                            </span>
                                            <input type="date" class="form-control border-start-0 ps-3" id="date_arrive"
                                                name="date_arrive" required style="height: 50px;">
                                            <div class="invalid-feedback">
                                                Veuillez sélectionner une date d'arrivée.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Compagnie -->
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="compagnie" class="form-label fw-bold text-dark">Compagnie *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-building" style="color: #0d8644"></i>
                                            </span>
                                            <input type="text" class="form-control border-start-0 ps-3" id="compagnie"
                                                name="compagnie" placeholder="Nom de la compagnie" required
                                                style="height: 50px;">
                                            <div class="invalid-feedback">
                                                Veuillez saisir le nom de la compagnie.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nom (dynamique selon le type) -->
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="nom" id="label_nom" class="form-label fw-bold text-dark">Nom
                                            *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-ship" id="nom_icon" style="color: #0d8644"></i>
                                            </span>
                                            <input type="text" class="form-control border-start-0 ps-3" id="nom"
                                                name="nom" placeholder="Entrez le nom" required style="height: 50px;">
                                            <div class="invalid-feedback">
                                                Veuillez saisir le nom.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Numéro -->
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="numero" class="form-label fw-bold text-dark">Numéro *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-hashtag" style="color: #0d8644"></i>
                                            </span>
                                            <input type="text" class="form-control border-start-0 ps-3" id="numero"
                                                name="numero" placeholder="Numéro d'identification" required
                                                style="height: 50px;">
                                            <div class="invalid-feedback">
                                                Veuillez saisir le numéro.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Agence (auto-remplie) -->
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="agence" class="form-label fw-bold text-dark">Agence *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-map-marker-alt" style="color: #0d8644"></i>
                                            </span>
                                            <input type="text" class="form-control border-start-0 ps-3 bg-light"
                                                id="agence" name="agence" readonly required style="height: 50px;">
                                            <input type="hidden" id="agence_id" name="agence_id">
                                        </div>
                                        <small class="form-text text-muted">Sélection automatique selon le type de
                                            transport</small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="submit" class="btn btn-primary px-4"
                                    style="height: 50px; background: linear-gradient(135deg, #fea219 0%, #fea219 100%); border: none;">
                                    <i class="fas fa-paper-plane me-2"></i>Planifier le Transport
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        :root {
            --primary-color: #fea219;
            --secondary-color: #fea219;
            --light-bg: #f8f9fa;
        }

        .card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #fea219 0%, #fea219 100%) !important;
            border-bottom: none;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #fea219;
            box-shadow: 0 0 0 0.2rem rgba(254, 162, 25, 0.25);
        }

        .input-group-text {
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
            background-color: var(--light-bg);
        }

        .form-control.border-start-0 {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .btn-primary {
            background: linear-gradient(135deg, #fea219 0%, #2ecc71 100%);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(254, 162, 25, 0.4);
        }

        .btn-outline-secondary {
            border-radius: 10px;
            border: 2px solid #6c757d;
            font-weight: 600;
        }

        .form-label {
            font-size: 14px;
            margin-bottom: 8px;
        }

        .invalid-feedback {
            font-size: 12px;
        }

        .shadow-lg {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #fea219 0%, #2ecc71 100%);
        }

        /* Animation pour le chargement */
        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        .loading {
            animation: pulse 1.5s ease-in-out infinite;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeTransport = document.getElementById('type_transport');
            const conteneurSelect = document.getElementById('conteneur');
            const nomInput = document.getElementById('nom');
            const labelNom = document.getElementById('label_nom');
            const labelConteneur = document.getElementById('label_conteneur');
            const agenceInput = document.getElementById('agence');
            const agenceIdInput = document.getElementById('agence_id');
            const nomIcon = document.getElementById('nom_icon');

            // Configuration des agences
            const agences = {
                'Bateau': {
                    id: 4,
                    nom: 'DS Translog Carrefour Angré'
                },
                'Avion': {
                    id: 2,
                    nom: 'DS Translog Angré 8ème Tranche'
                }
            };

            // Configuration des labels et icônes
            const labels = {
                'Bateau': {
                    'nom': 'Nom du Bateau *',
                    'conteneur': 'Conteneur *',
                    'icon': 'fa-ship'
                },
                'Avion': {
                    'nom': 'Nom de l\'Avion *',
                    'conteneur': 'Ballon *',
                    'icon': 'fa-plane'
                }
            };

            // Événement de changement du type de transport
            typeTransport.addEventListener('change', function() {
                const selectedType = this.value;

                if (selectedType) {
                    // Mettre à jour les labels et icônes
                    labelNom.textContent = labels[selectedType]?.nom || 'Nom *';
                    labelConteneur.textContent = 'Conteneur *'; // Toujours "Conteneur"

                    // Mettre à jour l'icône
                    if (labels[selectedType]?.icon) {
                        nomIcon.className = `fas ${labels[selectedType].icon} text-success`;
                    }

                    // Mettre à jour l'agence
                    const agence = agences[selectedType];
                    if (agence) {
                        agenceInput.value = agence.nom;
                        agenceIdInput.value = agence.id;
                    } else {
                        agenceInput.value = '';
                        agenceIdInput.value = '';
                    }

                    // Charger TOUS les conteneurs (pas de filtre)
                    chargerConteneurs();
                } else {
                    // Réinitialiser si aucun type sélectionné
                    resetForm();
                }
            });

            function chargerConteneurs() {
    // Ne recharger que si la liste est vide
    if (conteneurSelect.options.length > 1) {
        return; // Les conteneurs sont déjà chargés
    }
    
    conteneurSelect.innerHTML = '<option value="">Chargement...</option>';
    conteneurSelect.disabled = true;

    fetch(`/admin/conteneurs/tous`)
        .then(response => response.json())
        .then(data => {
            // Réinitialiser complètement
            conteneurSelect.innerHTML = '<option value="">Sélectionnez un conteneur</option>';
            
            if (data && data.length > 0) {
                data.forEach(conteneur => {
                    // Vérifier si cette option existe déjà
                    const optionExistante = Array.from(conteneurSelect.options).find(
                        option => option.value == conteneur.id
                    );
                    
                    if (!optionExistante) {
                        const option = document.createElement('option');
                        option.value = conteneur.id;
                        option.textContent = `${conteneur.name_conteneur || ''} ${conteneur.numero_conteneur ? '- ' + conteneur.numero_conteneur : ''}`.trim();
                        conteneurSelect.appendChild(option);
                    }
                });
            }
            
            conteneurSelect.disabled = false;
        })
        .catch(error => {
            console.error('Erreur:', error);
            conteneurSelect.innerHTML = '<option value="">Erreur</option>';
            conteneurSelect.disabled = false;
        });
}

            function resetForm() {
                labelNom.textContent = 'Nom *';
                labelConteneur.textContent = 'Conteneur *';
                nomIcon.className = 'fas fa-ship text-primary';
                conteneurSelect.innerHTML = '<option value="">Sélectionnez d\'abord le type de transport</option>';
                agenceInput.value = '';
                agenceIdInput.value = '';
                conteneurSelect.disabled = false;
            }

            // Validation Bootstrap
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        });
    </script>
@endsection
