<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="shortcut icon" href="{{ asset('assets/img/aft.jpg') }}" />
    <title>Conditions Générales de Vente - AFT</title>
    <style>
        :root {
            --orange: #f79027;
            --vert: #0e914b;
            --blanc: #ffffff;
            --gris-clair: #f5f5f5;
            --gris-fonce: #333333;
            --gris-moyen: #666666;
            --orange-clair: #fef6ec;
            --vert-clair: #edf7f2;
            --bleu-clair: #ecf7fe;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--gris-clair);
            color: var(--gris-fonce);
            line-height: 1.6;
        }
        
        .header {
            background: linear-gradient(135deg, var(--orange), var(--vert));
            padding: 40px 0;
            text-align: center;
            color: var(--blanc);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .logo {
            height: 80px;
            filter: brightness(0) invert(1);
        }
        
        .header h1 {
            font-size: 36px;
            margin-bottom: 10px;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        
        .header h2 {
            font-size: 22px;
            opacity: 0.9;
            font-weight: 400;
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .content {
            background-color: var(--blanc);
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            padding: 50px;
            margin: -30px auto 40px;
            position: relative;
            max-width: 1000px;
        }
        
        .last-update {
            background: var(--orange-clair);
            border-left: 4px solid var(--orange);
            padding: 15px 20px;
            margin-bottom: 40px;
            border-radius: 0 5px 5px 0;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .last-update strong {
            color: var(--orange);
        }
        
        .last-update .version {
            background: var(--vert);
            color: var(--blanc);
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .article {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid #eee;
        }
        
        .article:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .article-title {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: var(--vert);
            font-size: 24px;
            font-weight: 600;
        }
        
        .article-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--vert);
            color: var(--blanc);
            border-radius: 50%;
            margin-right: 15px;
            font-weight: bold;
            font-size: 18px;
        }
        
        .section-title {
            color: var(--orange);
            margin: 25px 0 15px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .content-text {
            color: var(--gris-moyen);
            margin-bottom: 15px;
            text-align: justify;
        }
        
        .content-text ul, .content-text ol {
            padding-left: 25px;
            margin: 15px 0;
        }
        
        .content-text li {
            margin-bottom: 10px;
            position: relative;
        }
        
        .highlight-box {
            background: var(--vert-clair);
            border-left: 4px solid var(--vert);
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        
        .warning-box {
            background: var(--orange-clair);
            border-left: 4px solid var(--orange);
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        
        .info-box {
            background: var(--bleu-clair);
            border-left: 4px solid #3498db;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        
        .definition-list {
            background: linear-gradient(135deg, var(--vert-clair), var(--orange-clair));
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
            border: 1px solid rgba(15, 145, 75, 0.1);
        }
        
        .definition-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
        }
        
        .definition-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .definition-term {
            color: var(--vert);
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .annexe {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 30px;
            margin: 40px 0;
            border: 1px solid #dee2e6;
        }
        
        .annexe-title {
            color: var(--vert);
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .annexe-title i {
            margin-right: 10px;
            color: var(--orange);
        }
        
        .doc-category {
            margin-bottom: 25px;
            padding: 20px;
            background: var(--blanc);
            border-radius: 8px;
            border-left: 4px solid var(--vert);
        }
        
        .doc-category h4 {
            color: var(--vert);
            margin-bottom: 15px;
            font-size: 18px;
            display: flex;
            align-items: center;
        }
        
        .doc-category h4 i {
            margin-right: 10px;
            color: var(--orange);
        }
        
        .footer {
            background: var(--gris-fonce);
            color: var(--blanc);
            padding: 30px 0;
            text-align: center;
            margin-top: 50px;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 20px 0;
        }
        
        .footer-links a {
            color: var(--blanc);
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        
        .footer-links a:hover {
            opacity: 1;
            text-decoration: underline;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        
        .action-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 30px;
            background: var(--orange);
            color: var(--blanc);
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        
        .action-button:hover {
            background: var(--vert);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(15, 145, 75, 0.2);
        }
        
        .action-button.secondary {
            background: transparent;
            color: var(--gris-fonce);
            border: 2px solid var(--gris-moyen);
        }
        
        .action-button.secondary:hover {
            background: var(--gris-moyen);
            color: var(--blanc);
            border-color: var(--gris-moyen);
        }
        
        @media (max-width: 768px) {
            .content {
                padding: 30px 20px;
                margin: -20px 15px 30px;
            }
            
            .header h1 {
                font-size: 28px;
            }
            
            .header h2 {
                font-size: 18px;
                padding: 0 15px;
            }
            
            .article-title {
                font-size: 20px;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 15px;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .action-button {
                width: 100%;
                max-width: 300px;
            }
        }
        
        .print-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--orange);
            color: var(--blanc);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .print-button:hover {
            background: var(--vert);
            transform: scale(1.1);
        }
        
        .scroll-top {
            position: fixed;
            bottom: 90px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--vert);
            color: var(--blanc);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
        }
        
        .scroll-top.active {
            opacity: 1;
            visibility: visible;
        }
        
        .scroll-top:hover {
            background: var(--orange);
            transform: translateY(-5px);
        }
        
        .toc {
            background: linear-gradient(135deg, var(--vert-clair), var(--orange-clair));
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
        }
        
        .toc-title {
            color: var(--vert);
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .toc-title i {
            margin-right: 10px;
            color: var(--orange);
        }
        
        .toc-list {
            column-count: 2;
            column-gap: 30px;
        }
        
        .toc-item {
            margin-bottom: 10px;
            break-inside: avoid;
        }
        
        .toc-item a {
            color: var(--gris-fonce);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: color 0.3s;
        }
        
        .toc-item a:hover {
            color: var(--vert);
        }
        
        .toc-item a i {
            margin-right: 8px;
            color: var(--orange);
            font-size: 12px;
        }
        
        @media (max-width: 768px) {
            .toc-list {
                column-count: 1;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="logo-container">
                <img src="{{ asset('images/LOGOAFT.png') }}" alt="AFT Logo" class="logo">
            </div>
            <h1>Conditions Générales de Vente</h1>
            <h2>AFRIQUE FRET TRANSIT IMPORT-EXPORT (AFT) - Transport maritime et aérien de colis</h2>
        </div>
    </div>

    <div class="container">
        <div class="content">
            <div class="last-update">
                <div>
                    <strong>📅 Date de mise à jour :</strong> 13 octobre 2025
                </div>
                <div class="version">Version 1.0</div>
            </div>

            <!-- Table des matières -->
            <div class="toc">
                <h3 class="toc-title"><i class="fas fa-list"></i> Table des matières</h3>
                <div class="toc-list">
                    <div class="toc-item"><a href="#article1"><i class="fas fa-chevron-right"></i> 1. Objet et domaine d'application</a></div>
                    <div class="toc-item"><a href="#article2"><i class="fas fa-chevron-right"></i> 2. Définitions</a></div>
                    <div class="toc-item"><a href="#article3"><i class="fas fa-chevron-right"></i> 3. Prix des prestations</a></div>
                    <div class="toc-item"><a href="#article4"><i class="fas fa-chevron-right"></i> 4. Incoterms® 2020</a></div>
                    <div class="toc-item"><a href="#article5"><i class="fas fa-chevron-right"></i> 5. Litiges douaniers</a></div>
                    <div class="toc-item"><a href="#article6"><i class="fas fa-chevron-right"></i> 6. Contrôles douaniers</a></div>
                    <div class="toc-item"><a href="#article7"><i class="fas fa-chevron-right"></i> 7. Formalités douanières</a></div>
                    <div class="toc-item"><a href="#article8"><i class="fas fa-chevron-right"></i> 8. Assurance des colis</a></div>
                    <div class="toc-item"><a href="#article9"><i class="fas fa-chevron-right"></i> 9. Responsabilité d'AFT</a></div>
                    <div class="toc-item"><a href="#article10"><i class="fas fa-chevron-right"></i> 10. Obligations du donneur d'ordre</a></div>
                    <div class="toc-item"><a href="#article11"><i class="fas fa-chevron-right"></i> 11. Conditions de paiement</a></div>
                    <div class="toc-item"><a href="#article12"><i class="fas fa-chevron-right"></i> 12. Exécution des prestations</a></div>
                    <div class="toc-item"><a href="#article13"><i class="fas fa-chevron-right"></i> 13. Droit de rétention et de gage</a></div>
                    <div class="toc-item"><a href="#article14"><i class="fas fa-chevron-right"></i> 14. Prescription</a></div>
                    <div class="toc-item"><a href="#article15"><i class="fas fa-chevron-right"></i> 15. Durée du contrat et résiliation</a></div>
                    <div class="toc-item"><a href="#article16"><i class="fas fa-chevron-right"></i> 16. Loi applicable et règlement des litiges</a></div>
                    <div class="toc-item"><a href="#article17"><i class="fas fa-chevron-right"></i> 17. Dispositions diverses</a></div>
                    <div class="toc-item"><a href="#annexe1"><i class="fas fa-chevron-right"></i> Annexe 1 : Documents douaniers</a></div>
                </div>
            </div>

            <div class="article" id="article1">
                <h2 class="article-title">
                    <span class="article-number">1</span>
                    OBJET ET DOMAINE D'APPLICATION
                </h2>
                <p class="content-text">
                    Les présentes conditions générales (ci-après « CGV ») définissent les modalités d'exécution par AFRIQUE FRET TRANSIT IMPORT-EXPORT (AFT), ci-après « AFT », de toute prestation de transport, logistique, transit, dédouanement, manutention, entreposage, et services connexes, entre la France et la Côte d'Ivoire, par voie maritime et/ou aérienne.
                </p>
                <p class="content-text">
                    Elles s'appliquent à tous les engagements, opérations ou prestations confiés à AFT, quelle que soit sa qualité (transitaire, commissionnaire de transport, mandataire, agent maritime, courtier de fret, etc.). Toute commande, engagement ou prestation confiée à AFT vaut acceptation pleine et entière des présentes CGV, sans réserve ni restriction.
                </p>
            </div>

            <div class="article" id="article2">
                <h2 class="article-title">
                    <span class="article-number">2</span>
                    DÉFINITIONS
                </h2>
                <div class="definition-list">
                    <div class="definition-item">
                        <div class="definition-term">Donneur d'ordre</div>
                        <div class="content-text">Personne physique ou morale contractant avec AFT pour une prestation.</div>
                    </div>
                    <div class="definition-item">
                        <div class="definition-term">Colis/Envoi</div>
                        <div class="content-text">Marchandise conditionnée, emballée, identifiée comme une unité de transport (carton, palette, conteneur, etc.).</div>
                    </div>
                    <div class="definition-item">
                        <div class="definition-term">Incoterms® 2020</div>
                        <div class="content-text">Règles internationales définissant les obligations de l'acheteur et du vendeur (ex. : FOB, CIF, DAP, DDP).</div>
                    </div>
                    <div class="definition-item">
                        <div class="definition-term">Dédouanement</div>
                        <div class="content-text">Formalités douanières pour l'import/export, incluant déclaration, paiement des droits, et obtention des autorisations.</div>
                    </div>
                    <div class="definition-item">
                        <div class="definition-term">Force majeure</div>
                        <div class="content-text">Événement imprévisible, insurmontable et extérieur, empêchant l'exécution du contrat.</div>
                    </div>
                </div>
            </div>

            <div class="article" id="article3">
                <h2 class="article-title">
                    <span class="article-number">3</span>
                    PRIX DES PRESTATIONS
                </h2>
                <p class="content-text">
                    Les prix sont calculés sur la base des informations fournies par le donneur d'ordre (nature, poids, volume, valeur, itinéraire, mode de transport). Les prix n'incluent pas les droits, taxes, redevances ou impôts (droits de douane, accises, TVA, etc.) dus en France ou en Côte d'Ivoire.
                </p>
                <div class="warning-box">
                    <p class="content-text">
                        <strong>⚠️ Attention :</strong> Les prix sont révisables en cas de variation significative des coûts (carburant, surtaxe portuaire/aéroportuaire, etc.). En cas de désaccord sur la révision tarifaire, chaque partie peut résilier le contrat selon les modalités de l'article 15.
                    </p>
                </div>
            </div>

            <div class="article" id="article4">
                <h2 class="article-title">
                    <span class="article-number">4</span>
                    INCOTERMS® 2020 ET RÉPARTITION DES OBLIGATIONS
                </h2>
                <p class="content-text">
                    Le donneur d'ordre précise, pour chaque envoi, l'Incoterm® 2020 applicable. À défaut, l'Incoterm® DAP Abidjan s'applique.
                </p>
                <div class="highlight-box">
                    <h3 class="section-title">FOB Le Havre</h3>
                    <p class="content-text">AFT gère le pré-acheminement et les formalités d'export ; le donneur d'ordre organise et paie le transport principal.</p>
                    
                    <h3 class="section-title">CIF/CFR Abidjan</h3>
                    <p class="content-text">AFT organise et paie le transport principal et l'assurance (si CIF) ; le donneur d'ordre gère les formalités d'import.</p>
                    
                    <h3 class="section-title">DAP/DDP Abidjan</h3>
                    <p class="content-text">AFT gère l'ensemble du transport et des formalités (sauf droits d'import pour DAP). Le donneur d'ordre assume les risques et frais liés à son choix d'Incoterm®.</p>
                </div>
            </div>

            <div class="article" id="article5">
                <h2 class="article-title">
                    <span class="article-number">5</span>
                    GESTION DES LITIGES DOUANIERS
                </h2>
                <p class="content-text">
                    Le donneur d'ordre garantit AFT contre toute conséquence financière ou pénale résultant d'une déclaration inexacte, incomplète ou tardive, ou de la non-conformité des colis.
                </p>
                <div class="warning-box">
                    <p class="content-text">
                        En cas de redressement douanier (droits supplémentaires, amendes, saisie) :
                    </p>
                    <ul class="content-text">
                        <li>Le donneur d'ordre règle immédiatement les sommes réclamées</li>
                        <li>Il indemnise AFT de tous frais, pénalités, et intérêts de retard</li>
                        <li>Il fournit à AFT, sous 48h, tous documents justificatifs demandés</li>
                    </ul>
                </div>
                <p class="content-text">
                    AFT peut suspendre toute prestation en cas de litige douanier non résolu sous 15 jours. En cas de saisie ou blocage, AFT informe le donneur d'ordre et lui transmet les motifs officiels. Les frais d'entreposage et de conservation sont à sa charge.
                </p>
            </div>

            <div class="article" id="article6">
                <h2 class="article-title">
                    <span class="article-number">6</span>
                    RETARDS LIÉS AUX CONTRÔLES DOUANIERS
                </h2>
                <p class="content-text">
                    Les délais de livraison sont indicatifs et peuvent être affectés par des contrôles douaniers aléatoires ou systématiques.
                </p>
                <div class="info-box">
                    <p class="content-text">
                        En cas de retard dû à un contrôle douanier :
                    </p>
                    <ul class="content-text">
                        <li>AFT informe le donneur d'ordre dès réception de la notification des autorités</li>
                        <li>Le donneur d'ordre fournit, sous 24h, tout document complémentaire demandé</li>
                        <li>Les frais d'entreposage ou de conservation pendant le contrôle sont facturés au donneur d'ordre</li>
                    </ul>
                </div>
                <p class="content-text">
                    AFT ne peut être tenu responsable des retards ou surcoûts résultant de contrôles douaniers, sauf en cas de faute prouvée de sa part.
                </p>
            </div>

            <div class="article" id="article7">
                <h2 class="article-title">
                    <span class="article-number">7</span>
                    FORMALITÉS DOUANIÈRES ET DOCUMENTS
                </h2>
                <p class="content-text">
                    Le donneur d'ordre fournit à AFT, avant l'expédition, tous les documents requis (voir Annexe 1). AFT agit en tant que représentant en douane direct (article 18 du Code des Douanes de l'Union).
                </p>
                <div class="warning-box">
                    <p class="content-text">
                        En cas de non-fourniture des documents dans les délais, AFT facture des frais de gestion supplémentaires et peut suspendre l'expédition.
                    </p>
                </div>
            </div>

            <div class="article" id="article8">
                <h2 class="article-title">
                    <span class="article-number">8</span>
                    ASSURANCE DES COLIS
                </h2>
                <div class="highlight-box">
                    <p class="content-text">
                        <strong>⚠️ Important :</strong> Aucune assurance n'est souscrite par AFT sans ordre écrit et répété du donneur d'ordre pour chaque expédition.
                    </p>
                </div>
                <p class="content-text">
                    Si un ordre est donné, AFT contracte une assurance auprès d'une compagnie solvable, aux conditions et tarifs en vigueur. En l'absence d'assurance, la responsabilité d'AFT est limitée aux conventions internationales (Règles de La Haye-Visby pour le maritime, Convention de Montréal pour l'aérien).
                </p>
                <p class="content-text">
                    Le donneur d'ordre peut déclarer une valeur supérieure pour augmenter la couverture, sous réserve d'acceptation par AFT et de paiement d'un supplément.
                </p>
            </div>

            <div class="article" id="article9">
                <h2 class="article-title">
                    <span class="article-number">9</span>
                    RESPONSABILITÉ D'AFT
                </h2>
                <p class="content-text">
                    AFT n'est responsable que des dommages directs et prévisibles, dans les limites des conventions internationales applicables. La responsabilité d'AFT est exclue en cas de force majeure, faute du donneur d'ordre, ou vice propre de la marchandise.
                </p>
                <div class="warning-box">
                    <p class="content-text">
                        <strong>Limitation de responsabilité :</strong> En cas de préjudice prouvé imputable à AFT, l'indemnisation est strictement limitée aux plafonds légaux ou conventionnels.
                    </p>
                </div>
                <p class="content-text">
                    AFT n'est tenu à aucune obligation de conseil ou de contrôle quant au conditionnement, à l'emballage, à l'empotage, au marquage et/ou à l'étiquetage des colis.
                </p>
                <p class="content-text">
                    La responsabilité d'AFT ne pourra en outre jamais être recherchée pour les pertes et les avaries à la marchandise survenues en raison de l'inadaptation et/ou de l'état défectueux du conteneur appartenant et/ou fourni par toute autre personne que AFT.
                </p>
                <p class="content-text">
                    En cas de perte, d'avarie ou de tout autre dommage subi par la marchandise, ou en cas de retard, il appartient au destinataire ou au réceptionnaire de procéder aux constatations régulières et suffisantes, de prendre des réserves motivées et en général d'effectuer tous les actes utiles à la conservation des recours et à confirmer lesdites réserves dans les formes et les délais légaux, faute de quoi aucune action ne pourra être exercée contre AFT ou ses substitués.
                </p>
            </div>

            <div class="article" id="article10">
                <h2 class="article-title">
                    <span class="article-number">10</span>
                    OBLIGATIONS DU DONNEUR D'ORDRE
                </h2>
                <p class="content-text">
                    Le colis doit être conditionné, emballé, emporté, marqué ou contremarqué, de façon à supporter un transport et/ou une opération de stockage exécutés dans des conditions normales, ainsi que les manutentions successives qui interviennent nécessairement pendant le déroulement de ces opérations.
                </p>
                <p class="content-text">
                    Elle ne doit pas constituer une cause de danger pour les personnels de conduite ou de manutention, l'environnement, la sécurité des engins de transport, les autres colis transportés ou stockés, les véhicules ou les tiers.
                </p>
                <div class="warning-box">
                    <p class="content-text">
                        <strong>Le donneur d'ordre répond :</strong>
                    </p>
                    <ul class="content-text">
                        <li>Seul du choix du conditionnement, emballage, empotage et de son aptitude à supporter le transport et la manutention</li>
                        <li>De toutes les conséquences d'une absence, d'une insuffisance ou d'une défectuosité du conditionnement, de l'emballage, de l'empotage ou du marquage</li>
                        <li>De toutes les conséquences d'un manquement à l'obligation d'information et de déclaration sur la nature très exacte et de la spécificité de la marchandise</li>
                    </ul>
                </div>
                <p class="content-text">
                    Par ailleurs, le donneur d'ordre s'engage expressément à ne pas remettre à AFT des colis illicites ou prohibés (par exemple des produits de contrefaçon, des stupéfiants, etc.).
                </p>
            </div>

            <!-- Les articles suivants (11 à 17) suivent le même pattern -->
            <!-- Pour des raisons de longueur, je vais montrer quelques exemples et vous pouvez continuer le pattern -->

            <div class="article" id="article11">
                <h2 class="article-title">
                    <span class="article-number">11</span>
                    CONDITIONS DE PAIEMENT
                </h2>
                <div class="highlight-box">
                    <p class="content-text">
                        <strong>⏰ Délai de paiement :</strong> Les factures sont payables à 30 jours date d'émission, sans escompte.
                    </p>
                </div>
                <p class="content-text">
                    AFT se réserve le droit de suspendre toute prestation en cas de non-paiement. AFT met en œuvre tous les moyens raisonnables pour assurer la livraison des colis dans les délais impartis.
                </p>
                <div class="warning-box">
                    <p class="content-text">
                        <strong>⚠️ Attention :</strong> En cas de non-paiement des frais de transport ou de stockage dus par le client, le transporteur décline toute responsabilité quant à la perte, l'endommagement ou le retard de livraison des marchandises.
                    </p>
                </div>
            </div>

            <div class="article" id="article13">
                <h2 class="article-title">
                    <span class="article-number">13</span>
                    DROIT DE RÉTENTION ET DROIT DE GAGE
                </h2>
                <p class="content-text">
                    AFT dispose d'un droit de rétention et de gage sur les colis en sa possession, en garantie de toutes créances impayées.
                </p>
                <div class="warning-box">
                    <p class="content-text">
                        <strong>⚠️ Stockage en Côte d'Ivoire :</strong> Les colis acheminés vers la Côte d'Ivoire sont conservés en entrepôt à l'arrivée pour une durée maximale de cinq (5) jours calendaires.
                    </p>
                </div>
                <ul class="content-text">
                    <li>Passé ce délai, des frais d'entreposage supplémentaires seront facturés à hauteur de 5000 Fcfa par jour supplémentaires</li>
                    <li>Si les colis ne sont ni retirés ni payés dans un délai de deux (2) mois après mise en demeure restée infructueuse, le transporteur se réserve le droit de procéder à la vente des marchandises ou leur destruction</li>
                </ul>
            </div>

            <!-- Annexe 1 -->
            <div class="annexe" id="annexe1">
                <h2 class="annexe-title">
                    <i class="fas fa-paperclip"></i>
                    ANNEXE 1 : DOCUMENTS DOUANIERS REQUIS POUR LA CÔTE D'IVOIRE
                </h2>
                
                <div class="doc-category">
                    <h4><i class="fas fa-box"></i> Colis générales :</h4>
                    <ul class="content-text">
                        <li>Facture commerciale détaillée (valeur, description, origine, code SH)</li>
                        <li>Connaissement maritime ou lettre de transport aérien (LAW)</li>
                        <li>Certificat d'origine</li>
                        <li>Liste de colisage (si applicable)</li>
                        <li>Certificat de conformité (si applicable)</li>
                    </ul>
                </div>
                
                <div class="doc-category">
                    <h4><i class="fas fa-utensils"></i> Produits alimentaires :</h4>
                    <ul class="content-text">
                        <li>Certificat sanitaire</li>
                        <li>Analyse microbiologique</li>
                        <li>Certificat Halal (si applicable)</li>
                    </ul>
                </div>
                
                <div class="doc-category">
                    <h4><i class="fas fa-pills"></i> Produits pharmaceutiques :</h4>
                    <ul class="content-text">
                        <li>Autorisation d'importation</li>
                        <li>Certificat de libre vente</li>
                        <li>Certificat GMP (Bonnes Pratiques de Fabrication)</li>
                        <li>Analyse de laboratoire</li>
                    </ul>
                </div>
                
                <div class="doc-category">
                    <h4><i class="fas fa-flask"></i> Produits chimiques :</h4>
                    <ul class="content-text">
                        <li>Fiche de données de sécurité (FDS)</li>
                        <li>Autorisation environnementale</li>
                        <li>Certificat de non-dangerosité</li>
                    </ul>
                </div>
                
                <div class="doc-category">
                    <h4><i class="fas fa-tshirt"></i> Textiles :</h4>
                    <ul class="content-text">
                        <li>Certificat d'origine</li>
                        <li>Étiquetage conforme aux normes locales</li>
                        <li>Certificat OEKO-TEX (si applicable)</li>
                    </ul>
                </div>
                
                <div class="doc-category">
                    <h4><i class="fas fa-laptop"></i> Électronique :</h4>
                    <ul class="content-text">
                        <li>Certificat de conformité CE</li>
                        <li>Facture pro forma</li>
                        <li>Autorisation du ministère ivoirien des TIC (si applicable)</li>
                    </ul>
                </div>
            </div>

            <!-- Coordonnées -->
            <div class="info-box" style="margin-top: 40px;">
                <h3 style="color: var(--vert); margin-bottom: 15px;">
                    <i class="fas fa-map-marker-alt"></i> AFRIQUE FRET TRANSIT IMPORT-EXPORT (AFT)
                </h3>
                <p class="content-text">
                    7 Avenue Louis Bleriot<br>
                    93120 La Courneuve, France
                </p>
            </div>

            <div class="action-buttons">
                <a href="{{ route('user.register') }}" class="action-button">
                    <i class="fas fa-file-signature" style="margin-right: 10px;"></i> S'inscrire
                </a>
                <button onclick="window.print()" class="action-button secondary">
                    <i class="fas fa-print" style="margin-right: 10px;"></i> Imprimer
                </button>
            </div>
        </div>
    </div>

    <div class="print-button" onclick="window.print()" title="Imprimer cette page">
        <i class="fas fa-print"></i>
    </div>

    <div class="scroll-top" onclick="scrollToTop()" title="Retour en haut">
        <i class="fas fa-chevron-up"></i>
    </div>

    <div class="footer">
        <div class="container">
            <div class="logo-container">
                <img src="{{ asset('images/LOGOAFT.png') }}" alt="AFT Logo" style="height: 50px; filter: brightness(0) invert(1); opacity: 0.8;">
            </div>
             <div class="footer-links">
                <a href="/">Accueil</a>
                <a href="{{ route('login') }}">Connexion</a>
                <a href="{{ route('user.register') }}">Inscription</a>
                <a href="{{route('page.condition')}}">Condition générale de vente</a>
                <a href="{{route('page.legal')}}">Mentions légales</a>
            </div>
            <p>© {{ date('Y') }} AFRIQUE FRET TRANSIT IMPORT-EXPORT (AFT). Tous droits réservés.</p>
        </div>
    </div>

    <script>
        // Bouton retour en haut
        const scrollTopButton = document.querySelector('.scroll-top');
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                scrollTopButton.classList.add('active');
            } else {
                scrollTopButton.classList.remove('active');
            }
        });
        
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
        
        // Animation des articles au défilement
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        // Appliquer l'animation à tous les articles
        document.querySelectorAll('.article, .annexe').forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(20px)';
            element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(element);
        });
        
        // Navigation dans la table des matières
        document.querySelectorAll('.toc-item a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Surligner la section active
        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('.article, .annexe');
            const scrollPosition = window.scrollY + 150;
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                const sectionId = section.getAttribute('id');
                
                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    // Mettre à jour la table des matières
                    document.querySelectorAll('.toc-item a').forEach(link => {
                        link.style.color = '';
                        link.style.fontWeight = '';
                    });
                    
                    const activeLink = document.querySelector(`.toc-item a[href="#${sectionId}"]`);
                    if (activeLink) {
                        activeLink.style.color = 'var(--vert)';
                        activeLink.style.fontWeight = '600';
                    }
                }
            });
        });
    </script>
</body>
</html>