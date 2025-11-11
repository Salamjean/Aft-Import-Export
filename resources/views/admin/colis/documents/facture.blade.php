<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture - {{ $colis->reference_colis }}</title>
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

        .invoice-title {
            text-align: right;
        }

        .invoice-title h1 {
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #fea219;
        }

        .invoice-meta {
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

        .items-table .col-qty, 
        .items-table .col-price, 
        .items-table .col-montant {
            text-align: right;
        }

        .items-table .col-produit { width: 50%; }
        .items-table .col-qty { width: 12%; }
        .items-table .col-price { width: 18%; }
        .items-table .col-montant { width: 20%; }

        .items-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .declaration-card {
            background: #f0f8f0;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #208938;
        }

        .declaration-card h4 {
            margin: 0 0 8px 0;
            font-size: 12px;
            font-weight: bold;
            color: #208938;
        }

        .declaration-card p {
            margin: 5px 0;
            font-size: 11px;
            line-height: 1.3;
        }

        .payment-card {
            background: #fffaf0;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #fea219;
        }

        .payment-card h4 {
            margin: 0 0 8px 0;
            font-size: 12px;
            font-weight: bold;
            color: #208938;
        }

        .payment-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px dashed #e1e1e1;
        }

        .payment-total {
            border-top: 2px solid #fea219;
            padding-top: 6px;
            margin-top: 6px;
            font-weight: bold;
            color: #208938;
        }

        .amount {
            font-weight: bold;
            color: #208938;
        }

        .totals-summary {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            border: 2px solid #208938;
            margin: 12px 0;
        }

        .totals-summary table {
            width: 50%;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals-summary td {
            padding: 6px 10px;
            font-size: 11px;
        }

        .totals-summary td:first-child {
            text-align: right;
            font-weight: bold;
            color: #208938;
        }

        .totals-summary td:last-child {
            text-align: right;
            font-weight: bold;
            background-color: #fffaf0;
            border: 1px solid #fea219;
        }

        .grand-total {
            background-color: #208938 !important;
            color: white !important;
            font-size: 12px !important;
        }

        .conditions {
            margin: 12px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 3px solid #fea219;
        }

        .conditions h4 {
            margin: 0 0 8px 0;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            color: #208938;
        }

        .conditions p {
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

        .client-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: 2px solid black;
            border-radius: 8px;
            overflow: hidden;
        }

        .client-header {
            color: black;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            border: 1px solid black;
        }

        .client-cell {
            padding: 5px;
            text-align: center;
            border: 1px solid #ddd;
            vertical-align: top;
            background: #f8f9fa;
            width: 50%;
        }

        .client-cell p {
            margin: 8px 0;
            font-size: 12px;
        }

        .client-cell strong {
            color: black;
            font-size: 13px;
        }

        .payment-table-column {
    width: 100%;
    border-collapse: collapse;
    border: 2px solid #fea219;
    border-radius: 8px;
    overflow: hidden;
}

.payment-table-column th,
.payment-table-column td {
    padding: 10px 12px;
    border-bottom: 1px solid #e1e1e1;
    font-size: 12px;
}

.payment-header-left {
    background: linear-gradient(135deg, #fea219 0%, #208938 100%);
    color: white;
    font-weight: bold;
    text-align: left;
    width: 60%;
}

.payment-header-right {
    background: linear-gradient(135deg, #fea219 0%, #208938 100%);
    color: white;
    font-weight: bold;
    text-align: right;
    width: 40%;
}

.payment-designation {
    background: #f8f9fa;
    text-align: left;
}

.payment-montant {
    background: white;
    text-align: right;
    font-weight: bold;
    color: #208938;
}

.payment-total-row {
    border-top: 2px solid #fea219;
}

.payment-total-designation {
    background: #fffaf0;
    font-weight: bold;
    text-align: left;
}

.payment-total-montant {
    background: #fffaf0;
    text-align: right;
    font-weight: bold;
    color: #208938;
    font-size: 13px;
}

.payment-rest-row {
    border-top: 2px solid #208938;
}

.payment-rest-designation {
    background: #f0f8f0;
    font-weight: bold;
    text-align: left;
}

.payment-rest-montant {
    background: #f0f8f0;
    text-align: right;
    font-weight: bold;
    color: #208938;
    font-size: 13px;
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

     <div class="invoice-title" style="display: grid; grid-template-columns: auto auto; align-items: center; justify-content: end; gap: 10px;">
    <div style="text-align: right;">
        <h1 style="margin: 0; font-size: 20px;">FACTURE</h1>
        <div class="invoice-meta" style="font-size: 12px; line-height: 16px;">
            <div><strong>N°: {{ $numeroFacture }}</strong></div>
            <div>Date: {{ $dateFacture }}</div>
            <div>Réf: {{ $colis->reference_colis }}</div>
        </div>
    </div>
    <img src="assets/img/barre.jpg" style="width: 120px; height: auto;" alt="">
</div>
        </div>


        <!-- Informations client -->
        <table class="client-table">
            <thead>
                <tr>
                    <th class="client-header">EXPÉDITEUR</th>
                    <th class="client-header">DESTINATAIRE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="client-cell">
                        <p><strong>{{ $colis->name_expediteur }} {{ $colis->prenom_expediteur ?? '' }}</strong></p>
                        <p>Téléphone: {{ $colis->contact_expediteur }}</p>
                        <p>Adresse: {{ $colis->adresse_expediteur }}</p>
                    </td>
                    <td class="client-cell">
                        <p><strong>{{ $colis->name_destinataire }} {{ $colis->prenom_destinataire }}</strong></p>
                        <p>Téléphone: {{ $colis->indicatif }} {{ $colis->contact_destinataire }}</p>
                        <p>Adresse: {{ $colis->adresse_destinataire }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-produit" style="color:black">Produit / Service</th>
                    <th class="col-qty" style="color:black">Qté</th>
                    <th class="col-price" style="color:black">P.U. ({{$devise}})</th>
                    <th class="col-montant" style="color:black">Montant ({{$devise}})</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalGeneral = 0;
                @endphp
                @if(is_array($colisDetails) && count($colisDetails) > 0)
                    @foreach($colisDetails as $detail)
                        @php
                            $produit = $detail['produit'] ?? 'AUTRE COLIS USAGE';
                            $quantite = $detail['quantite'] ?? 1;
                            $prixUnitaire = $detail['prix_unitaire'] ?? 20.00;
                            $montantLigne = $quantite * $prixUnitaire;
                            $totalGeneral += $montantLigne;
                        @endphp
                        <tr>
                            <td>{{ strtoupper($produit) }}</td>
                            <td class="col-qty">{{ $quantite }}</td>
                            <td class="col-price">{{ number_format($prixUnitaire, 0, ',', ' ') }}</td>
                            <td class="col-montant">{{ number_format($montantLigne, 0, ',', ' ') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="text-align: center;">Aucun détail de colis disponible</td>
                    </tr>
                @endif

                <!-- Dans le tableau items-table, après la boucle des colisDetails -->
                @if(isset($service) && $service && $colis->prix_service)
                <tr>
                    <td>{{ strtoupper($service->designation) }} - SERVICE SUPPLÉMENTAIRE</td>
                    <td class="col-qty">1</td>
                    <td class="col-price">{{ number_format($colis->prix_service, 0, ',', ' ') }}</td>
                    <td class="col-montant">{{ number_format($colis->prix_service, 0, ',', ' ') }}</td>
                </tr>
                @php
                    $totalGeneral += $colis->prix_service;
                @endphp
                @endif
                
                <!-- Ligne totale -->
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">Total Général</td>
                    <td class="col-montant" style="font-weight: bold;">{{ number_format($totalGeneral, 0, ',', ' ') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Déclaration et Paiement -->
        <div class="summary-grid">
                    <table class="payment-table-column">
                    <tr>
                        <td class="payment-designation">Montant colis</td>
                        <td class="payment-montant">{{ number_format($montantTotal - ($colis->prix_service ?? 0), 0, ',', ' ') }}</td>
                    </tr>
                    
                    @if(isset($service) && $service && $colis->prix_service)
                    <tr>
                        <td class="payment-designation">Service ({{ $service->designation }})</td>
                        <td class="payment-montant">{{ number_format($colis->prix_service, 0, ',', ' ') }}</td>
                    </tr>
                    @endif
                    
                    <tr class="payment-total-row">
                        <td class="payment-total-designation"><strong>TOTAL GÉNÉRAL</strong></td>
                        <td class="payment-total-montant"><strong>{{ number_format($montantTotal, 0, ',', ' ') }}</strong></td>
                    </tr>
                    
                    <tr>
                        <td class="payment-designation">Montant payé</td>
                        <td class="payment-montant">{{ number_format($montantPaye, 0, ',', ' ') }}</td>
                    </tr>
                    
                    <tr class="payment-rest-row">
                        <td class="payment-rest-designation"><strong>RESTE À PAYER</strong></td>
                        <td class="payment-rest-montant"><strong>{{ number_format($resteAPayer, 0, ',', ' ') }}</strong></td>
                    </tr>
                </table>

        <!-- Totaux -->
        {{-- <div class="totals-summary">
            <table>
                <tr><td>Montant total (EUR)</td><td class="grand-total">{{ number_format($montantTotal, 0, ',', ' ') }}</td></tr>
                <tr><td>Montant payé (EUR)</td><td>{{ number_format($montantPaye, 0, ',', ' ') }}</td></tr>
                <tr><td>Reste à payer (EUR)</td><td class="grand-total">{{ number_format($resteAPayer, 0, ',', ' ') }}</td></tr>
            </table>
        </div> --}}

        <!-- Conditions -->
        <div class="conditions">
            <h4>CONDITIONS DE VENTE</h4>
            <p>
                Les marchandises transportées doivent faire l'objet du règlement intégral des frais de transport, droits de douane et taxes avant livraison. Les coûts non soldés seront conservés dans nos entrepôts. Passé 5 jours, des frais de magasinage et une majoration de 10% seront appliqués. Au-delà de 30 jours, les colis non réclamés seront vendus pour couvrir les frais engagés.
            </p>
        </div>

        <!-- Signature -->
        {{-- <div class="signature-section">
            <div class="signature">
                <div class="signature-text">Le client</div>
                <div class="signature-line">Signature</div>
            </div>
            <div class="signature">
                <div class="signature-text">{{ $entreprise['nom'] }}</div>
                <div class="signature-line">Signature et cachet</div>
            </div>
        </div> --}}

        <!-- Footer -->
        <div class="footer-section">
            {{-- <div class="footer-generation">
                <div>Généré le {{ $dateFacture }}</div>
                <div>Page 1/1</div>
            </div> --}}
            <div class="barcode"></div>
            <div>
                <p><strong>AFT IMPORT EXPORT</strong> - 7 AVENUE LOUIS BLERIOT LA COURNEUVE 93120 France</p>
                <p>Tel. +3397860399 | contact.aaf@qmtl.com | SIRET 81916365 | TVA FR86681916365</p>
            </div>
        </div>
    </div>

    <!-- Bouton d'impression -->
    <div class="no-print" style="text-align: center; margin: 15px;">
        <button onclick="window.print();" style="padding: 8px 16px; font-size: 14px; background: linear-gradient(135deg, #fea219 0%, #208938 100%); color: white; border: none; border-radius: 4px; cursor: pointer;">
            🖨 Imprimer la facture
        </button>
    </div>
</body>
</html>