<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Votre code de réinitialisation</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #f79027 0%, #0e914b 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: white;
        }
        .content {
            padding: 30px;
            text-align: center;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #0e914b;
            font-weight: bold;
        }
        .message {
            margin-bottom: 25px;
            font-size: 16px;
            line-height: 1.6;
        }
        .code-container {
            background-color: #f8f9fa;
            border: 2px dashed #f79027;
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
        }
        .code {
            font-size: 48px;
            font-weight: bold;
            color: #0e914b;
            letter-spacing: 15px;
            margin-left: 15px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #e3e6f0;
        }
        .contact-info {
            margin-top: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Code de Réinitialisation</h1>
            <p>AFT - IMPORT - EXPORT</p>
        </div>

        <div class="content">
            <div class="greeting">
                Bonjour {{ $user->name }},
            </div>

            <div class="message">
                Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte chez <strong>AFT - IMPORT - EXPORT</strong>.
            </div>

            <p>Voici votre code de validation :</p>
            
            <div class="code-container">
                <span class="code">{{ $code }}</span>
            </div>

            <div class="message">
                Ce code expirera dans 60 minutes. Saisissez-le sur la page de réinitialisation pour continuer.
            </div>

            <div class="message" style="font-size: 14px; color: #666;">
                Si vous n'avez pas demandé de réinitialisation de mot de passe, aucune autre action n'est requise de votre part.
            </div>
        </div>

        <div class="footer">
            <div>
                <strong>AFT - IMPORT - EXPORT</strong><br>
                Votre partenaire logistique de confiance
            </div>
            
            <div class="contact-info">
                📧 Email : contact@aft-import-export.com<br>
                🌐 Site : https://aft-import-export.com
            </div>
            
            <div style="margin-top: 15px; font-size: 12px; color: #858796;">
                Cet e-mail a été envoyé automatiquement. Merci de ne pas y répondre.<br>
                © {{ date('Y') }} AFT - IMPORT - EXPORT. Tous droits réservés.
            </div>
        </div>
    </div>
</body>
</html>