<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{asset('assets/img/aft.jpg')}}" />
    <title>Inscription</title>
    <style>
        :root {
            --orange: #f79027;
            --vert: #0e914b;
            --blanc: #ffffff;
            --gris-clair: #f5f5f5;
            --gris-fonce: #333333;
            --rouge: #e74c3c;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--gris-clair);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            width: 50%;
            background-color: var(--blanc);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, var(--orange), var(--vert));
            padding: 30px;
            text-align: center;
            color: var(--blanc);
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .form-container {
            padding: 40px;
        }
        
        .form-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: var(--gris-fonce);
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--gris-fonce);
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border 0.3s ease;
        }
        
        .form-group input:focus, .form-group select:focus {
            border-color: var(--orange);
            outline: none;
        }
        
        .form-group input.error, .form-group select.error {
            border-color: var(--rouge);
        }
        
        .error-message {
            color: var(--rouge);
            font-size: 14px;
            margin-top: 5px;
        }
        
        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: var(--orange);
            color: var(--blanc);
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-align: center;
            text-decoration: none;
        }
        
        .btn:hover {
            background-color: var(--vert);
        }
        
        .btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        
        .btn-secondary {
            background-color: transparent;
            color: var(--gris-fonce);
            border: 1px solid #ddd;
            margin-top: 15px;
        }
        
        .btn-secondary:hover {
            background-color: #f5f5f5;
            color: var(--vert);
        }
        
        .form-footer {
            margin-top: 20px;
            text-align: center;
            color: var(--gris-fonce);
        }
        
        .form-footer a {
            color: var(--orange);
            text-decoration: none;
            font-weight: 500;
        }
        
        .form-footer a:hover {
            text-decoration: underline;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: var(--vert);
            font-size: 28px;
        }
        
        .logo span {
            color: var(--orange);
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .password-requirements {
            font-size: 14px;
            color: var(--gris-fonce);
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .container {
                width: 95%;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Inscription</h1>
            <p>Créez votre compte</p>
        </div>
        
        <div class="form-container">
            <div class="logo">
                <img src="{{ asset('images/LOGOAFT.png') }}" style="width: 150px" alt="">
            </div>
            
            <!-- Messages de succès/erreur -->
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            
            <form method="POST" action="{{ route('user.handleRegister') }}">
                @csrf

                <h2 class="form-title">Créer un compte</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="register-name">Nom</label>
                        <input type="text" id="register-name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="register-prenom">Prénom</label>
                        <input type="text" id="register-prenom" name="prenom" value="{{ old('prenom') }}" required>
                        @error('prenom')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="register-email">Email</label>
                        <input type="email" id="register-email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="register-adresse">Adresse</label>
                        <input type="text" id="register-adresse" name="adresse" value="{{ old('adresse') }}" required>
                        @error('adresse')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="register-password">Mot de passe</label>
                        <input type="password" id="register-password" name="password" required>
                        <div class="password-requirements">
                            Le mot de passe doit contenir au moins 8 caractères
                        </div>
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="register-password_confirmation">Confirmer le mot de passe</label>
                        <input type="password" id="register-password_confirmation" name="password_confirmation" required>
                        <div class="error-message" id="password-error">
                            Les mots de passe ne correspondent pas
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="register-pays">Pays</label>
                        <select id="register-pays" name="pays" required>
                            <option value="">Sélectionnez votre pays</option>
                            <option value="+225" {{ old('pays') == '+225' ? 'selected' : '' }}>Côte d'Ivoire (+225) </option>
                            <option value="+33" {{ old('pays') == '+33' ? 'selected' : '' }}>France (+33) </option>
                            <option value="+86" {{ old('pays') == '+86' ? 'selected' : '' }}>Chine (+86) </option>
                            <option value="+1" {{ old('pays') == '+1' ? 'selected' : '' }}>USA (+1)</option>
                        </select>
                        @error('pays')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="register-contact">Contact</label>
                        <input type="text" id="register-contact" name="contact" value="{{ old('contact') }}" required>
                        @error('contact')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <button type="submit" class="btn" id="submit-btn">S'inscrire</button>
                
                <a href="/" class="btn btn-secondary">Retour à l'accueil</a>
                
                <div class="form-footer">
                    <p>Déjà un compte? <a href="{{ route('login') }}">Se connecter</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validation côté client pour une meilleure expérience utilisateur
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('register-password_confirmation').value;
            const passwordError = document.getElementById('password-error');
            
            // Vérification de la correspondance des mots de passe
            if (password !== confirmPassword) {
                e.preventDefault();
                document.getElementById('register-password_confirmation').classList.add('error');
                passwordError.style.display = 'block';
                return;
            }
            
            // Vérification de la longueur du mot de passe
            if (password.length < 8) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au moins 8 caractères');
                return;
            }
        });
        
        // Validation en temps réel des mots de passe
        document.getElementById('register-password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('register-password').value;
            const confirmPassword = this.value;
            const passwordError = document.getElementById('password-error');
            
            if (confirmPassword && password !== confirmPassword) {
                this.classList.add('error');
                passwordError.style.display = 'block';
            } else {
                this.classList.remove('error');
                passwordError.style.display = 'none';
            }
        });
    </script>
</body>
</html>