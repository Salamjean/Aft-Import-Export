<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Étiquette Dépôt - {{ $depot->reference }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 6pt; /* Réduit de 7pt à 6pt */
            line-height: 1;
            width: 50mm;
            height: 80mm;
            margin: 0;
            padding: 1mm;
        }
        .etiquette {
            border: 0.5pt solid #000;
            height: 78mm;
            padding: 0.8mm; /* Légèrement réduit */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .header {
            text-align: center;
            border-bottom: 0.5pt solid #000;
            padding-bottom: 0.8mm; /* Réduit */
            margin-bottom: 0.8mm; /* Réduit */
        }
        .header h1 {
            font-size: 7pt; /* Réduit de 8pt à 7pt */
            font-weight: bold;
            color: #000;
            margin: 0;
            line-height: 1;
        }
        .qr-section {
            text-align: center;
            margin: 0.8mm 0; /* Réduit */
        }
        .qr-code {
            width: 25mm;
            height: 25mm;
            margin: 0 auto;
        }
        .reference {
            font-size: 8pt; /* Réduit de 9pt à 8pt */
            font-weight: bold;
            text-align: center;
            margin: 0.8mm 0; /* Réduit */
        }
        .info-section {
            flex-grow: 1;
        }
        .info-row {
            display: flex;
            margin-bottom: 0.4mm; /* Réduit */
            border-bottom: 0.3pt dotted #ccc;
            padding-bottom: 0.4mm; /* Réduit */
        }
        .info-label {
            font-weight: bold;
            width: 18mm;
            flex-shrink: 0;
            font-size: 5.5pt; /* Réduit */
        }
        .info-value {
            flex-grow: 1;
            font-size: 5.5pt; /* Réduit */
        }
        .footer {
            border-top: 0.5pt solid #000;
            padding-top: 0.8mm; /* Réduit */
            text-align: center;
            font-size: 5pt; /* Réduit de 6pt à 5pt */
            color: #666;
            line-height: 1;
        }
        .compact-text {
            font-size: 5pt; /* Pour les textes longs */
        }
        @media print {
            body {
                margin: 0;
                padding: 1mm;
            }
            .etiquette {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    @foreach($codes as $code)
    <div class="etiquette">
        <div class="header">
            <h1>AFT IMPORT EXPORT</h1>
            <div style="font-size: 5pt;">DÉPÔT PROGRAMMÉ</div> <!-- Réduit de 6pt à 5pt -->
        </div>

        <div class="qr-section">
            @php
                // Déterminer si on est en mode PDF ou web
                $isPdf = request()->routeIs('*.download*') || str_contains(request()->url(), 'download');
                
                // Chemin du QR code
                $qrPath = $code['qr_code'] ?? '';
                
                if (!empty($qrPath)) {
                    if ($isPdf) {
                        // Mode PDF : utiliser public_path()
                        $qrSrc = public_path($qrPath);
                        $fileExists = file_exists($qrSrc);
                    } else {
                        // Mode web : utiliser asset()
                        $qrSrc = asset($qrPath);
                        $fileExists = true;
                    }
                } else {
                    $fileExists = false;
                    $qrSrc = '';
                }
            @endphp

            @if(!empty($qrPath) && $fileExists)
                <img src="{{ $qrSrc }}" class="qr-code" alt="QR Code {{ $code['code'] }}">
            @else
                <div style="border:1pt dashed #ccc; width:25mm;height:25mm;display:flex;align-items:center;justify-content:center;flex-direction:column;">
                    <span style="font-size:5pt;text-align:center;">QR CODE<br>NON DISPONIBLE</span> <!-- Réduit -->
                    @if(!empty($code['code']))
                        <span style="font-size:3.5pt;color:red;margin-top:1px;">{{ $code['code'] }}</span> <!-- Réduit -->
                    @endif
                </div>
            @endif
        </div>

        <div class="reference">
            {{ $code['code'] }}
        </div>

        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Référence:</div>
                <div class="info-value">{{ $depot->reference }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nature:</div>
                <div class="info-value">{{ $depot->nature_objet }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Destinataire:</div>
                <div class="info-value">{{ $depot->nom_concerne }} {{ $depot->prenom_concerne }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Contact:</div>
                <div class="info-value">{{ $depot->contact }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Adresse:</div>
                <div class="info-value" style="font-size:5pt;">{{ Str::limit($depot->adresse_depot, 45) }}</div> <!-- Réduit et augmenté la limite -->
            </div>
            <div class="info-row">
                <div class="info-label">Chauffeur:</div>
                <div class="info-value">{{ $depot->chauffeur->nom }} {{ $depot->chauffeur->prenom }}</div>
            </div>
            @if($depot->date_depot)
            <div class="info-row">
                <div class="info-label">Date dépôt:</div>
                <div class="info-value">{{ $depot->date_depot }}</div>
            </div>
            @endif
        </div>

        <div class="footer">
            Généré le {{ now()->format('d/m/Y H:i') }} | {{ $loop->iteration }}/{{ count($codes) }}
        </div>
    </div>
    @endforeach
</body>
</html>