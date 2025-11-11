@extends('agent.layouts.template')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card modern-card">
                <div class="card-header modern-header">
                    <div class="header-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <h3 class="card-title">Modifier le conteneur</h3>
                    <p class="card-subtitle">Modifiez les informations du conteneur</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('agent.conteneur.update', $conteneur->id) }}" method="POST" class="modern-form">
                        @csrf
                        @method('PUT')
                        
                        <!-- Première ligne : Nom conteneur et Type conteneur -->
                        <div class="row form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name_conteneur" class="form-label required">Nom du conteneur</label>
                                    <input type="text" class=" modern-input" id="name_conteneur" name="name_conteneur" value="{{ old('name_conteneur', $conteneur->name_conteneur) }}" required>
                                    @error('name_conteneur')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type_conteneur" class="form-label required">Type de conteneur</label>
                                    <select class=" modern-select" id="type_conteneur" name="type_conteneur" required>
                                        <option value="">Sélectionnez un type</option>
                                        <option value="Conteneur" {{ old('type_conteneur', $conteneur->type_conteneur) == 'Conteneur' ? 'selected' : '' }}>Conteneur</option>
                                        <option value="Ballon" {{ old('type_conteneur', $conteneur->type_conteneur) == 'Ballon' ? 'selected' : '' }}>Ballon</option>
                                    </select>
                                    @error('type_conteneur')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Deuxième ligne : Numéro conteneur et Statut -->
                        <div class="row form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero_conteneur" class="form-label">Numéro du conteneur</label>
                                    <input type="text" class=" modern-input" id="numero_conteneur" name="numero_conteneur" value="{{ old('numero_conteneur', $conteneur->numero_conteneur) }}">
                                    @error('numero_conteneur')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary modern-btn">
                                <i class="fas fa-save"></i>
                                Mettre à jour
                            </button>
                            <a href="{{ route('agent.conteneur.index') }}" class="btn btn-secondary modern-btn">
                                <i class="fas fa-arrow-left"></i>
                                Annuler
                            </a>
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
    --primary-dark: #e8910c;
    --white: #ffffff;
    --light-gray: #f8f9fa;
    --medium-gray: #e9ecef;
    --dark-gray: #6c757d;
    --text-color: #333333;
    --border-radius: 12px;
    --box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}

.modern-card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
    margin-top: 30px;
}

.modern-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: var(--white);
    border: none;
    padding: 30px;
    text-align: center;
}

.modern-header .header-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.modern-header .card-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.modern-header .card-subtitle {
    opacity: 0.9;
    font-size: 1rem;
}

.card-body {
    padding: 40px;
}

.form-row {
    margin-bottom: 0;
}

.modern-form .form-group {
    margin-bottom: 25px;
}

.form-label {
    font-weight: 600;
    color: var(--text-color);
    margin-bottom: 8px;
    display: block;
}

.form-label.required::after {
    content: " *";
    color: #e74c3c;
}

.modern-input, .modern-select {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid var(--medium-gray);
    border-radius: 8px;
    font-size: 16px;
    transition: var(--transition);
    background-color: var(--white);
}

.modern-input:focus, .modern-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(254, 162, 25, 0.2);
}

.modern-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236c757d' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 16px;
}

.radio-group {
    display: flex;
    gap: 20px;
    margin-top: 10px;
}

.radio-option {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.radio-option input {
    display: none;
}

.radio-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-weight: normal;
    margin-bottom: 0;
}

.radio-custom {
    width: 20px;
    height: 20px;
    border: 2px solid var(--medium-gray);
    border-radius: 50%;
    margin-right: 10px;
    position: relative;
    transition: var(--transition);
}

.radio-option input:checked + .radio-label .radio-custom {
    border-color: var(--primary-color);
    background-color: var(--primary-color);
}

.radio-option input:checked + .radio-label .radio-custom::after {
    content: '';
    width: 8px;
    height: 8px;
    background: var(--white);
    border-radius: 50%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    justify-content: flex-end;
    border-top: 1px solid var(--medium-gray);
    padding-top: 25px;
}

.modern-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-primary.modern-btn {
    background: var(--primary-color);
    color: var(--white);
}

.btn-primary.modern-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(254, 162, 25, 0.3);
}

.btn-secondary.modern-btn {
    background: var(--medium-gray);
    color: var(--dark-gray);
}

.btn-secondary.modern-btn:hover {
    background: var(--dark-gray);
    color: var(--white);
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .container-fluid {
        padding: 0 15px;
    }
    
    .card-body {
        padding: 25px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .radio-group {
        flex-direction: column;
        gap: 15px;
    }
    
    .form-row .col-md-6 {
        margin-bottom: 15px;
    }
}

@media (max-width: 576px) {
    .form-row .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
.error-message {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.error-message::before {
    content: "⚠";
    font-size: 0.8rem;
}
</style>
@endsection