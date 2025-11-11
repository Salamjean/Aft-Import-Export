<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nouvelle demande de devis</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .field { margin-bottom: 10px; }
        .field-label { font-weight: bold; }
        .colis-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .colis-table th, .colis-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .colis-table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nouvelle demande de devis</h1>
            <p>Une nouvelle demande de devis a été soumise sur la plateforme</p>
        </div>
        
        <div class="content">
            <h2>Informations client</h2>
            <div class="field">
                <span class="field-label">Client :</span>
                {{ $devis->prenom_client }} {{ $devis->name_client }}
            </div>
            <div class="field">
                <span class="field-label">Email :</span>
                {{ $devis->email_client }}
            </div>
            <div class="field">
                <span class="field-label">Téléphone :</span>
                {{ $devis->contact_client }}
            </div>
            <div class="field">
                <span class="field-label">Adresse :</span>
                {{ $devis->adresse_client }}
            </div>

            <h2>Informations expédition</h2>
            <div class="field">
                <span class="field-label">Mode de transit :</span>
                {{ $devis->mode_transit }}
            </div>
            <div class="field">
                <span class="field-label">Agence d'expédition :</span>
                {{ $devis->agence_expedition }}
            </div>
            <div class="field">
                <span class="field-label">Agence de destination :</span>
                {{ $devis->agence_destination }}
            </div>
            <div class="field">
                <span class="field-label">Pays d'expédition :</span>
                {{ $devis->pays_expedition }}
            </div>

            <h2>Colis</h2>
            <table class="colis-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th>Valeur</th>
                        <th>Type de colis</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($devis->colis as $colis)
                    <tr>
                        <td>{{ $colis['produit'] }}</td>
                        <td>{{ $colis['quantite'] }}</td>
                        <td>{{ number_format($colis['valeur'], 0) }} {{ $devis->devise }}</td>
                        <td>{{ $colis['type_colis'] ?? 'Non spécifié' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <p style="margin-top: 20px;">
                <strong>Date de soumission :</strong> {{ $devis->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>
</body>
</html>