@extends('agent.layouts.template')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
:root {
    --primary-color: #0d8644;
    --primary-hover: #0a6c36;
    --light-bg: #f8f9fa;
    --border-color: #e0e0e0;
}

.primary-bg {
    background-color: var(--primary-color);
}

.primary-text {
    color: var(--primary-color);
}

.btn-primary-custom {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
    transition: all 0.3s ease;
    border-radius: 6px;
    font-weight: 500;
}

.btn-primary-custom:hover {
    background-color: var(--primary-hover);
    border-color: var(--primary-hover);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(13, 134, 68, 0.2);
}

.card-modern {
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    margin-bottom: 1.5rem;
    margin: 10px;
}

.card-modern:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
}

.card-header-modern {
    background-color: var(--primary-color);
    color: white;
    border-bottom: none;
    padding: 1.25rem 1.5rem;
    border-radius: 8px 8px 0 0 !important;
}

.card-header-modern h3 {
    margin: 0;
    font-weight: 600;
    font-size: 1.25rem;
}

.form-control-modern {
    border: 1px solid var(--border-color);
    border-radius: 6px;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.form-control-modern:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(13, 134, 68, 0.15);
}

.alert-preview {
    border-radius: 6px;
    border: 1px solid var(--border-color);
    background-color: white;
    padding: 1rem;
}

.badge-custom {
    background-color: var(--primary-color);
    color: white;
    border-radius: 12px;
    padding: 0.35rem 0.7rem;
    font-weight: 500;
    font-size: 0.8rem;
}

.table-modern {
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid var(--border-color);
}

.table-modern thead {
    background-color: var(--primary-color);
    color: white;
}

.table-modern th {
    border: none;
    padding: 1rem;
    font-weight: 600;
    font-size: 0.9rem;
}

.table-modern td {
    padding: 1rem;
    vertical-align: middle;
    border-color: var(--border-color);
    font-size: 0.9rem;
}

.table-modern tbody tr {
    transition: all 0.2s ease;
}

.table-modern tbody tr:hover {
    background-color: rgba(13, 134, 68, 0.03);
}

.btn-action {
    border-radius: 4px;
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
    transition: all 0.3s ease;
    background-color: transparent;
    border: 1px solid var(--primary-color);
    color: var(--primary-color);
}

.btn-action:hover {
    background-color: var(--primary-color);
    color: white;
    transform: translateY(-1px);
}

.swal2-textarea {
    resize: vertical;
    min-height: 120px;
    border-radius: 6px;
    border: 1px solid var(--border-color);
    padding: 0.75rem;
}

.swal2-input {
    border-radius: 6px;
    border: 1px solid var(--border-color);
    padding: 0.75rem;
}

.recipient-count {
    font-size: 1rem;
    font-weight: 600;
    color: var(--primary-color);
}

.filter-section {
    background-color: white;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid var(--border-color);
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--primary-color);
}

.form-label {
    font-weight: 500;
    color: #333;
    margin-bottom: 0.5rem;
}

.loading-state {
    display: flex;
    align-items: center;
    color: var(--primary-color);
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: var(--border-color);
}

.status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: #28a745;
}

.status-dot.warning {
    background-color: #ffc107;
}

.status-dot.danger {
    background-color: #dc3545;
}

.card-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    margin-top: 0.25rem;
}

.char-counter {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 0.25rem;
    text-align: right;
    display: block;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .filter-section {
        padding: 1rem;
    }
    
    .table-modern th,
    .table-modern td {
        padding: 0.75rem 0.5rem;
    }
}
</style>

<!-- Section Envoi groupé SMS -->
<div class="card card-modern mt-4">
    <div class="card-header card-header-modern">
        <h3 class="card-title"><i class="fas fa-sms me-2"></i>Envoi de SMS groupés (Agent)</h3>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('agent.sms.send-group') }}" method="POST" id="smsForm">
            @csrf
            
            <div class="filter-section">
                <h4 class="section-title">Filtres de sélection</h4>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="conteneur_id" class="form-label">Filtrer par conteneur</label>
                            <select name="conteneur_id" id="conteneur_id" class="form-control form-control-modern">
                                <option value="">Tous les conteneurs</option>
                                @foreach($conteneurs as $conteneur)
                                    <option value="{{ $conteneur->id }}">
                                        {{ $conteneur->numero_conteneur }}
                                        @if($conteneur->name_conteneur)
                                            - {{ $conteneur->name_conteneur }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="type_destinataire" class="form-label">Type de destinataire</label>
                            <select name="type_destinataire" id="type_destinataire" class="form-control form-control-modern">
                                <option value="tous">Tous les clients</option>
                                <option value="expediteurs">Expéditeurs uniquement</option>
                                <option value="destinataires">Destinataires uniquement</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Destinataires concernés</label>
                            <div id="recipientPreview" class="alert alert-preview">
                                <div class="loading-state">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Sélectionnez des filtres pour voir les destinataires
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label for="message" class="form-label">Message SMS <span class="text-danger">*</span></label>
                <textarea name="message" id="message" rows="6" class="form-control form-control-modern" required placeholder="Rédigez votre SMS ici..."></textarea>
                <small id="charCounter" class="char-counter">0 / 160 caractère(s) | 1 SMS</small>
            </div>
            
            <button type="submit" class="btn btn-primary-custom px-4" id="submitBtn">
                <i class="fas fa-paper-plane me-2"></i> Envoyer les SMS groupés
            </button>
        </form>
    </div>
</div>

<!-- Section Clients et Prospects -->
<div class="row">
    <div class="col-md-12">
        <div class="card card-modern">
            <div class="card-header card-header-modern">
                <h3 class="card-title"><i class="fas fa-users me-2"></i>Liste des clients expéditeurs</h3>
                <p class="card-subtitle">Utilisateurs ayant envoyé au moins un colis</p>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="color:white; text-align:center">Nom</th>
                                <th style="color:white; text-align:center">Email</th>
                                <th style="color:white; text-align:center">Téléphone</th>
                                <th style="color:white; text-align:center">Nombre de colis</th>
                                <th style="color:white; text-align:center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clients as $user)
                            <tr>
                                <td class="fw-semibold" style="text-align: center">{{ $user->name }} {{ $user->prenom }}</td>
                                <td style="text-align: center">{{ $user->email }}</td>
                                <td style="text-align: center">{{ $user->contact ?? 'Non renseigné' }}</td>
                                <td style="text-align: center">
                                    <span class="badge badge-custom">{{ $user->colis_count }}</span>
                                </td>
                                <td style="text-align: center">
                                    <button type="button" class="btn btn-action btn-send-individual" 
                                            data-user-id="{{ $user->id }}" 
                                            data-user-name="{{ $user->name }} {{ $user->prenom }}">
                                        <i class="fas fa-sms me-1"></i> SMS individuel
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <p class="mb-0">Aucun client trouvé</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-modern">
            <div class="card-header card-header-modern text-white">
                <h3 class="card-title"><i class="fas fa-user-friends me-2"></i>Envoi groupé de prospection (Prospects)</h3>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('agent.sms.send-prospect-group') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <strong>{{ $prospects->count() }} prospect(s) concerné(s)</strong>
                                <br><small>Ces utilisateurs n'ont jamais envoyé de colis dans notre système.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="prospect_message" class="form-label">Message de prospection <span class="text-danger">*</span></label>
                                <textarea name="message" id="prospect_message" rows="4" class="form-control form-control-modern" required placeholder="Rédigez votre SMS de prospection ici..."></textarea>
                                <small id="prospectCharCounter" class="char-counter">0 / 160 caractère(s) | 1 SMS</small>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary-custom px-4">
                        <i class="fas fa-bullhorn me-2"></i> Envoyer aux prospects
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-modern">
            <div class="card-header card-header-modern">
                <h3 class="card-title"><i class="fas fa-address-book me-2"></i>Liste des prospects</h3>
                <p class="card-subtitle">Utilisateurs n'ayant jamais envoyé de colis</p>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="color:white; text-align:center">Nom</th>
                                <th style="color:white; text-align:center">Email</th>
                                <th style="color:white; text-align:center">Téléphone</th>
                                <th style="color:white; text-align:center">Adresse</th>
                                <th style="color:white; text-align:center">Inscription</th>
                                <th style="color:white; text-align:center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prospects as $user)
                            <tr>
                                <td class="fw-semibold" style="text-align: center">{{ $user->name }} {{ $user->prenom }}</td>
                                <td style="text-align: center">{{ $user->email }}</td>
                                <td style="text-align: center">{{ $user->contact ?? 'Non renseigné' }}</td>
                                <td style="text-align: center">{{ $user->adresse ?? 'Non renseignée' }}</td>
                                <td style="text-align: center">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td style="text-align: center">
                                    <button type="button" class="btn btn-action btn-send-prospect-individual" 
                                            data-user-id="{{ $user->id }}" 
                                            data-user-name="{{ $user->name }} {{ $user->prenom }}">
                                        <i class="fas fa-sms me-1"></i> SMS individuel
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <p class="mb-0">Aucun prospect trouvé</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulaires cachés pour envois individuels -->
<form id="individualSmsForm" action="{{ route('agent.sms.send-individual') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="user_id" id="individual_user_id">
    <input type="hidden" name="type_destinataire" id="individual_type_destinataire">
    <input type="hidden" name="message" id="individual_message">
</form>

<form id="prospectIndividualSmsForm" action="{{ route('agent.sms.send-prospect-individual') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="user_id" id="prospect_individual_user_id">
    <input type="hidden" name="message" id="prospect_individual_message">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la prévisualisation des destinataires groupés
    const conteneurSelect = document.getElementById('conteneur_id');
    const typeDestinataireSelect = document.getElementById('type_destinataire');
    const previewDiv = document.getElementById('recipientPreview');
    const submitBtn = document.getElementById('submitBtn');
    
    function updatePreview() {
        const conteneurId = conteneurSelect.value;
        const typeDestinataire = typeDestinataireSelect.value;
        
        previewDiv.innerHTML = `
            <div class="loading-state">
                <i class="fas fa-spinner fa-spin me-2"></i>
                <span>Chargement des contacts...</span>
            </div>
        `;
        
        fetch('{{ route("agent.sms.preview") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                conteneur_id: conteneurId,
                type_destinataire: typeDestinataire
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.count === 0) {
                previewDiv.innerHTML = `
                    <div class="status-indicator">
                        <span class="status-dot danger"></span>
                        <span class="text-danger">Aucun contact trouvé</span>
                    </div>
                `;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Aucun destinataire';
                return;
            }
            
            let html = `
                <div class="status-indicator">
                    <span class="status-dot"></span>
                    <span class="recipient-count">${data.count} contact(s) concerné(s)</span>
                </div>
            `;
            
            if (data.contacts && data.contacts.length > 0) {
                html += `<div class="mt-2"><small class="text-muted"><strong>Numéros:</strong> ${data.contacts.join(', ')}`;
                if (data.moreCount > 0) {
                    html += ` ... et ${data.moreCount} autres`;
                }
                html += `</small></div>`;
            }
            
            previewDiv.innerHTML = html;
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Envoyer les SMS groupés';
        })
        .catch(error => {
            console.error('Error:', error);
            previewDiv.innerHTML = `
                <div class="status-indicator">
                    <span class="status-dot warning"></span>
                    <span class="text-warning">Erreur lors du chargement</span>
                </div>
            `;
        });
    }
    
    conteneurSelect.addEventListener('change', updatePreview);
    typeDestinataireSelect.addEventListener('change', updatePreview);
    updatePreview();

    // Gestion dynamique des compteurs de caractères
    function setupCharCounter(textareaId, counterId) {
        const textarea = document.getElementById(textareaId);
        const counter = document.getElementById(counterId);
        
        if (!textarea || !counter) return;
        
        function updateCounter() {
            const count = textarea.value.length;
            let smsParts = 1;
            
            if (count > 160) {
                smsParts = Math.ceil(count / 153);
            }
            
            counter.textContent = `${count} / 160 caractère(s) | ${smsParts} SMS`;
            
            if (count > 160) {
                counter.style.color = '#ff9900';
            } else {
                counter.style.color = '#6c757d';
            }
        }
        
        textarea.addEventListener('input', updateCounter);
        updateCounter();
    }
    
    setupCharCounter('message', 'charCounter');
    setupCharCounter('prospect_message', 'prospectCharCounter');

    // Gestion de l'envoi individuel pour les clients
    const individualButtons = document.querySelectorAll('.btn-send-individual');
    
    individualButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            
            Swal.fire({
                title: 'Destinataire du SMS',
                text: `Voulez-vous envoyer au client (Expéditeur) ou à son Destinataire ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Expéditeur',
                cancelButtonText: 'Destinataire',
                confirmButtonColor: '#0d8644',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    showSmsForm(userId, userName, 'expediteur');
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    showSmsForm(userId, userName, 'destinataire');
                }
            });
        });
    });
    
    function showSmsForm(userId, userName, typeDestinataire) {
        const typeText = typeDestinataire === 'expediteur' ? 'Expéditeur' : 'Destinataire';
        
        Swal.fire({
            title: `Envoyer un SMS au ${typeText.toLowerCase()}`,
            html: `
                <div class="mb-2 text-start"><small class="text-muted">Pour : ${userName}</small></div>
                <textarea id="individual_sms_input" class="swal2-textarea" placeholder="Rédigez votre SMS ici..." rows="4" required></textarea>
                <div class="text-end"><small id="swalCharCounter" class="text-muted">0 / 160 | 1 SMS</small></div>
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Envoyer',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#0d8644',
            didOpen: () => {
                const textInput = document.getElementById('individual_sms_input');
                const counter = document.getElementById('swalCharCounter');
                
                textInput.addEventListener('input', () => {
                    const count = textInput.value.length;
                    let parts = 1;
                    if (count > 160) {
                        parts = Math.ceil(count / 153);
                    }
                    counter.textContent = `${count} / 160 | ${parts} SMS`;
                    if (count > 160) {
                        counter.style.color = '#ff9900';
                    } else {
                        counter.style.color = '#6c757d';
                    }
                });
            },
            preConfirm: () => {
                const message = document.getElementById('individual_sms_input').value;
                if (!message) {
                    Swal.showValidationMessage('Veuillez saisir un message');
                    return false;
                }
                return message;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('individual_user_id').value = userId;
                document.getElementById('individual_type_destinataire').value = typeDestinataire;
                document.getElementById('individual_message').value = result.value;
                document.getElementById('individualSmsForm').submit();
            }
        });
    }

    // Gestion de l'envoi individuel pour les prospects
    const prospectIndividualButtons = document.querySelectorAll('.btn-send-prospect-individual');
    
    prospectIndividualButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            
            Swal.fire({
                title: `Envoyer un SMS au prospect`,
                html: `
                    <div class="mb-2 text-start"><small class="text-muted">Pour : ${userName}</small></div>
                    <textarea id="prospect_individual_sms_input" class="swal2-textarea" placeholder="Rédigez votre SMS ici..." rows="4" required></textarea>
                    <div class="text-end"><small id="swalProspectCharCounter" class="text-muted">0 / 160 | 1 SMS</small></div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Envoyer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#0d8644',
                didOpen: () => {
                    const textInput = document.getElementById('prospect_individual_sms_input');
                    const counter = document.getElementById('swalProspectCharCounter');
                    
                    textInput.addEventListener('input', () => {
                        const count = textInput.value.length;
                        let parts = 1;
                        if (count > 160) {
                            parts = Math.ceil(count / 153);
                        }
                        counter.textContent = `${count} / 160 | ${parts} SMS`;
                        if (count > 160) {
                            counter.style.color = '#ff9900';
                        } else {
                            counter.style.color = '#6c757d';
                        }
                    });
                },
                preConfirm: () => {
                    const message = document.getElementById('prospect_individual_sms_input').value;
                    if (!message) {
                        Swal.showValidationMessage('Veuillez saisir un message');
                        return false;
                    }
                    return message;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('prospect_individual_user_id').value = userId;
                    document.getElementById('prospect_individual_message').value = result.value;
                    document.getElementById('prospectIndividualSmsForm').submit();
                }
            });
        });
    });

    // Afficher les notifications de session (SweetAlert2)
    @if(session('success'))
        Swal.fire({
            title: 'Succès !',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#0d8644',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Erreur !',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#0d8644'
        });
    @endif
});
</script>
@endsection
