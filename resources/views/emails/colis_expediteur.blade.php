<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $emailData['colis']->reference_colis }} - Colis créé</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #fea219; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 20px; background: white; border: 1px solid #ddd; border-top: none; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 10px 10px; }
        .info-box { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .qr-code { text-align: center; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Votre colis a été créé !</h1>
            <p>AFT IMPORT EXPORT</p>
        </div>
        
        <div class="content">
            <p>Bonjour <strong>{{ $expediteur->name }}</strong>,</p>
            
            <p>Votre colis a été créé avec succès et est maintenant pris en charge par nos services.</p>
            
            <div class="info-box">
                <h3>📋 Détails du colis</h3>
                <p><strong>Référence :</strong> {{ $colis->reference_colis }}</p>
                <p><strong>Code colis :</strong> {{ $emailData['code_colis_principal'] }}</p>
                <p><strong>Destination :</strong> {{ $colis->agence_destination }}</p>
                <p><strong>Mode de transit :</strong> {{ $colis->mode_transit }}</p>
                <p><strong>Date de création :</strong> {{ $colis->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <div class="info-box">
                <h3>👤 Destinataire</h3>
                <p><strong>Nom :</strong> {{ $colis->name_destinataire }} {{ $colis->prenom_destinataire }}</p>
                <p><strong>Adresse :</strong> {{ $colis->adresse_destinataire }}</p>
                <p><strong>Contact :</strong> {{ $colis->contact_destinataire }}</p>
            </div>

            <p>Vous pouvez suivre à chaque étape importante du trajet de votre colis.</p>
            
            <p>Cordialement,<br>L'équipe AFT IMPORT EXPORT</p>
        </div>
        
        <div class="footer">
            <p>AFT IMPORT EXPORT<br>
            Email: contact@aft-import-export.com<br>
            Téléphone: +33 1 71 89 45 51</p>
        </div>
    </div>
</body>
</html>