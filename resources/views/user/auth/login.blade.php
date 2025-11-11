<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{asset('assets/img/aft.jpg')}}" />
    <title>Connexion</title>
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
            max-width: 500px;
            width: 100%;
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
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border 0.3s ease;
        }
        
        .form-group input:focus {
            border-color: var(--orange);
            outline: none;
        }
        
        .form-group input.error {
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
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .remember-me input {
            width: auto;
        }
        
        .forgot-password {
            color: var(--orange);
            text-decoration: none;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Connexion</h1>
            <p>Accédez à votre compte</p>
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

            @if(session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <h2 class="form-title">Se connecter</h2>
                
                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="login-password">Mot de passe</label>
                    <input type="password" id="login-password" name="password" required>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Se souvenir de moi</label>
                    </div>
                    
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-password">
                        Mot de passe oublié ?
                    </a>
                    @endif
                </div>
                
                <button type="submit" class="btn">Se connecter</button>
                
                <a href="/" class="btn btn-secondary">Retour à l'accueil</a>
                
                <div class="form-footer">
                    <p>Pas encore de compte? <a href="{{ route('user.register') }}">Créer un compte</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validation basique côté client
        document.querySelector('form').addEventListener('submit', function(e) {
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires');
                return;
            }
            
            // Validation basique de l'email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Veuillez entrer une adresse email valide');
                return;
            }
        });

        // Gestion des erreurs de validation
        @if($errors->has('email') || $errors->has('password'))
            document.getElementById('login-email').classList.add('error');
            document.getElementById('login-password').classList.add('error');
        @endif
    </script>
</body>
</html>