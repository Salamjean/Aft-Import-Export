@extends('agent.layouts.template')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Section Envoi groupé aux prospects -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Envoi des messages groupés aux prospects</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.send-prospect-group-email') }}" method="POST" id="emailForm">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="sujet">Sujet *</label>
                        <input type="text" name="sujet" id="sujet" class="form-control" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Destinataires concernés</label>
                        <div class="alert alert-info">
                            <strong>{{ $users->count() }} prospect(s) concerné(s)</strong>
                            <br><small>Ces utilisateurs n'ont jamais envoyé de colis</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="contenu">Contenu de l'email *</label>
                <textarea name="contenu" id="contenu" rows="10" class="form-control" required placeholder="Rédigez votre message de prospection ici..."></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Envoyer aux prospects
            </button>
        </form>
    </div>
</div>

<!-- Section Liste des prospects -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Liste des prospects</h3>
        <p class="card-subtitle">Utilisateurs n'ayant jamais envoyé de colis</p>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                        <th>Date d'inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }} {{ $user->prenom }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->contact ?? 'Non renseigné' }}</td>
                        <td>{{ $user->adresse ?? 'Non renseignée' }}</td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning btn-send-individual" 
                                    data-user-id="{{ $user->id }}" 
                                    data-user-name="{{ $user->name }} {{ $user->prenom }}"
                                    data-user-email="{{ $user->email }}">
                                <i class="fas fa-envelope"></i> Envoyer un message
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Aucun prospect trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->count() > 0)
        <div class="mt-3">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Total: <strong>{{ $users->count() }}</strong> prospect(s) n'ayant jamais envoyé de colis
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Formulaire caché pour l'envoi individuel aux prospects -->
<form id="individualEmailForm" action="{{ route('admin.send-prospect-individual-email') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="user_id" id="individual_user_id">
    <input type="hidden" name="sujet" id="individual_sujet">
    <input type="hidden" name="contenu" id="individual_contenu">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'envoi individuel aux prospects
    const individualButtons = document.querySelectorAll('.btn-send-individual');
    
    individualButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            const userEmail = this.getAttribute('data-user-email');
            
            Swal.fire({
                title: `Envoyer un message à ${userName}`,
                html: `
                    <div class="text-start mb-3">
                        <small class="text-muted">Email: ${userEmail}</small>
                    </div>
                    <input type="text" id="individual_sujet_input" class="swal2-input" placeholder="Sujet du message" required>
                    <textarea id="individual_contenu_input" class="swal2-textarea" placeholder="Contenu du message..." rows="6" required></textarea>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Envoyer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#3085d6',
                preConfirm: () => {
                    const sujet = document.getElementById('individual_sujet_input').value;
                    const contenu = document.getElementById('individual_contenu_input').value;
                    
                    if (!sujet || !contenu) {
                        Swal.showValidationMessage('Veuillez remplir tous les champs');
                        return false;
                    }
                    
                    return { sujet, contenu };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Remplir le formulaire caché et le soumettre
                    document.getElementById('individual_user_id').value = userId;
                    document.getElementById('individual_sujet').value = result.value.sujet;
                    document.getElementById('individual_contenu').value = result.value.contenu;
                    
                    document.getElementById('individualEmailForm').submit();
                }
            });
        });
    });

    // Afficher les messages flash Laravel avec SweetAlert2
    @if(session('success'))
        Swal.fire({
            title: 'Succès !',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#3085d6',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Erreur !',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#3085d6'
        });
    @endif
});
</script>

<style>
.swal2-textarea {
    resize: vertical;
    min-height: 120px;
}
</style>
@endsection