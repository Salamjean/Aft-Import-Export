<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{asset('assets/img/aft.jpg')}}" />
    <title>Nouveau mot de passe</title>
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

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Réinitialisation</h1>
            <p>Choisissez votre nouveau mot de passe</p>
        </div>

        <div class="form-container">
            <div class="logo">
                <img src="{{ asset('images/LOGOAFT.png') }}" style="width: 150px" alt="">
            </div>

            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <h2 class="form-title">Réinitialisation</h2>

                <div class="form-group">
                    <label for="code">Code de validation (4 chiffres)</label>
                    <input type="text" id="code" name="code" required maxlength="4" placeholder="Ex: 1234" 
                        style="text-align: center; letter-spacing: 5px; font-size: 20px; font-weight: bold;">
                    @error('code')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" required placeholder="Au moins 8 caractères">
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        placeholder="Répétez le mot de passe">
                </div>

                <button type="submit" class="btn">Réinitialiser le mot de passe</button>
            </form>
        </div>
    </div>
</body>

</html>