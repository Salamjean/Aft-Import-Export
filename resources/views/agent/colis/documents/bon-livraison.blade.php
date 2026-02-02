<!DOCTYPE html>
<html >
<head>
    <meta charset="utf-8">
    <title>Bon de Livraison - {{ $colis->reference_colis }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/mae-imo.png') }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital@1&display=swap');
        
        body {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            line-height: 1.4;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 10px;
            background-color: #f9f9f9;
            font-size: 12px;
        }
        
        .receipt-container {
            background-color: white;
            border: 1px solid #e1e1e1;
            border-radius: 3px;
            padding: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            position: relative;
            page-break-inside: avoid;
        }

        .receipt-container::before {
            content: "";
            position: absolute;
            top: 25%;
            left: 10%;
            width: 80%;
            height: 50%;
            background-image: url(assets/img/aft.jpg);
            background-size: cover;
            background-position: center;
            opacity: 0.1;
            z-index: -1;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #208938;
        }

        .company-info h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
            font-weight: bold;
            color: #208938;
        }

        .company-info p {
            margin: 2px 0;
            font-size: 11px;
            color: #666;
        }

        .bon-title {
            text-align: right;
        }

        .bon-title h1 {
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #fea219;
        }

        .bon-meta {
            font-size: 11px;
            color: #333;
        }

        .client-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .client-card {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            border-left: 3px solid #208938;
        }

        .client-card h3 {
            margin: 0 0 8px 0;
            font-size: 13px;
            font-weight: bold;
            color: #208938;
        }

        .client-card p {
            margin: 3px 0;
            font-size: 11px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-card {
            background: #fffaf0;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #fea219;
        }

        .info-card h4 {
            margin: 0 0 8px 0;
            font-size: 12px;
            font-weight: bold;
            color: #208938;
            border-bottom: 1px solid #fea219;
            padding-bottom: 3px;
        }

        .info-item {
            margin-bottom: 4px;
            display: flex;
            justify-content: space-between;
        }

        .info-label {
            font-weight: bold;
            color: #208938;
            font-size: 11px;
        }

        .info-value {
            font-size: 11px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 11px;
        }

        .items-table th {
            background: linear-gradient(135deg, #208938 0%, #2d9f42 100%);
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #208938;
        }

        .items-table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .instructions {
            margin: 12px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 3px solid #fea219;
        }

        .instructions h4 {
            margin: 0 0 8px 0;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            color: #208938;
        }

        .instructions p {
            font-size: 10px;
            line-height: 1.3;
            color: #555;
            text-align: justify;
            margin: 0;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e1e1e1;
        }

        .signature {
            text-align: center;
        }

        .signature-text {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 150px;
            padding-top: 3px;
            font-size: 10px;
        }

        .footer-section {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #e1e1e1;
            font-size: 9px;
            color: #666;
            text-align: center;
        }

        .footer-generation {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 9px;
        }

        .barcode {
            height: 20px;
            background: linear-gradient(to right,
                #208938 0%, #208938 2px, transparent 2px, transparent 4px,
                #208938 4px, #208938 5px, transparent 5px, transparent 7px
            );
            background-repeat: repeat-x;
            background-size: 7px 100%;
            margin: 5px 0;
        }

        .no-print { display: none; }

        @media print {
            body {
                margin: 0;
                padding: 0;
                font-size: 11px;
            }
            
            .receipt-container {
                box-shadow: none;
                border: 1px solid #ccc;
                padding: 12px;
                margin: 0;
            }
            
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- En-tête -->
        <div class="header-section" style="display:flex; justify-content:space-between; align-items:center;">
            <div class="company-info">
                <img src="assets/img/aft.jpg" style="width: 150px" alt="">
                <p>{{ $entreprise['adresse'] }}</p>
                <p>Tél: {{ $entreprise['telephone'] }} | Email: {{ $entreprise['email'] }}</p>
                <p>SIRET: {{ $entreprise['siret'] }} | TVA: {{ $entreprise['tva'] }}</p>
            </div>

            <div class="bon-title" style="display: grid; grid-template-columns: auto auto; align-items: center; justify-content: end; gap: 10px;">
                <div style="text-align: right;">
                    <h1 style="margin: 0; font-size: 20px;">BON DE LIVRAISON</h1>
                    <div class="bon-meta" style="font-size: 12px; line-height: 16px;">
                        <div><strong>N°: {{ $numeroBonLivraison }}</strong></div>
                        <div>Date: {{ $dateLivraison }}</div>
                        <div>Réf: {{ $colis->reference_colis }}</div>
                        <div>Transport: {{ $colis->mode_transit }}</div>
                    </div>
                </div>
                <img src="assets/img/barre.jpg" style="width: 120px; height: auto;" alt="">
            </div>
        </div>

        <!-- Informations client -->
        <div class="client-details-grid">
            <div class="client-card">
                <h3>EXPÉDITEUR</h3>
                <p><strong>{{ $colis->name_expediteur }} {{ $colis->prenom_expediteur ?? '' }}</strong></p>
                <p>Téléphone: {{ $colis->contact_expediteur }}</p>
                <p>Email: {{ $colis->email_expediteur }}</p>
                <p>Adresse: {{ $colis->adresse_expediteur }}</p>
            </div>
            <div class="client-card">
                <h3>DESTINATAIRE</h3>
                <p><strong>{{ $colis->name_destinataire }} {{ $colis->prenom_destinataire }}</strong></p>
                <p>Téléphone: {{ $colis->indicatif }} {{ $colis->contact_destinataire }}</p>
                <p>Email: {{ $colis->email_destinataire }}</p>
                <p>Adresse: {{ $colis->adresse_destinataire }}</p>
            </div>
        </div>

        <!-- Détails des colis -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="color:black">Produit / Description</th>
                    <th style="color:black">Quantité</th>
                    <th style="color:black">Poids (kg)</th>
                    <th style="color:black">Dimensions (cm)</th>
                    <th style="color:black">Observations</th>
                </tr>
            </thead>
            <tbody>
                @foreach($colisDetails as $detail)
                    <tr>
                        <td>{{ $detail['produit'] ?? 'COLIS DIVERS' }}</td>
                        <td>{{ $detail['quantite'] ?? 1 }}</td>
                        <td>{{ $detail['poids'] ?? '--' }}</td>
                        <td>
                            @if(isset($detail['longueur']) && isset($detail['largeur']) && isset($detail['hauteur']))
                                {{ $detail['longueur'] }}x{{ $detail['largeur'] }}x{{ $detail['hauteur'] }}
                            @else
                                --
                            @endif
                        </td>
                        <td>{{ $detail['description'] ?? '--' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="barcode"></div>

        <!-- Instructions de livraison -->
        <div class="instructions">
            <h4>INSTRUCTIONS DE LIVRAISON</h4>
            <p>
                Le présent bon de livraison doit être signé par le destinataire lors de la réception des marchandises. 
                Toute anomalie ou dommage doit être immédiatement signalé au livreur et mentionné sur ce document 
                avant signature. La signature vaut acceptation de la livraison dans l'état constaté.
            </p>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature mb-4">
                <div class="signature-text" >Signature du Livreur : </div>
                <div style="font-size: 9px;">Date: ________________</div>
            </div>
            <div class="signature" style="margin-top: 20px">
                <div class="signature-text">Signature du Destinataire : </div>
                <div style="font-size: 9px;">Date: ________________</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-section">
            <div>
                <p><strong>AFT IMPORT EXPORT</strong> - 7 AVENUE LOUIS BLERIOT LA COURNEUVE 93120 France</p>
                <p>Tel. +3397860399 | contact.aaf@qmtl.com | SIRET 81916365 | TVA FR86681916365</p>
            </div>
        </div>
    </div>

    <!-- Bouton d'impression -->
    <div class="no-print" style="text-align: center; margin: 15px;">
        <button onclick="window.print();" style="padding: 8px 16px; font-size: 14px; background: linear-gradient(135deg, #fea219 0%, #208938 100%); color: white; border: none; border-radius: 4px; cursor: pointer;">
            🖨 Imprimer le bon de livraison
        </button>
    </div>
</body>
</html>