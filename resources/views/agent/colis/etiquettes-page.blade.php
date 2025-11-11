@extends('admin.layouts.template')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card modern-card">
                <div class="card-header modern-header">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                        <div class="header-text">
                            <h3 class="card-title">Génération d'Étiquettes</h3>
                            <p class="card-subtitle">Colis: {{ $colis->reference_colis }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Informations</h5>
                            <p class="mb-1"><strong>Référence:</strong> {{ $colis->reference_colis }}</p>
                            <p class="mb-1"><strong>Nombre d'étiquettes à générer:</strong> {{ $quantiteTotale }}</p>
                            <p class="mb-0"><strong>Destinataire:</strong> {{ $colis->name_destinataire }} {{ $colis->prenom_destinataire }}</p>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <a href="{{ route('colis.etiquettes', ['colis' => $colis->id, 'action' => 'print']) }}" 
                                       class="btn btn-primary w-100 py-4 h-100 d-flex flex-column align-items-center justify-content-center"
                                       target="_blank">
                                        <i class="fas fa-print fa-3x mb-3"></i>
                                        <h5>Imprimer</h5>
                                        <small>Ouvre l'imprimante PDF</small>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('agent.colis.etiquettes', ['colis' => $colis->id, 'action' => 'download']) }}" 
                                       class="btn btn-success w-100 py-4 h-100 d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-download fa-3x mb-3"></i>
                                        <h5>Télécharger</h5>
                                        <small>Télécharge le PDF</small>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('agent.colis.etiquettes', ['colis' => $colis->id, 'action' => 'preview']) }}" 
                                       class="btn btn-info w-100 py-4 h-100 d-flex flex-column align-items-center justify-content-center"
                                       target="_blank">
                                        <i class="fas fa-eye fa-3x mb-3"></i>
                                        <h5>Aperçu</h5>
                                        <small>Voir dans le navigateur</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <a href="{{ route('agent.colis.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection