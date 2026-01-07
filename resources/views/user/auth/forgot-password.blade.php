<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{asset('assets/img/aft.jpg')}}" />
    <title>Mot de passe oublié</title>
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
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Mot de passe oublié</h1>
            <p>Réinitialisez votre accès en quelques instants</p>
        </div>

        <div class="form-container">
            <div class="logo">
                <img src="{{ asset('images/LOGOAFT.png') }}" style="width: 150px" alt="">
            </div>

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

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <h2 class="form-title">Demander un lien</h2>
                <p style="text-align: center; margin-bottom: 20px; color: #666; font-size: 14px;">
                    Entrez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.
                </p>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="votre@email.com">
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn">Envoyer le lien</button>

                <a href="{{ route('login') }}" class="btn btn-secondary">Retour à la connexion</a>
            </form>
        </div>
    </div>
</body>

</html>