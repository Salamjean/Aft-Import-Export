<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nouveau message de contact</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #ff7b00; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #ff7b00; }
        .footer { background: #2c3e50; color: white; padding: 15px; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>AFT IMPORT EXPORT</h1>
            <h2>Nouveau message de contact</h2>
        </div>
        
        <div class="content">
            <div class="field">
                <span class="label">Nom complet:</span>
                <span>{{ $contact['name'] }}</span>
            </div>
            
            <div class="field">
                <span class="label">Email:</span>
                <span>{{ $contact['email'] }}</span>
            </div>
            
            <div class="field">
                <span class="label">Téléphone:</span>
                <span>{{ $contact['phone'] ?? 'Non renseigné' }}</span>
            </div>
            
            <div class="field">
                <span class="label">Sujet:</span>
                <span>{{ $contact['subject'] }}</span>
            </div>
            
            <div class="field">
                <span class="label">Message:</span>
                <div style="margin-top: 10px; padding: 15px; background: white; border-left: 4px solid #ff7b00;">
                    {{ $contact['message'] }}
                </div>
            </div>
            
            <div class="field">
                <span class="label">Date d'envoi:</span>
                <span>{{ now()->format('d/m/Y à H:i') }}</span>
            </div>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} AFT IMPORT EXPORT. Tous droits réservés.</p>
            <p>7 AVENUE LOUIS BLERIOT, 93120 LA COURNEUVE</p>
        </div>
    </div>
</body>
</html>