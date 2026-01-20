@extends('admin.layouts.template')

@section('content')
    <div class="mdc-layout-grid">
        <div class="mdc-layout-grid__inner">
            <div class="mdc-layout-grid__cell stretch-card mdc-layout-grid__cell--span-12">
                <div class="mdc-card" style="border-top: 5px solid #fea219;">
                    <div class="mdc-card__header d-flex justify-content-between align-items-center">
                        <h4 class="card-title"><i class="fas fa-search-location"></i> Recherche Globale de Colis</h4>
                        <img src="{{asset('assets/img/aft.jpg')}}" style="height: 40px;" alt="AFT Logo">
                    </div>
                    <div class="mdc-card__content">
                        <p class="text-muted mb-4">Recherchez instantanément des colis par référence, nom ou prénom de
                            l'expéditeur ou du destinataire.</p>

                        <div class="mdc-layout-grid__inner">
                            <div class="mdc-layout-grid__cell stretch-card mdc-layout-grid__cell--span-10">
                                <div
                                    class="mdc-text-field mdc-text-field--outlined mdc-text-field--with-leading-icon w-100">
                                    <i class="material-icons mdc-text-field__icon">search</i>
                                    <input class="mdc-text-field__input" id="search-query"
                                        placeholder="Ex: AFT-COL-12345, Jean Dupont...">
                                    <div class="mdc-notched-outline">
                                        <div class="mdc-notched-outline__leading"></div>
                                        <div class="mdc-notched-outline__notch">
                                            <label class="mdc-floating-label">Terme de recherche</label>
                                        </div>
                                        <div class="mdc-notched-outline__trailing"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mdc-layout-grid__cell stretch-card mdc-layout-grid__cell--span-2">
                                <button class="mdc-button mdc-button--raised w-100" onclick="performSearch()"
                                    id="search-btn" style="background-color: #fea219; height: 56px; font-weight: bold;">
                                    <span class="mdc-button__label">RECHERCHER</span>
                                </button>
                            </div>
                        </div>

                        <div id="search-info" class="mt-3" style="display: none;">
                            <p class="text-primary"><i class="fas fa-spinner fa-spin"></i> Recherche en cours...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function performSearch() {
            const query = document.getElementById('search-query').value;
            const btn = document.getElementById('search-btn');
            const info = document.getElementById('search-info');

            if (!query || query.length < 2) {
                Swal.fire('Attention', 'Veuillez saisir au moins 2 caractères', 'warning');
                return;
            }

            btn.disabled = true;
            info.style.display = 'block';

            Swal.fire({
                title: 'Recherche en cours...',
                html: 'Nous parcourons la base de données...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`{{ route('admin.global.search') }}?query=${query}`)
                .then(response => response.json())
                .then(results => {
                    Swal.close();
                    btn.disabled = false;
                    info.style.display = 'none';

                    if (results.length === 0) {
                        Swal.fire({
                            title: 'Aucun résultat',
                            text: 'Désolé, aucun colis ne correspond à votre recherche.',
                            icon: 'info',
                            confirmButtonColor: '#fea219'
                        });
                        return;
                    }

                    displayResults(results);
                })
                .catch(error => {
                    Swal.close();
                    btn.disabled = false;
                    info.style.display = 'none';
                    Swal.fire('Erreur', 'Impossible d\'effectuer la recherche', 'error');
                    console.error(error);
                });
        }

        function displayResults(results) {
            let html = `
                            <div class="search-results-container" style="max-height: 500px; overflow-y: auto; text-align: left; padding: 5px;">
                                <p class="mb-3 text-muted">${results.length} résultat(s) trouvé(s)</p>
                        `;

            results.forEach(colis => {
                html += `
                                <div class="result-item" style="background: white; border-radius: 10px; border-left: 5px solid #fea219; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); cursor: pointer;" 
                                     onclick="showFullDetails(${JSON.stringify(colis).replace(/"/g, '&quot;')})">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 style="color: #fea219; margin: 0;">${colis.reference_colis}</h5>
                                        <span class="badge" style="background: #e9ecef; color: #495057; border: 1px solid #ced4da;">${colis.statut}</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">EXPÉDITEUR</small>
                                            <p class="mb-0"><strong>${colis.name_expediteur} ${colis.prenom_expediteur || ''}</strong></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <small class="text-muted">DESTINATAIRE</small>
                                            <p class="mb-0"><strong>${colis.name_destinataire} ${colis.prenom_destinataire || ''}</strong></p>
                                        </div>
                                    </div>
                                </div>
                            `;
            });

            html += '</div>';

            Swal.fire({
                title: 'Résultats de recherche',
                html: html,
                width: '650px',
                showConfirmButton: false,
                showCloseButton: true,
                background: '#f8f9fa'
            });
        }

        function showFullDetails(colis) {
            const date = new Date(colis.created_at).toLocaleString();
            const datePaiement = colis.date_paiement ? new Date(colis.date_paiement).toLocaleString() : 'N/A';

            // Extraction de la nature
            let itemsArray = [];
            if (colis.colis) {
                if (Array.isArray(colis.colis)) {
                    itemsArray = colis.colis;
                } else if (typeof colis.colis === 'string') {
                    try {
                        itemsArray = JSON.parse(colis.colis);
                    } catch (e) { }
                }
            }

            let natureColis = 'N/A';
            if (itemsArray.length > 0) {
                const firstItem = itemsArray[0];
                natureColis = firstItem.produit || firstItem.description || firstItem.nature || 'N/A';
            }

            // Construction de la liste des articles
            let itemsHtml = '<p class="text-muted text-center">Aucun article listé</p>';
            if (itemsArray.length > 0) {
                itemsHtml = `
                            <table class="table table-sm table-bordered" style="font-size: 0.8rem;">
                                <thead style="background: #f8f9fa;">
                                    <tr>
                                        <th>Désignation</th>
                                        <th>Quantité</th>
                                        <th>Poids/Vol</th>
                                        <th>Valeur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${itemsArray.map(item => `
                                        <tr>
                                            <td>${item.produit || item.description || item.nature || 'N/A'}</td>
                                            <td>${item.quantite || '1'}</td>
                                            <td>${item.poids || item.volume || '0'}</td>
                                            <td>${item.valeur || item.prix_unitaire || '0'}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        `;
            }

            // Détails du paiement
            let payDetails = '';
            if (colis.methode_paiement === 'virement_bancaire') {
                payDetails = `<strong>Banque:</strong> ${colis.nom_banque || 'N/A'}<br><strong>Compte:</strong> ${colis.numero_compte || 'N/A'}`;
            } else if (colis.methode_paiement === 'mobile_money') {
                payDetails = `<strong>Opérateur:</strong> ${colis.operateur_mobile_money || 'N/A'}<br><strong>Numéro:</strong> ${colis.numero_mobile_money || 'N/A'}`;
            }

            // Agences
            const agenceExp = colis.agence_expedition?.name || colis.agence_expedition?.name_agence || (typeof colis.agence_expedition === 'string' ? colis.agence_expedition : 'N/A');
            const agenceDest = colis.agence_destination?.name || colis.agence_destination?.name_agence || (typeof colis.agence_destination === 'string' ? colis.agence_destination : 'N/A');
            const serviceNom = colis.service?.nom_service || colis.service_id || 'N/A';

            let html = `
                            <div class="full-details text-left" style="font-size: 0.9rem; max-height: 70vh; overflow-y: auto; padding-right: 10px;">
                                <div class="section-title" style="background: #fea219; color: white; padding: 8px 15px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;">
                                    <i class="fas fa-info-circle"></i> INFORMATIONS GÉNÉRALES
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <strong>Référence:</strong> <span class="text-primary">${colis.reference_colis}</span><br>
                                        <strong>Nature:</strong> <span class="text-dark">${natureColis}</span><br>
                                        <strong>Date Enreg.:</strong> ${date}<br>
                                        <strong>Devise:</strong> ${colis.devise || 'N/A'}
                                    </div>
                                    <div class="col-6">
                                        <strong>Statut:</strong> <span class="badge badge-warning">${colis.statut}</span><br>
                                        <strong>Mode Transit:</strong> ${colis.mode_transit || 'N/A'}<br>
                                    </div>
                                </div>

                                <div class="section-title" style="background: #34495e; color: white; padding: 8px 15px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;">
                                    <i class="fas fa-list"></i> LISTE DES ARTICLES (CONTENU)
                                </div>
                                <div class="mb-3">
                                    ${itemsHtml}
                                </div>

                                <div class="section-title" style="background: #34495e; color: white; padding: 8px 15px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;">
                                    <i class="fas fa-user-friends"></i> ACTEURS
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6" style="border-right: 1px solid #eee;">
                                        <p class="mb-1 text-primary"><strong>EXPÉDITEUR</strong></p>
                                        <strong>Nom:</strong> ${colis.name_expediteur} ${colis.prenom_expediteur || ''}<br>
                                        <strong>Contact:</strong> ${colis.contact_expediteur || 'N/A'}<br>
                                        <strong>Email:</strong> ${colis.email_expediteur || 'N/A'}<br>
                                        <strong>Adresse:</strong> ${colis.adresse_expediteur || 'N/A'}
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1 text-primary"><strong>DESTINATAIRE</strong></p>
                                        <strong>Nom:</strong> ${colis.name_destinataire} ${colis.prenom_destinataire || ''}<br>
                                        <strong>Contact:</strong> ${colis.contact_destinataire || 'N/A'}<br>
                                        <strong>Email:</strong> ${colis.email_destinataire || 'N/A'}<br>
                                        <strong>Adresse:</strong> ${colis.adresse_destinataire || 'N/A'}
                                    </div>
                                </div>

                                <div class="section-title" style="background: #34495e; color: white; padding: 8px 15px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;">
                                    <i class="fas fa-map-marked-alt"></i> LOGISTIQUE
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <strong>Agence Exp.:</strong> ${agenceExp}<br>
                                        <strong>Conteneur:</strong> ${colis.conteneur?.name_conteneur || 'Non assigné'}
                                    </div>
                                    <div class="col-6">
                                        <strong>Agence Dest.:</strong> ${agenceDest}<br>
                                    </div>
                                </div>

                                <div class="section-title" style="background: #27ae60; color: white; padding: 8px 15px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;">
                                    <i class="fas fa-file-invoice-dollar"></i> FINANCES & PAIEMENT
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <strong>Montant Total:</strong> ${new Intl.NumberFormat().format(colis.montant_total || 0)} ${colis.devise}<br>
                                        <strong>Montant Payé:</strong> ${new Intl.NumberFormat().format(colis.montant_paye || 0)} ${colis.devise}<br>
                                        <strong>Reste à Payer:</strong> <span class="text-danger">${new Intl.NumberFormat().format(colis.reste_a_payer || 0)} ${colis.devise}</span>
                                    </div>
                                    <div class="col-6">
                                        <strong>Méthode:</strong> ${colis.methode_paiement || 'N/A'}<br>
                                        ${payDetails}
                                        <strong>Statut Paiement:</strong> ${colis.statut_paiement || 'N/A'}
                                    </div>
                                </div>
                                <div class="row mb-3 mt-2">
                                    <div class="col-6">
                                        <strong>Agent Encaisseur:</strong> ${colis.agent_encaisseur_name || 'N/A'}<br>
                                    </div>
                                    <div class="col-6">
                                        <strong>Date Paiement:</strong> ${datePaiement}
                                    </div>
                                </div>
                                ${colis.notes_paiement ? `<div class="p-2 bg-light border rounded"><strong>Notes:</strong> ${colis.notes_paiement}</div>` : ''}

                            <div class="section-title" style="background: #e67e22; color: white; padding: 8px 15px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;">
                                <i class="fas fa-file-pdf"></i> DOCUMENTS
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 d-flex justify-content-around">
                                    <a href="/admin/parcel/${colis.id}/etiquettes?action=download" target="_blank" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-tags"></i> Étiquettes
                                    </a>
                                    <a href="/admin/parcel/${colis.id}/facture?action=download" target="_blank" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-file-invoice"></i> Facture
                                    </a>
                                    <a href="/admin/parcel/${colis.id}/bon-livraison?action=download" target="_blank" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-truck-loading"></i> Bon de Livraison
                                    </a>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-center">
                                <button onclick="Swal.close()" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> FERMER
                                </button>
                            </div>
                            </div>
                            `;

            Swal.fire({
                title: `DÉTAILS DU COLIS - ${colis.reference_colis}`,
                html: html,
                width: '850px',
                showConfirmButton: false,
                showCloseButton: true,
                background: '#fff'
            });
        }

        document.getElementById('search-query').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    </script>

    <style>
        .mdc-card {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .mdc-text-field--outlined:not(.mdc-text-field--disabled) .mdc-notched-outline__leading,
        .mdc-text-field--outlined:not(.mdc-text-field--disabled) .mdc-notched-outline__notch,
        .mdc-text-field--outlined:not(.mdc-text-field--disabled) .mdc-notched-outline__trailing {
            border-color: rgba(0, 0, 0, 0.12) !important;
        }

        .mdc-text-field--outlined:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-notched-outline__leading,
        .mdc-text-field--outlined:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-notched-outline__notch,
        .mdc-text-field--outlined:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-notched-outline__trailing {
            border-color: #fea219 !important;
            border-width: 2px;
        }

        .mdc-floating-label {
            color: #666;
        }

        .mdc-text-field--outlined:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-floating-label {
            color: #fea219 !important;
        }

        .result-item:hover {
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
@endsection