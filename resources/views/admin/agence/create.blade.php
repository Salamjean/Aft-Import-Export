@extends('admin.layouts.template')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-12 col-xl-12">

            <!-- Carte du formulaire -->
            <div class="card shadow-sm mb-5">
                <div class="card-header {{ isset($agence) ? 'bg-warning text-dark' : 'bg-primary text-white' }}">
                    <h3 class="card-title mb-0">
                        <i class="fas {{ isset($agence) ? 'fa-edit' : 'fa-building' }} me-2"></i>
                        {{ isset($agence) ? 'Modifier l\'Agence' : 'Nouvelle Agence' }}
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ isset($agence) ? route('agence.update', $agence->id) : route('agence.store') }}" method="POST" id="agenceForm">
                        @csrf
                        @if(isset($agence))
                            @method('PUT')
                        @endif
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label fw-bold">Nom de l'agence <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" 
                                           value="{{ old('name', $agence->name ?? '') }}" 
                                           placeholder="Entrez le nom de l'agence" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="pays" class="form-label fw-bold">Pays <span class="text-danger">*</span></label>
                                    <select class="form-control @error('pays') is-invalid @enderror" 
                                            id="pays" name="pays" required>
                                        <option value="">Sélectionnez un pays</option>
                                        <option value="Côte d'Ivoire" {{ old('pays', $agence->pays ?? '') == 'Côte d\'Ivoire' ? 'selected' : '' }}>Côte d'Ivoire</option>
                                        <option value="France" {{ old('pays', $agence->pays ?? '') == 'France' ? 'selected' : '' }}>France</option>
                                        <option value="Chine" {{ old('pays', $agence->pays ?? '') == 'Chine' ? 'selected' : '' }}>Chine</option>
                                    </select>
                                    @error('pays')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="devise" class="form-label fw-bold">Devise <span class="text-danger">*</span></label>
                                    <select class="form-control @error('devise') is-invalid @enderror" 
                                            id="devise" name="devise" required>
                                        <option value="">Sélectionnez une devise</option>
                                        <option value="EUR" {{ old('devise', $agence->devise ?? '') == 'EUR' ? 'selected' : '' }}>Euro (€)</option>
                                        <option value="XOF" {{ old('devise', $agence->devise ?? '') == 'XOF' ? 'selected' : '' }}>Franc CFA (XOF)</option>
                                    </select>
                                    @error('devise')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="adresse" class="form-label fw-bold">Adresse <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('adresse') is-invalid @enderror" 
                                      id="adresse" name="adresse" rows="3" 
                                      placeholder="Entrez l'adresse complète de l'agence" required>{{ old('adresse', $agence->adresse ?? '') }}</textarea>
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('agence.create') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                            </a>
                            <div>
                                @if(isset($agence))
                                    <a href="{{ route('agence.create') }}" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </a>
                                @endif
                                <button type="submit" class="btn {{ isset($agence) ? 'btn-warning' : 'btn-success' }}">
                                    <i class="fas {{ isset($agence) ? 'fa-sync' : 'fa-save' }} me-2"></i>
                                    {{ isset($agence) ? 'Mettre à jour' : 'Enregistrer' }} l'agence
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des agences existantes -->
            <div class="card shadow-sm">
                <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #0b7a3d">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Liste des Agences
                    </h3>
                    <span class="badge bg-warning text-dark fs-6">
                        {{ $agences->total() }} agence(s) au total
                    </span>
                </div>
                <div class="card-body p-0">
                    @if($agences->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3 px-4 text-center">Nom</th>
                                        <th class="py-3 px-4 text-center">Pays</th>
                                        <th class="py-3 px-4 text-center">Devise</th>
                                        <th class="py-3 px-4 text-center">Adresse</th>
                                        <th class="py-3 px-4 text-center">Date création</th>
                                        <th class="py-3 px-4 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($agences as $agenceItem)
                                    <tr class="{{ isset($agence) && $agence->id == $agenceItem->id ? 'table-warning' : '' }}" 
                                        id="agence-{{ $agenceItem->id }}">
                                        <td class="py-3 px-4">
                                            <div class="d-flex align-items-center" style="display: flex; justify-content:center">
                                                <div class="bg-primary rounded-circle p-2 me-3">
                                                    <i class="fas fa-building text-white"></i>
                                                </div>
                                                <span class="fw-medium">{{ $agenceItem->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="badge bg-info text-dark">{{ $agenceItem->pays }}</span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="badge bg-success">{{ $agenceItem->devise }}</span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <small class="text-black">{{ Str::limit($agenceItem->adresse, 40) }}</small>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <small class="text-black">{{ $agenceItem->created_at->format('d/m/Y') }}</small>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="btn-group" role="group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary edit-agence-btn" 
                                                        title="Modifier"
                                                        data-agence-id="{{ $agenceItem->id }}"
                                                        data-agence-name="{{ $agenceItem->name }}"
                                                        data-agence-pays="{{ $agenceItem->pays }}"
                                                        data-agence-devise="{{ $agenceItem->devise }}"
                                                        data-agence-adresse="{{ $agenceItem->adresse }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('agence.destroy', $agenceItem->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger delete-agence-btn" 
                                                            title="Supprimer"
                                                            data-agence-name="{{ $agenceItem->name }}">
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
                        @if($agences->hasPages())
                        <div class="card-footer bg-white border-top">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="text-muted mb-2 mb-md-0">
                                    Affichage de <strong>{{ $agences->firstItem() }}</strong> à 
                                    <strong>{{ $agences->lastItem() }}</strong> sur 
                                    <strong>{{ $agences->total() }}</strong> agences
                                </div>
                                
                                <nav aria-label="Pagination">
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item {{ $agences->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $agences->url(1) }}" aria-label="Première">
                                                <i class="fas fa-angle-double-left"></i>
                                            </a>
                                        </li>
                                        
                                        <li class="page-item {{ $agences->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $agences->previousPageUrl() }}" aria-label="Précédent">
                                                <i class="fas fa-angle-left"></i>
                                            </a>
                                        </li>

                                        @foreach($agences->getUrlRange(max(1, $agences->currentPage() - 2), min($agences->lastPage(), $agences->currentPage() + 2)) as $page => $url)
                                            <li class="page-item {{ $page == $agences->currentPage() ? 'active' : '' }}">
                                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                            </li>
                                        @endforeach

                                        <li class="page-item {{ $agences->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link" href="{{ $agences->nextPageUrl() }}" aria-label="Suivant">
                                                <i class="fas fa-angle-right"></i>
                                            </a>
                                        </li>
                                        
                                        <li class="page-item {{ $agences->currentPage() == $agences->lastPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $agences->url($agences->lastPage()) }}" aria-label="Dernière">
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
                                <i class="fas fa-building fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">Aucune agence enregistrée</h5>
                            <p class="text-muted">Commencez par créer votre première agence en utilisant le formulaire ci-dessus.</p>
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
    
    .bg-warning {
        background: linear-gradient(135deg, #fea219 0%, #e69100 100%) !important;
    }
    
    .bg-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
    }
    
    .btn-success {
        background: linear-gradient(135deg, #0e914b 0%, #0b7a3d 100%);
        border: none;
    }
    
    .btn-warning {
        background: linear-gradient(135deg, #fea219 0%, #e69100 100%);
        border: none;
        color: white;
    }
    
    .btn-success:hover {
        background: linear-gradient(135deg, #0b7a3d 0%, #08622f 100%);
        transform: translateY(-1px);
    }
    
    .btn-warning:hover {
        background: linear-gradient(135deg, #e69100 0%, #cc8100 100%);
        transform: translateY(-1px);
        color: white;
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
// Mettre en évidence la nouvelle agence créée ou modifiée
document.addEventListener('DOMContentLoaded', function() {
    const highlightAgenceId = {{ session('highlight_agence') ?? 'null' }};
    
    if (highlightAgenceId) {
        const newAgenceRow = document.getElementById('agence-' + highlightAgenceId);
        if (newAgenceRow) {
            newAgenceRow.classList.add('table-warning');
            
            // Scroll vers la nouvelle agence
            setTimeout(() => {
                newAgenceRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 500);
        }
    }
    
    // Auto-dismiss des alertes après 5 secondes
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.isConnected) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });
});

// Édition d'agence avec SweetAlert2
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-agence-btn');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const agenceId = this.getAttribute('data-agence-id');
            const agenceName = this.getAttribute('data-agence-name');
            const agencePays = this.getAttribute('data-agence-pays');
            const agenceDevise = this.getAttribute('data-agence-devise');
            const agenceAdresse = this.getAttribute('data-agence-adresse');
            
            // Options des pays
            const paysOptions = `
                <option value="Côte d'Ivoire" ${agencePays === "Côte d'Ivoire" ? 'selected' : ''}>Côte d'Ivoire</option>
                <option value="France" ${agencePays === 'France' ? 'selected' : ''}>France</option>
                <option value="Chine" ${agencePays === 'Chine' ? 'selected' : ''}>Chine</option>
            `;
            
            // Options des devises
            const deviseOptions = `
                <option value="EUR" ${agenceDevise === 'EUR' ? 'selected' : ''}>Euro (€)</option>
                <option value="XOF" ${agenceDevise === 'XOF' ? 'selected' : ''}>Franc CFA (XOF)</option>
            `;
            
            Swal.fire({
                title: `Modifier l'agence "${agenceName}"`,
                html: `
                    <form id="editAgenceForm">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label fw-bold">Nom de l'agence <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" value="${agenceName}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_pays" class="form-label fw-bold">Pays <span class="text-danger">*</span></label>
                            <select class="form-control" id="edit_pays" name="pays" required>
                                <option value="">Sélectionnez un pays</option>
                                ${paysOptions}
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_devise" class="form-label fw-bold">Devise <span class="text-danger">*</span></label>
                            <select class="form-control" id="edit_devise" name="devise" required>
                                <option value="">Sélectionnez une devise</option>
                                ${deviseOptions}
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_adresse" class="form-label fw-bold">Adresse <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_adresse" name="adresse" rows="3" required>${agenceAdresse}</textarea>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Mettre à jour',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#0e914b',
                cancelButtonColor: '#6c757d',
                background: '#fff',
                preConfirm: () => {
                    const name = document.getElementById('edit_name').value;
                    const pays = document.getElementById('edit_pays').value;
                    const devise = document.getElementById('edit_devise').value;
                    const adresse = document.getElementById('edit_adresse').value;
                    
                    if (!name || !pays || !devise || !adresse) {
                        Swal.showValidationMessage('Veuillez remplir tous les champs obligatoires');
                        return false;
                    }
                    
                    return { name, pays, devise, adresse };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Envoi des données via AJAX
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'PUT');
                    formData.append('name', result.value.name);
                    formData.append('pays', result.value.pays);
                    formData.append('devise', result.value.devise);
                    formData.append('adresse', result.value.adresse);
                    
                    // Récupérer l'URL de base de la route
                     fetch(`{{ route('agence.update', ':id') }}`.replace(':id', agenceId), {
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
});

// Suppression d'agence avec SweetAlert2
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-agence-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const agenceName = this.getAttribute('data-agence-name');
            const form = this.closest('form');
            
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                html: `Vous êtes sur le point de supprimer l'agence <strong>"${agenceName}"</strong>.<br>Cette action est irréversible !`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0e914b',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler',
                background: '#fff',
                iconColor: '#fea219'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

<!-- Inclure SweetAlert2 pour les confirmations stylisées -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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

@endsection