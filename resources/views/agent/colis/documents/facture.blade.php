<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture - {{ $colis->reference_colis }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/mae-imo.png') }}">

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #000;
        }

        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }

        /* Header Section Table Layout */
        .header-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
            padding: 0;
        }

        .header-left {
            width: 40%;
            text-align: left;
        }

        .header-right {
            width: 60%;
            text-align: right;
        }

        .logo-img {
            max-width: 150px;
            height: auto;
            display: block;
        }

        .barcode-img {
            height: 45px;
            width: auto;
            display: block;
            margin-top: 10px;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            color: #000;
            display: block;
            margin-bottom: 5px;
        }

        .company-details-text {
            font-size: 11px;
            color: #444;
            line-height: 1.4;
        }

        .document-title {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            color: #666;
            margin-top: 25px;
            display: block;
        }
        
        .title-barcode {
            margin-top: 5px;
            height: 35px;
            width: auto;
            display: inline-block;
        }

        /* Info Grid Section */
        .info-grid {
            display: grid;
            grid-template-columns: 50% 48%; /* Slightly adjusted for spacing */
            gap: 2%;
            margin-bottom: 20px;
        }
        
        .client-name-large {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .meta-table tr {
            border: 1px solid #999;
        }

        .meta-table td {
            padding: 4px 8px;
            border: 1px solid #999;
        }

        .meta-label {
            text-align: right;
            width: 35%;
            background-color: white; /* Clean look from invoice image */
            color: #444;
        }

        .meta-value {
            text-align: center;
            font-weight: bold;
            background-color: #d3d3d3;
        }
        
        .meta-value.white-bg {
            background-color: white;
            font-weight: normal;
        }

        /* Reference Strip */
        .ref-strip-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        
        .ref-strip-table td {
            border: 1px solid #ccc;
            text-align: center;
            padding: 5px;
            width: 16.66%;
            color: #555;
        }

        /* Products Table */
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }

        .products-table th {
            background-color: #d3d3d3;
            color: #000;
            padding: 8px;
            text-align: left;
            border: none;
            font-weight: bold;
        }

        .products-table td {
            padding: 8px;
            border-right: 1px solid #eee; /* Light vertical lines */
            vertical-align: top;
        }
        
        .products-table td:last-child {
            border-right: none;
        }

        .products-table th.text-right,
        .products-table td.text-right {
            text-align: right;
        }
        
        .group-header {
            font-weight: bold;
            text-decoration: underline;
            padding-top: 10px;
            padding-bottom: 5px;
            display: block;
        }
        
        .sub-item {
            padding-left: 10px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .sub-desc {
            padding-left: 20px;
            color: #666;
            font-size: 10px;
        }
        
        .sub-total-row td {
            padding-top: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
        }

        /* Footer Grid */
        .footer-grid {
            display: grid;
            grid-template-columns: 60% 35%; /* Left 60% (Payment Terms), Right 35% (Totals) */
            gap: 5%;
            margin-bottom: 20px;
            align-items: start;
        }
        
        .payment-terms-bar {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .payment-terms-table {
            width: 100%;
            font-size: 11px;
            border-collapse: collapse;
        }
        
        .payment-terms-table th {
            text-align: center;
            font-weight: normal;
            color: #555;
            padding-bottom: 5px;
        }
        
        .payment-terms-table td {
            background-color: #d3d3d3;
            text-align: center;
            font-weight: bold;
            padding: 5px;
        }

        .notes-section {
            border: 1px solid #ccc;
            padding: 10px;
            font-size: 10px;
            margin-top: 10px;
        }
        
        .notes-label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        /* Totals Area */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        .totals-table td {
            padding: 5px;
            text-align: right;
        }
        
        .totals-label {
            font-weight: bold;
        }
        
        .totals-value-bg {
            background-color: #d3d3d3;
            font-weight: bold;
            width: 30%;
        }

        /* Footer Legal */
        .page-footer {
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 9px;
            display: flex;
            justify-content: space-between;
            color: #555;
        }
        
        .footer-center {
            text-align: center;
        }

        /* Utility */
        @media print {
            body { padding: 0; }
            .container { max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Header Table (Same as Bon de Livraison) -->
        <table class="header-table">
            <tr>
                <td class="header-left">
                    <img src="assets/img/aft.jpg" class="logo-img" alt="Logo AFT Import Export">
                </td>
                <td class="header-right">
                    <span class="company-name">AFT IMPORT EXPORT</span>
                    <div class="company-details-text">
                        7 AVENUE LOUIS BLERIOT LA COURNEUVE<br>
                        93120 France<br>
                        Tel. +33171894351
                    </div>
                </td>
            </tr>
            <tr>
                <td class="header-left">
                     <!-- No Barcode here on Facture image, actually title is centered. But user asked for consistency?
                          Actually image has "FACTURE" centered with barcode below it. 
                          Wait, let's look at the image again.
                          Image: Top Left Logo. Top Right Company.
                          Center: "FACTURE"
                          Center Below Facture: Barcode.
                          Okay, I will adapt to the Facture Image specifically.
                     -->
                </td>
                <td class="header-right">
                    <!-- Empty right space in second row, handled by centered title below -->
                </td>
            </tr>
        </table>
        
        <!-- Center Title Section -->
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="font-size: 28px; font-weight: bold; text-transform: uppercase; color: #666;">FACTURE</div>
            <img src="assets/img/barre_code.png" style="height: 40px; margin-top: 5px;" alt="Code Barre Facture">
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <div class="client-column">
                <!-- Client Name & Address -->
                <div class="client-name-large">
                    Nom & Prénoms: {{ $colis->name_destinataire }} {{ $colis->prenom_destinataire }}
                </div>
                <div style="font-size: 11px; line-height: 1.4;">
                    Adresse: {{ $colis->adresse_destinataire }}<br>
                    Email: {{ $colis->email_destinataire }}<br>
                    Contact: {{ $colis->contact_destinataire }}
                </div>
            </div>

            <div class="meta-column">
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Facture No.</td>
                        <td class="meta-value">{{ $numeroFacture }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Date:</td>
                        <td class="meta-value">{{ $dateFacture }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Numéro de client:</td>
                        <td class="meta-value">{{ $colis->contact_expediteur ?? 'C'.$colis->id }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Référence:</td>
                        <td class="meta-value white-bg">{{ $colis->reference_colis }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Code et Nom du vendeur:</td>
                        <td class="meta-value">{{ Auth::guard('agent')->user()->name. ' '. Auth::guard('agent')->user()->prenom ?? 'ADAMA SYLLA' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
       

        <!-- Products Table -->
        <table class="products-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Produit / Service</th>
                    <th class="text-right" style="width: 10%;">Qté</th>
                    <th class="text-right" style="width: 20%;">Prix Unit.</th>
                    <th class="text-right" style="width: 20%;">Montant (EUR)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Group 1: Items -->
                @php $totalAmount = 0; @endphp
                @foreach($colisDetails as $detail)
                @php 
                    $amount = ($detail['quantite'] ?? 1) * ($detail['prix_unitaire'] ?? 0); 
                    $totalAmount += $amount;
                @endphp
                <tr>
                    <td>
                        <span class="group-header">{{ strtoupper($detail['produit'] ?? 'COLIS') }}</span>
                        <div class="sub-item">{{ $detail['produit'] ?? 'Marchandise' }} - {{ $colis->mode_transit }}</div>
                        <div class="sub-desc">{{ $detail['description'] ?? '' }}</div>
                    </td>
                    <td class="text-right" style="vertical-align: top; padding-top: 10px;">{{ $detail['quantite'] ?? 1 }}</td>
                    <td class="text-right" style="vertical-align: top; padding-top: 10px;">{{ number_format($detail['prix_unitaire'] ?? 0, 0, ',', ' ') }}</td>
                    <td class="text-right" style="vertical-align: top; padding-top: 10px;">{{ number_format($amount, 0, ',', ' ') }}</td>
                </tr>
                @endforeach
                
                <!-- Service Extra -->
                @if(isset($colis->prix_service) && $colis->prix_service > 0)
                @php $totalAmount += $colis->prix_service; @endphp
                <tr>
                    <td>
                        <span class="group-header">SERVICE SUPPLÉMENTAIRE</span>
                        <div class="sub-item">{{ $service->designation ?? 'Service' }}</div>
                    </td>
                    <td class="text-right" style="vertical-align: top; padding-top: 10px;">1,00</td>
                    <td class="text-right" style="vertical-align: top; padding-top: 10px;">{{ number_format($colis->prix_service, 0, ',', ' ') }}</td>
                    <td class="text-right" style="vertical-align: top; padding-top: 10px;">{{ number_format($colis->prix_service, 0, ',', ' ') }}</td>
                </tr>
                @endif
                
                <!-- Sous total spacer row -->
                <tr class="sub-total-row">
                    <td></td>
                    <td colspan="2" class="text-right" style="font-size: 10px;">Sous total</td>
                    <td class="text-right" style="font-size: 10px;">{{ number_format($totalAmount, 0, ',', ' ') }}</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Footer Grid -->
        <div class="footer-grid">
            <div class="left-footer">
                <div class="payment-terms-bar">
                   <img src="assets/img/barre_code.png" style="height: 35px; margin-bottom: 10px;" alt="">

                   <!-- Historique des paiements (Requested) -->
                   @php
                       $totalPaiements = $colis->paiements ? $colis->paiements->sum('montant') : 0;
                       $montantPayeTotal = $colis->montant_paye ?? 0;
                       // Calculate initial payment (stored on colis table) as difference
                       $paiementInitial = $montantPayeTotal - $totalPaiements;
                       
                       $hasInvestments = $colis->paiements && count($colis->paiements) > 0;
                       $hasInitial = $paiementInitial > 0;
                   @endphp

                   @if($hasInvestments || $hasInitial)
                   <div style="margin-top: 20px;">
                       <strong style="display:block; margin-bottom:5px; font-size:12px; color: black;">Historique des paiements</strong>
                       <table style="width: 100%; border-collapse: collapse; font-size: 11px; border: 1px solid #ddd;">
                           <thead style="background-color: #f5f5f5;">
                               <tr>
                                   <th style="border: 1px solid #ddd; padding: 5px; text-align: center;">Date</th>
                                   <th style="border: 1px solid #ddd; padding: 5px; text-align: center;">Encaissement No.</th>
                                   <th style="border: 1px solid #ddd; padding: 5px; text-align: center;">Montant payé ({{ $colis->devise ?? 'F.CFA' }})</th>
                                   <th style="border: 1px solid #ddd; padding: 5px; text-align: center;">Mode de paiement</th>
                                   <th style="border: 1px solid #ddd; padding: 5px; text-align: center;">Référence</th>
                               </tr>
                           </thead>
                           <tbody>
                               <!-- Row for Initial/Colis Payment -->
                               @if($hasInitial)
                               <tr>
                                   <td style="border: 1px solid #ddd; padding: 5px; text-align: center;">{{ $colis->created_at ? $colis->created_at->format('d-m-Y') : '-' }}</td>
                                   <td style="border: 1px solid #ddd; padding: 5px; text-align: center;">EN-{{ str_pad($colis->id, 5, '0', STR_PAD_LEFT) }}</td>
                                   <td style="border: 1px solid #ddd; padding: 5px; text-align: center;">{{ number_format($paiementInitial, 0, ',', ' ') }}</td>
                                   <td style="border: 1px solid #ddd; padding: 5px; text-align: center;">{{ $colis->methode_paiement ?? 'Espèce' }}</td>
                                   <td style="border: 1px solid #ddd; padding: 5px; text-align: center;">Paiement Initial</td>
                               </tr>
                               @endif

                               <!-- Rows for Subsequent Payments -->
                               @if($hasInvestments)
                                   @foreach($colis->paiements as $paiement)
                                   <tr>
                                       <td style="border: 1px solid #ddd; padding: 5px; text-align: center;">{{ $paiement->created_at ? $paiement->created_at->format('d-m-Y') : '-' }}</td>
                                       <td style="border: 1px solid #ddd; padding: 5px; text-align: center;">EN-{{ str_pad($paiement->id, 5, '0', STR_PAD_LEFT) }}</td>
                                       <td style="border: 1px solid #ddd; padding: 5px; text-align: center;">{{ number_format($paiement->montant, 0, ',', ' ') }}</td>
                                       <td style="border: 1px solid #ddd; padding: 5px; text-align: center;">{{ $paiement->methode_paiement }}</td>
                                       <td style="border: 1px solid #ddd; padding: 5px; text-align: center;">{{ $paiement->notes ?? '' }}</td>
                                   </tr>
                                   @endforeach
                               @endif
                           </tbody>
                       </table>
                   </div>
                   @endif
                </div>
                
                <div class="notes-section">
                    <span class="notes-label">Notes</span>
                    EBURNY SOLUTIONS et autre fournisseur. <br>
                    Les marchandises transportées doivent être soldées avant livraison. Pénalités de 10% après 5 jours de retard.
                </div>
            </div>
            
            <div class="right-footer">
                <table class="totals-table">
                    <tr>
                        <td class="totals-label">Montant HT (EUR)</td>
                        <td class="totals-value-bg">{{ number_format($montantTotal, 0, ',', ' ') }}</td>
                    </tr>
                    <tr><td colspan="2" style="height: 10px;"></td></tr> <!-- Spacer -->
                    <tr>
                        <td class="totals-label">Total TTC (EUR)</td>
                        <td class="totals-value-bg">{{ number_format($montantTotal, 0, ',', ' ') }}</td>
                    </tr>
                    <tr>
                        <td class="totals-label">Total Payé (EUR)</td>
                        <td style="font-weight: bold; text-align: right; padding-right: 15px;">{{ number_format($montantPaye, 2, ',', ' ') }}</td>
                    </tr>
                    <tr>
                        <td class="totals-label">Reste à payer (EUR)</td>
                        <td class="totals-value-bg">{{ number_format($resteAPayer, 0, ',', ' ') }}</td>
                    </tr>
                </table>
                <div style="text-align: right; margin-top: 10px;">
                    <img src="assets/img/barre_code.png" style="height: 30px;" alt="">
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="page-footer">
            <div class="footer-center">
                <strong>7 AVENUE LOUIS BLERIOT LA COURNEUVE 93120 France | Tel. +3397860399 | contacts.aft@gmail.com |</strong><br>
                N°TVA:FR96881916365 N°ORI FR88191636500011 SIRET:881916365 RCS Bobigny, EXO TVA, article 262 DU CGI
            </div>
        </div>
    </div>

</body>
</html>