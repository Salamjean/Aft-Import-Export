<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Devis Validé</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #0e914b 0%, #0e914b 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: white;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #0e914b;
            font-weight: bold;
        }
        .message {
            margin-bottom: 25px;
            font-size: 16px;
            line-height: 1.6;
        }
        .devis-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #fea219;
        }
        .devis-amount {
            font-size: 24px;
            font-weight: bold;
            color: #0e914b;
            text-align: center;
            margin: 15px 0;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .detail-item {
            padding: 10px;
            background: white;
            border-radius: 6px;
            border: 1px solid #e3e6f0;
        }
        .detail-label {
            font-weight: bold;
            color: #495057;
            font-size: 14px;
        }
        .detail-value {
            color: #6c757d;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #fea219 0%, #0e914b 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #e3e6f0;
        }
        .contact-info {
            margin-top: 15px;
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            background: #0e914b;
            color: white;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        @media (max-width: 600px) {
            .details-grid {
                grid-template-columns: 1fr;
            }
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h1>🎉 Votre devis est prêt !</h1>
            <p>AFT - IMPORT - EXPORT</p>
        </div>

        <!-- Contenu principal -->
        <div class="content">
            <!-- Salutation -->
            <div class="greeting">
                Bonjour {{ $devis->name_client }} {{ $devis->prenom_client }},
            </div>

            <!-- Message principal -->
            <div class="message">
                Nous avons le plaisir de vous informer que votre demande de devis a été traitée avec succès. 
                Votre devis est maintenant disponible.
            </div>

            <!-- Carte du devis -->
            <div class="devis-card">
                
                <div class="devis-amount">
                    {{ number_format($devis->montant_devis, 0, ',', ' ') }} {{ $devis->devise }}
                </div>

                <!-- Détails du devis -->
                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-label">Mode de Transport</div>
                        <div class="detail-value">
                            @if($devis->mode_transit == 'Maritime')
                                🚢 Maritime
                            @else
                                ✈️ Aérien
                            @endif
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Pays d'Expédition</div>
                        <div class="detail-value">{{ $devis->pays_expedition }}</div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Agence Destination</div>
                        <div class="detail-value">{{ $devis->agence_destination }}</div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Date de Demande</div>
                        <div class="detail-value">{{ $devis->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="message">
                <strong>Prochaines étapes :</strong><br>
                1. Consultez votre espace client pour voir les détails complets<br>
                2. N’ayez aucune hésitation à valider ce devis.<br>
                3. Notre équipe reste à votre disposition pour toute question
            </div>

            <!-- Bouton d'action -->
            {{-- <div style="text-align: center;">
                <a href="{{ url('/user/devis') }}" class="button">
                    📋 Voir mon devis en détail
                </a>
            </div> --}}

            <!-- Message de service client -->
            <div style="background: #e7f3ff; padding: 15px; border-radius: 6px; margin-top: 20px;">
                <strong>💼 Notre engagement :</strong><br>
                Chez AFT - IMPORT - EXPORT, nous nous engageons à vous offrir un service de qualité 
                et des tarifs compétitifs pour tous vos besoins logistiques.
            </div>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <div style="margin-bottom: 10px;">
                <strong>AFT - IMPORT - EXPORT</strong><br>
                Votre partenaire logistique de confiance
            </div>
            
            <div class="contact-info">
                📞 Contact : +33 1 23 45 67 89<br>
                📧 Email : contact@aft-app.com<br>
                🌐 Site : https://aft-app.com
            </div>
            
            <div style="margin-top: 15px; font-size: 12px; color: #858796;">
                Cet email a été envoyé automatiquement. Merci de ne pas y répondre.<br>
                © {{ date('Y') }} AFT - IMPORT - EXPORT. Tous droits réservés.
            </div>
        </div>
    </div>
</body>
</html>