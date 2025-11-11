@extends('admin.layouts.template')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-12 col-xl-12">
            
            <!-- Messages d'alerte -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-warning alert-dismissible fade show mx-3 mt-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Veuillez corriger les erreurs suivantes :</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Carte du formulaire -->
            <div class="card shadow-sm mb-5">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user-plus me-2"></i>Nouvel Agent
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('agent.store') }}" method="POST" id="agentForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="Entrez le nom de l'agent" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="prenom" class="form-label fw-bold">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                                           id="prenom" name="prenom" 
                                           value="{{ old('prenom') }}" 
                                           placeholder="Entrez le prénom de l'agent" required>
                                    @error('prenom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="Entrez l'email de l'agent" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label fw-bold">Mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" 
                                           placeholder="Minimum 8 caractères" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password_confirmation" class="form-label fw-bold">Confirmation mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" 
                                           placeholder="Confirmez le mot de passe" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="contact" class="form-label fw-bold">Contact <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('contact') is-invalid @enderror" 
                                           id="contact" name="contact" 
                                           value="{{ old('contact') }}" 
                                           placeholder="Ex: +225 07 00 00 00 00" required>
                                    @error('contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="agence_id" class="form-label fw-bold">Agence <span class="text-danger">*</span></label>
                                    <select class="form-control @error('agence_id') is-invalid @enderror" 
                                            id="agence_id" name="agence_id" required>
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
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Enregistrer l'agent
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des agents existants -->
            <div class="card shadow-sm">
                <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #0d8644">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Liste des Agents
                    </h3>
                    <span class="badge bg-warning text-dark fs-6">
                        {{ $agents->total() }} agent(s) au total
                    </span>
                </div>
                <div class="card-body p-0">
                    @if($agents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3 px-4 text-center">Agent</th>
                                        <th class="py-3 px-4 text-center">Contact</th>
                                        <th class="py-3 px-4 text-center">Agence</th>
                                        <th class="py-3 px-4 text-center">Date création</th>
                                        <th class="py-3 px-4 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($agents as $agentItem)
                                    <tr id="agent-{{ $agentItem->id }}">
                                        <td class="py-3 px-4 text-center">
                                            <div class="d-flex align-items-center" style="display: flex; justify-content:center">
                                                <div class="bg-primary rounded-circle p-2 me-3">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $agentItem->prenom }} {{ $agentItem->name }}</div>
                                                    <small class="text-muted">{{ $agentItem->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <small class="text-muted">{{ $agentItem->contact }}</small>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="badge bg-info text-dark">{{ $agentItem->agence->name ?? 'agence non trouvé' }}</span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <small class="text-muted">{{ $agentItem->created_at->format('d/m/Y') }}</small>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="btn-group" role="group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary edit-agent-btn" 
                                                        title="Modifier"
                                                        data-agent-id="{{ $agentItem->id }}"
                                                        data-agent-name="{{ $agentItem->name }}"
                                                        data-agent-prenom="{{ $agentItem->prenom }}"
                                                        data-agent-email="{{ $agentItem->email }}"
                                                        data-agent-contact="{{ $agentItem->contact }}"
                                                        data-agent-agence-id="{{ $agentItem->agence_id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('agent.destroy', $agentItem->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger delete-agent-btn" 
                                                            title="Supprimer"
                                                            data-agent-name="{{ $agentItem->prenom }} {{ $agentItem->name }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($agents->hasPages())
                        <div class="card-footer bg-white border-top">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="text-muted mb-2 mb-md-0">
                                    Affichage de <strong>{{ $agents->firstItem() }}</strong> à 
                                    <strong>{{ $agents->lastItem() }}</strong> sur 
                                    <strong>{{ $agents->total() }}</strong> agents
                                </div>
                                
                                <nav aria-label="Pagination">
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item {{ $agents->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $agents->url(1) }}" aria-label="Première">
                                                <i class="fas fa-angle-double-left"></i>
                                            </a>
                                        </li>
                                        
                                        <li class="page-item {{ $agents->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $agents->previousPageUrl() }}" aria-label="Précédent">
                                                <i class="fas fa-angle-left"></i>
                                            </a>
                                        </li>

                                        @foreach($agents->getUrlRange(max(1, $agents->currentPage() - 2), min($agents->lastPage(), $agents->currentPage() + 2)) as $page => $url)
                                            <li class="page-item {{ $page == $agents->currentPage() ? 'active' : '' }}">
                                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                            </li>
                                        @endforeach

                                        <li class="page-item {{ $agents->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link" href="{{ $agents->nextPageUrl() }}" aria-label="Suivant">
                                                <i class="fas fa-angle-right"></i>
                                            </a>
                                        </li>
                                        
                                        <li class="page-item {{ $agents->currentPage() == $agents->lastPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $agents->url($agents->lastPage()) }}" aria-label="Dernière">
                                                <i class="fas fa-angle-double-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        @endif

                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-users fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">Aucun agent enregistré</h5>
                            <p class="text-muted">Commencez par créer votre premier agent en utilisant le formulaire ci-dessus.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary {
        background: linear-gradient(135deg, #0e914b 0%, #0b7a3d 100%) !important;
    }
    
    .bg-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
    }
    
    .btn-success {
        background: linear-gradient(135deg, #0e914b 0%, #0b7a3d 100%);
        border: none;
    }
    
    .btn-success:hover {
        background: linear-gradient(135deg, #0b7a3d 0%, #08622f 100%);
        transform: translateY(-1px);
    }
    
    .table-warning {
        background-color: rgba(254, 162, 25, 0.1) !important;
        border-left: 4px solid #fea219;
    }
    
    .swal2-popup {
        border-radius: 12px !important;
    }
</style>

<script>
// Mettre en évidence le nouvel agent créé ou modifié
document.addEventListener('DOMContentLoaded', function() {
    const highlightAgentId = {{ session('highlight_agent') ?? 'null' }};
    
    if (highlightAgentId) {
        const newAgentRow = document.getElementById('agent-' + highlightAgentId);
        if (newAgentRow) {
            newAgentRow.classList.add('table-warning');
            
            // Scroll vers le nouvel agent
            setTimeout(() => {
                newAgentRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 500);
        }
    }
    
    // Édition d'agent avec SweetAlert2
    const editButtons = document.querySelectorAll('.edit-agent-btn');
    
    editButtons.forEach(button => {
        
        button.addEventListener('click', function() {
            const agentId = this.getAttribute('data-agent-id');
            const agentName = this.getAttribute('data-agent-name');
            const agentPrenom = this.getAttribute('data-agent-prenom');
            const agentEmail = this.getAttribute('data-agent-email');
            const agentContact = this.getAttribute('data-agent-contact');
            const agentAgenceId = this.getAttribute('data-agent-agence-id');
            
            // Options des agences
            let agencesOptions = '';
            @foreach($agences as $agence)
                agencesOptions += `<option value="{{ $agence->id }}" ${agentAgenceId == {{ $agence->id }} ? 'selected' : ''}>{{ $agence->name }} - {{ $agence->pays }}</option>`;
            @endforeach
            
            Swal.fire({
                title: `Modifier l'agent "${agentPrenom} ${agentName}"`,
                html: `
                    <form id="editAgentForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_name" class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_name" name="name" value="${agentName}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_prenom" class="form-label fw-bold">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_prenom" name="prenom" value="${agentPrenom}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" value="${agentEmail}" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_contact" class="form-label fw-bold">Contact <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_contact" name="contact" value="${agentContact}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_agence_id" class="form-label fw-bold">Agence <span class="text-danger">*</span></label>
                                    <select class="form-control" id="edit_agence_id" name="agence_id" required>
                                        <option value="">Sélectionnez une agence</option>
                                        ${agencesOptions}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Mettre à jour',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#0e914b',
                cancelButtonColor: '#6c757d',
                background: '#fff',
                width: '600px',
                preConfirm: () => {
                    const name = document.getElementById('edit_name').value;
                    const prenom = document.getElementById('edit_prenom').value;
                    const email = document.getElementById('edit_email').value;
                    const contact = document.getElementById('edit_contact').value;
                    const agence_id = document.getElementById('edit_agence_id').value;
                    
                    if (!name || !prenom || !email || !contact || !agence_id) {
                        Swal.showValidationMessage('Veuillez remplir tous les champs obligatoires');
                        return false;
                    }
                    
                    if (!isValidEmail(email)) {
                        Swal.showValidationMessage('Veuillez entrer un email valide');
                        return false;
                    }
                    
                    return { name, prenom, email, contact, agence_id };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Envoi des données via AJAX
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'PUT');
                    formData.append('name', result.value.name);
                    formData.append('prenom', result.value.prenom);
                    formData.append('email', result.value.email);
                    formData.append('contact', result.value.contact);
                    formData.append('agence_id', result.value.agence_id);
                    
                   fetch(`{{ route('agent.update', ':id') }}`.replace(':id', agentId), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })

                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Succès !',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#0e914b'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Erreur !',
                                text: data.message,
                                icon: 'error',
                                confirmButtonColor: '#0e914b'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Erreur !',
                            text: 'Une erreur est survenue lors de la mise à jour',
                            icon: 'error',
                            confirmButtonColor: '#0e914b'
                        });
                    });
                }
            });
        });
    });
    
    // Fonction de validation d'email
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Confirmation de suppression
    const deleteButtons = document.querySelectorAll('.delete-agent-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const agentName = this.getAttribute('data-agent-name');
            const form = this.closest('form');
            
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                html: `Vous êtes sur le point de supprimer l'agent <strong>"${agentName}"</strong>.<br>Cette action est irréversible !`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0e914b',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});

// SweetAlert notifications
@if (Session::has('success'))
    Swal.fire({
        icon: 'success',
        title: 'Succès',
        text: '{{ Session::get('success') }}',
        confirmButtonText: 'OK',
        background: 'white',
    });
@endif

@if (Session::has('error'))
    Swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: '{{ Session::get('error') }}',
        confirmButtonText: 'OK',
        background: 'white',
    });
@endif
</script>

<!-- Inclure SweetAlert2 pour les confirmations stylisées -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection