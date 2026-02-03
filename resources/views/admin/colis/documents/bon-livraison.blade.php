<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bon de Livraison - {{ $colis->reference_colis }}</title>
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
            /* Space between Logo and Barcode */
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
            /* Visual separation for title row */
            display: block;
        }

        /* Info Grid Section */
        .info-grid {
            display: grid;
            grid-template-columns: 55% 40%;
            gap: 5%;
            margin-bottom: 30px;
        }

        .address-box {
            /* border: 1px solid #ccc; */
        }

        .address-label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .address-content {
            border: 1px solid #ddd;
            padding: 10px;
            min-height: 80px;
            font-size: 14px;
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
            font-weight: bold;
            width: 40%;
            background-color: #f5f5f5;
        }

        .meta-value {
            text-align: center;
            font-weight: bold;
            background-color: #d3d3d3;
            /* Light gray background for values logic based on image */
        }

        .meta-value.white-bg {
            background-color: #fff;
        }

        /* Products Table */
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .products-table th {
            background-color: #d3d3d3;
            color: #000;
            padding: 6px;
            text-align: left;
            border: none;
            font-size: 11px;
            font-weight: bold;
        }

        .products-table td {
            padding: 6px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        .products-table th.text-right,
        .products-table td.text-right {
            text-align: right;
        }

        .product-name {
            font-weight: bold;
            display: block;
        }

        .product-desc {
            font-size: 10px;
            color: #555;
            margin-left: 5px;
        }

        /* Tracking and Notes Section */
        .tracking-notes-grid {
            display: grid;
            grid-template-columns: 60% 35%;
            gap: 5%;
            margin-bottom: 20px;
            align-items: start;
        }

        .tracking-info {
            text-align: right;
            font-size: 11px;
        }

        .tracking-row {
            margin-bottom: 5px;
        }

        .tracking-barcode img {
            height: 40px;
            width: auto;
            max-width: 100%;
        }

        .notes-section {
            border: 1px solid #ccc;
            padding: 10px;
            font-size: 9px;
            margin-bottom: 20px;
            line-height: 1.3;
            text-align: justify;
        }

        .notes-label {
            font-weight: bold;
            margin-bottom: 3px;
            display: block;
        }

        /* Signatures */
        .signature-section {
            margin-top: 10px;
        }

        .signature-label {
            font-weight: bold;
            text-decoration: underline;
            font-size: 11px;
        }

        /* Footer */
        .page-footer {
            margin-top: 250px;
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
        .no-border-top {
            border-top: none !important;
        }

        @media print {
            body {
                padding: 0;
            }

            .container {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Unified Header Section Table -->
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
                     <img src="assets/img/barre_code.png" class="barcode-img" alt="Code Barre Reference Colis">
                </td>
                <td class="header-right" style="vertical-align: bottom;">
                    <div class="document-title">BON DE LIVRAISON</div>
                </td>
            </tr>
        </table>

        <!-- Info Grid -->
        <div class="info-grid">
            <div class="address-column">
                <span class="address-label">Adresse de livraison</span>
                <div class="address-content">
                   Nom & Prenoms : {{ $colis->name_destinataire }} {{ $colis->prenom_destinataire }}<br>
                   Adresse : {{ $colis->adresse_destinataire }}<br>
                   Email : {{ $colis->email_destinataire }}<br>
                   Contact : {{ $colis->contact_destinataire }}
                </div>
            </div>

            <div class="meta-column">
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Bon de Livraison No.</td>
                        <td class="meta-value">{{ $numeroBonLivraison }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Date:</td>
                        <td class="meta-value">{{ $dateLivraison }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Numéro de client:</td>
                        <td class="meta-value">{{ $colis->contact_expediteur ?? 'C' . $colis->id }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Créé par:</td>
                        <td class="meta-value">
                            {{ Auth::guard('admin')->user()->nom ?? 'ADAMA SYLLA' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Products Table -->
        <table class="products-table">
            <thead>
                <tr>
                    <th style="width: 70%;">Produit / Service</th>
                    <th class="text-right" style="width: 15%;">À livrer</th>
                    <th class="text-right" style="width: 15%;">Livré</th>
                </tr>
            </thead>
            <tbody>
                @foreach($colisDetails as $detail)
                    <tr>
                        <td>
                            <span class="product-name">{{ $detail['produit'] ?? 'COLIS STANDARD' }}</span>
                            <span class="product-desc">{{ $detail['description'] ?? '' }}</span>
                        </td>
                        <td class="text-right">{{ $detail['quantite'] ?? '1,00' }}</td>
                        <td class="text-right">0</td>
                    </tr>
                @endforeach
                <!-- Empty rows to fill space if needed, matching visual style usually implies just data -->
            </tbody>
        </table>

        <div style="border-bottom: 1px dashed #ccc; margin-bottom: 20px;"></div>

        <!-- Tracking Bar Code Area & Info -->
        <div class="tracking-notes-grid">
            <div class="tracking-barcode">
                <img src="assets/img/barre_code.png" style="width: 30%; height: 35px; object-fit: cover;"
                    alt="Code Barre Suivi">
            </div>
            <div class="tracking-info">
                <div class="tracking-row">Numéro de suivi: </div>
                <div class="tracking-row">Nombre de colis: <strong>{{ count($colisDetails) }}</strong></div>
            </div>
        </div>

        <!-- Notes -->
        <div class="notes-section">
            <span class="notes-label">Notes</span>
            Les colis et marchandise transportées par AFRIQUE FRET TRANSIT IMPORT EXPORT de la France vers la cote
            d'ivoire et de la cote d'ivoire vers la France. Le montant du transport, autres frais de douane et taxes
            doivent êtres solder avant livraison. les colis non soldés seront confisqué dans nos entrepôts jusqu'à la
            régularisation de la situation. passé délai 5 jours les frais de magasinage ainsi qu' une pénalité de 10%
            montant du seront appliqués et au delà 30 jours les colis et marchandises seront vendus pour remboursement
            des frais.
        </div>

        <!-- Signature -->
        <div class="signature-section">
            <div class="signature-label">Signature du client</div>
        </div>

        <!-- Footer -->
        <div class="page-footer">
            <div class="footer-center">
                <strong>7 AVENUE LOUIS BLERIOT LA COURNEUVE 93120 France | Tel. +33171894351 | contacts.aft@gmail.com
                    |</strong><br>
                N°TVA:FR96881916365 N°ORI FR88191636500011 SIRET:881916365 RCS Bobigny, EXO TVA, article 262 DU CGI
            </div>
        </div>
    </div>

</body>

</html>