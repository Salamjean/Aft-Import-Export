<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Aperçu des Étiquettes - {{ $colis->reference_colis }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #fea219;
        }
        .etiquettes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .etiquette-card {
            border: 2px solid #333;
            padding: 15px;
            background: white;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .etiquette-header {
            background-color: #000;
            color: #fff;
            text-align: center;
            padding: 8px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .etiquette-info {
            margin-bottom: 10px;
        }
        .etiquette-info strong {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        .qr-code {
            text-align: center;
            margin: 10px 0;
        }
        .qr-code img {
            max-width: 100px;
            max-height: 100px;
        }
        .actions {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 10px;
            background-color: #fea219;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #e8910c;
        }
        .statistics {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Aperçu des Étiquettes</h1>
            <p>Référence: <strong>{{ $colis->reference_colis }}</strong></p>
            <p>Quantité totale: <strong>{{ $quantiteTotale }} étiquette(s)</strong></p>
        </div>

        <div class="statistics">
            <h3>Résumé</h3>
            <p><strong>Expéditeur:</strong> {{ $colis->name_expediteur }} {{ $colis->prenom_expediteur }}</p>
            <p><strong>Destinataire:</strong> {{ $colis->name_destinataire }} {{ $colis->prenom_destinataire }}</p>
            <p><strong>Agence de destination:</strong> {{ $colis->agence_destination }}</p>
        </div>

        @if($colis_collection->isNotEmpty())
            <div class="etiquettes-grid">
                @foreach($colis_collection as $colisItem)
                <div class="etiquette-card">
                    <div class="etiquette-header">
                        AFT IMPORT EXPORT
                    </div>
                    
                    <div class="etiquette-info">
                        <strong>Référence:</strong>
                        {{ $colisItem->reference_colis }}
                    </div>
                    
                    <div class="etiquette-info">
                        <strong>Destinataire:</strong>
                        {{ $colisItem->name_destinataire }} {{ $colisItem->prenom_destinataire }}
                    </div>
                    
                    <div class="etiquette-info">
                        <strong>Contact:</strong>
                        {{ $colisItem->contact_destinataire }}
                    </div>
                    
                    <div class="etiquette-info">
                        <strong>Agence:</strong>
                        {{ $colisItem->agence_destination }}
                    </div>
                    
                    <div class="qr-code">
                        @if(!empty($colisItem->qr_code_path))
                            <img src="{{ asset('storage/' . $colisItem->qr_code_path) }}" alt="QR Code">
                        @else
                            <div style="color: #666; font-size: 12px;">
                                QR Code non disponible
                            </div>
                        @endif
                    </div>
                    
                    <div class="etiquette-info" style="text-align: center;">
                        <strong>Étiquette {{ $colisItem->numero_etiquette }}/{{ $colisItem->quantite_totale }}</strong>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 40px;">
                <p style="color: #666; font-size: 18px;">Aucune étiquette à afficher</p>
            </div>
        @endif

        <div class="actions">
            <a href="/admin/parcel/{{ $colis->id }}/etiquettes?action=download" class="btn">
                Télécharger PDF
            </a>
            <a href="/admin/parcel/{{ $colis->id }}/etiquettes?action=print" class="btn">
                Imprimer
            </a>
            <a href="{{ route('colis.index') }}" class="btn" style="background-color: #6c757d;">
                Retour à la liste
            </a>
        </div>
    </div>
</body>
</html>