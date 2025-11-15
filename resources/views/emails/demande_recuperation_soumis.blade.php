<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nouvelle Demande {{ $demande->type_recuperation == 'depot' ? 'Dépôt' : 'Récuperation' }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #fea219, #e8910c); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 10px 10px; }
        .info-box { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #fea219; }
        .footer { text-align: center; margin-top: 20px; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 Nouvelle Demande de {{ $demande->type_recuperation == 'depot' ? 'Dépôt' : 'Récuperation' }}</h1>
            <p>AFT-IMPORT-EXPORT</p>
        </div>
        
        <div class="content">
            <p>Bonjour,</p>
            <p>Une nouvelle demande de récupération a été soumise via notre plateforme.</p>
            
            <div class="info-box">
                <h3>📋 Informations de la Demande</h3>
                <p><strong>Référence :</strong> {{ $demande->reference }}</p>
                <p><strong>Date de soumission :</strong> {{ $demande->created_at->format('d/m/Y à H:i') }}</p>
            </div>

            <div class="info-box">
                <h3>🏢 Agence de Destination</h3>
                <p><strong>Agence :</strong> {{ $demande->agence->name }}</p>
                <p><strong>Pays :</strong> {{ $demande->agence->pays }}</p>
                <p><strong>Adresse :</strong> {{ $demande->agence->adresse }}</p>
            </div>

            <div class="info-box">
                <h3>📦 Détails de l'Objet</h3>
                <p><strong>Type de demande :</strong> {{ $demande->type_recuperation }}</p>
                <p><strong>Nature de l'objet :</strong> {{ $demande->nature_objet }}</p>
                <p><strong>Quantité :</strong> {{ $demande->quantite }}</p>
            </div>

            <div class="info-box">
                <h3>👤 Informations du Client</h3>
                <p><strong>Nom :</strong> {{ $demande->nom_concerne }} {{ $demande->prenom_concerne }}</p>
                <p><strong>Contact :</strong> {{ $demande->contact }}</p>
                <p><strong>Email :</strong> {{ $demande->email ?? 'Non renseigné' }}</p>
                <p><strong>Adresse de récupération :</strong> {{ $demande->adresse_recuperation }}</p>
                <p><strong>Date souhaitée :</strong> {{ $demande->date_recuperation ? $demande->date_recuperation : 'Dès que possible' }}</p>
            </div>
            <p><strong>Action requise :</strong> Veuillez traiter cette demande dans les plus brefs délais.</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} AFT-IMPORT-EXPORT. Tous droits réservés.</p>
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>