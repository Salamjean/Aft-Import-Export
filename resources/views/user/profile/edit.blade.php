@extends('user.layouts.template')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header text-white" style="background-color: #fea219;">
                        <h4 class="mb-0">Mon profil</h4>
                    </div>

                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0 pl-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('user.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nom</label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        value="{{ old('name', $user->name) }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="prenom" class="form-label">Prenom</label>
                                    <input type="text" id="prenom" name="prenom" class="form-control"
                                        value="{{ old('prenom', $user->prenom) }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="{{ old('email', $user->email) }}" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="contact" class="form-label">Contact</label>
                                    <input type="text" id="contact" name="contact" class="form-control"
                                        value="{{ old('contact', $user->contact) }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="pays" class="form-label">Pays</label>
                                    <input type="text" id="pays" name="pays" class="form-control"
                                        value="{{ old('pays', $user->pays) }}">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="adresse" class="form-label">Adresse</label>
                                <input type="text" id="adresse" name="adresse" class="form-control"
                                    value="{{ old('adresse', $user->adresse) }}">
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn text-white"
                                    style="background-color: #0e914b; border-color: #0e914b;">
                                    Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
