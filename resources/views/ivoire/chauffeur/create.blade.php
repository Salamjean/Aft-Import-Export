@extends('ivoire.layouts.template')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-12 col-xl-12">

            <!-- Carte du formulaire -->
            <div class="card shadow-sm mb-5">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user-tie me-2"></i>Nouveau Chauffeur
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('ivoire.chauffeur.store') }}" method="POST" id="chauffeurForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="Entrez le nom du chauffeur" required>
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
                                           placeholder="Entrez le prénom du chauffeur" required>
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
                                           placeholder="Entrez l'email du chauffeur" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
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
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="password_confirmation" class="form-label fw-bold">Confirmation mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" 
                                           placeholder="Confirmez le mot de passe" required>
                                </div>
                            </div>
                            <div class="col-md-4">
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
                        </div>

                        {{-- <div class="row">
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
                        </div> --}}
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Reinitialiser
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Enregistrer le chauffeur
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des chauffeurs existants -->
            <div class="card shadow-sm">
                <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #0d8644">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Liste des Chauffeurs
                    </h3>
                    <span class="badge bg-warning text-dark fs-6">
                        {{ $chauffeurs->total() }} chauffeur(s) au total
                    </span>
                </div>
                <div class="card-body p-0">
                    @if($chauffeurs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3 px-4 text-center">Chauffeur</th>
                                        <th class="py-3 px-4 text-center">Contact</th>
                                        <th class="py-3 px-4 text-center">Agence</th>
                                        <th class="py-3 px-4 text-center">Statut</th>
                                        <th class="py-3 px-4 text-center">Date création</th>
                                        <th class="py-3 px-4 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($chauffeurs as $chauffeur)
                                    <tr id="chauffeur-{{ $chauffeur->id }}">
                                        <td class="py-3 px-4 text-center">
                                            <div class="d-flex align-items-center" style="display: flex; justify-content:center">
                                                <div class="bg-primary rounded-circle p-2 me-3">
                                                    <i class="fas fa-user-tie text-white"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $chauffeur->prenom }} {{ $chauffeur->name }}</div>
                                                    <small class="text-muted">{{ $chauffeur->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <small class="text-muted">{{ $chauffeur->contact }}</small>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="badge bg-info text-dark">{{ $chauffeur->agence->name ?? 'Agence non trouvée' }}</span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if($chauffeur->archived_at)
                                                <span class="badge bg-danger">Archivé</span>
                                            @elseif($chauffeur->email_verified_at)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-warning text-dark">En attente</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <small class="text-muted">{{ $chauffeur->created_at->format('d/m/Y') }}</small>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="btn-group" role="group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary edit-chauffeur-btn" 
                                                        title="Modifier"
                                                        data-chauffeur-id="{{ $chauffeur->id }}"
                                                        data-chauffeur-name="{{ $chauffeur->name }}"
                                                        data-chauffeur-prenom="{{ $chauffeur->prenom }}"
                                                        data-chauffeur-email="{{ $chauffeur->email }}"
                                                        data-chauffeur-contact="{{ $chauffeur->contact }}"
                                                        data-chauffeur-agence-id="{{ $chauffeur->agence_id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('ivoire.chauffeur.destroy', $chauffeur->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger delete-chauffeur-btn" 
                                                            title="Supprimer"
                                                            data-chauffeur-name="{{ $chauffeur->prenom }} {{ $chauffeur->name }}">
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
                        @if($chauffeurs->hasPages())
                            <div class="pagination-container">
                                {{ $chauffeurs->links('pagination.modern') }}
                            </div>
                        @endif

                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-user-tie fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">Aucun chauffeur enregistré</h5>
                            <p class="text-muted">Commencez par créer votre premier chauffeur en utilisant le formulaire ci-dessus.</p>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Mettre en évidence le nouveau chauffeur créé ou modifié
document.addEventListener('DOMContentLoaded', function() {
    const highlightChauffeurId = {{ session('highlight_chauffeur') ?? 'null' }};
    
    if (highlightChauffeurId) {
        const newChauffeurRow = document.getElementById('chauffeur-' + highlightChauffeurId);
        if (newChauffeurRow) {
            newChauffeurRow.classList.add('table-warning');
            
            // Scroll vers le nouveau chauffeur
            setTimeout(() => {
                newChauffeurRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 500);
        }
    }
    
    // Édition de chauffeur avec SweetAlert2
    const editButtons = document.querySelectorAll('.edit-chauffeur-btn');
    
    editButtons.forEach(button => {
        
        button.addEventListener('click', function() {
            const chauffeurId = this.getAttribute('data-chauffeur-id');
            const chauffeurName = this.getAttribute('data-chauffeur-name');
            const chauffeurPrenom = this.getAttribute('data-chauffeur-prenom');
            const chauffeurEmail = this.getAttribute('data-chauffeur-email');
            const chauffeurContact = this.getAttribute('data-chauffeur-contact');
            const chauffeurAgenceId = this.getAttribute('data-chauffeur-agence-id');
            
            // Options des agences
            let agencesOptions = '';
            @foreach($agences as $agence)
                agencesOptions += `<option value="{{ $agence->id }}" ${chauffeurAgenceId == {{ $agence->id }} ? 'selected' : ''}>{{ $agence->name }} - {{ $agence->pays }}</option>`;
            @endforeach
            
            Swal.fire({
                title: `Modifier le chauffeur "${chauffeurPrenom} ${chauffeurName}"`,
                html: `
                    <form id="editChauffeurForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_name" class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_name" name="name" value="${chauffeurName}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_prenom" class="form-label fw-bold">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_prenom" name="prenom" value="${chauffeurPrenom}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" value="${chauffeurEmail}" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_contact" class="form-label fw-bold">Contact <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_contact" name="contact" value="${chauffeurContact}" required>
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
                    
                   fetch(`{{ route('ivoire.chauffeur.update', ':id') }}`.replace(':id', chauffeurId), {
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
    const deleteButtons = document.querySelectorAll('.delete-chauffeur-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const chauffeurName = this.getAttribute('data-chauffeur-name');
            const form = this.closest('form');
            
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                html: `Vous êtes sur le point de supprimer le chauffeur <strong>"${chauffeurName}"</strong>.<br>Cette action est irréversible !`,
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

// Afficher les messages flash Laravel avec SweetAlert2
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            title: 'Succès !',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#2196F3',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Erreur !',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#2196F3'
        });
    @endif
});
</script>

<!-- Inclure SweetAlert2 pour les confirmations stylisées -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection