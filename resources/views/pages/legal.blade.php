<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions Légales - AFT</title>
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
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .logo {
            height: 80px;
            filter: brightness(0) invert(1);
        }
        
        .header h1 {
            font-size: 36px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .header p {
            font-size: 18px;
            opacity: 0.9;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .effective-date {
            background: var(--orange-clair);
            color: var(--gris-fonce);
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            margin-top: 15px;
            font-weight: 600;
        }
        
        .content {
            background-color: var(--blanc);
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            padding: 50px;
            margin: -30px auto 40px;
            position: relative;
        }
        
        .legal-section {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid #eee;
        }
        
        .legal-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .section-title {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: var(--vert);
            font-size: 24px;
            font-weight: 600;
        }
        
        .section-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--vert);
            color: var(--blanc);
            border-radius: 50%;
            margin-right: 15px;
            font-size: 18px;
        }
        
        .section-subtitle {
            color: var(--orange);
            margin: 25px 0 15px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .section-subtitle i {
            margin-right: 10px;
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
        
        .contact-info {
            background: linear-gradient(135deg, var(--orange-clair), var(--vert-clair));
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
            border: 1px solid rgba(15, 145, 75, 0.1);
        }
        
        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .contact-item:last-child {
            margin-bottom: 0;
        }
        
        .contact-icon {
            color: var(--vert);
            margin-right: 15px;
            font-size: 20px;
            min-width: 30px;
            text-align: center;
        }
        
        .company-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }
        
        .company-card {
            background: var(--blanc);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #eee;
        }
        
        .company-card h4 {
            color: var(--vert);
            margin-bottom: 15px;
            font-size: 18px;
            display: flex;
            align-items: center;
        }
        
        .company-card h4 i {
            margin-right: 10px;
            color: var(--orange);
        }
        
        .legal-reference {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
            border: 1px solid #dee2e6;
        }
        
        .legal-reference h4 {
            color: var(--vert);
            margin-bottom: 10px;
            font-size: 16px;
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
            flex-wrap: wrap;
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
            
            .header p {
                font-size: 16px;
                padding: 0 15px;
            }
            
            .section-title {
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
            
            .company-info {
                grid-template-columns: 1fr;
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
        
        .law-badge {
            display: inline-block;
            background: var(--vert);
            color: var(--blanc);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
            vertical-align: middle;
        }
        
        .quick-links {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 25px 0;
        }
        
        .quick-link {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background: var(--gris-clair);
            border-radius: 5px;
            text-decoration: none;
            color: var(--gris-fonce);
            transition: all 0.3s;
            border: 1px solid #ddd;
        }
        
        .quick-link:hover {
            background: var(--vert);
            color: var(--blanc);
            transform: translateY(-2px);
        }
        
        .quick-link i {
            margin-right: 8px;
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
            <h1>Mentions Légales</h1>
            <p>Informations légales relatives au site https://aft-app.com/</p>
            <div class="effective-date">En vigueur au 19/09/2025</div>
        </div>
    </div>

    <div class="container">
        <div class="content">
            <!-- Introduction -->
            <div class="legal-section">
                <h2 class="section-title">
                    <span class="section-icon"><i class="fas fa-gavel"></i></span>
                    Informations légales
                </h2>
                <p class="content-text">
                    Conformément aux dispositions de la loi n°2004-575 du 21 juin 2004 pour la Confiance en l'économie numérique, il est porté à la connaissance des utilisateurs et visiteurs, ci-après l'"Utilisateur", du site https://aft-app.com/, ci-après le "Site", les présentes mentions légales.
                </p>
                <div class="warning-box">
                    <p class="content-text">
                        <strong>Acceptation des mentions légales :</strong> La connexion et la navigation sur le Site par l'Utilisateur implique l'acceptation intégrale et sans réserve des présentes mentions légales.
                    </p>
                </div>
                <p class="content-text">
                    Ces dernières sont accessibles sur le Site à la rubrique "Mentions légales".
                </p>
                
                <div class="quick-links">
                    <a href="#edition" class="quick-link">
                        <i class="fas fa-building"></i> Édition du site
                    </a>
                    <a href="#hebergement" class="quick-link">
                        <i class="fas fa-server"></i> Hébergement
                    </a>
                    <a href="#donnees" class="quick-link">
                        <i class="fas fa-database"></i> Données personnelles
                    </a>
                    <a href="#liens" class="quick-link">
                        <i class="fas fa-link"></i> Liens utiles
                    </a>
                </div>
            </div>

            <!-- Édition du site -->
            <div class="legal-section" id="edition">
                <h2 class="section-title">
                    <span class="section-icon"><i class="fas fa-building"></i></span>
                    Édition du site
                </h2>
                <p class="content-text">
                    L'édition du Site est assurée par la société KKS-TECHNOLOGIES, SARL au capital de 5000,001 euros, immatriculée à ABIDJAN dont le siège social est situé au Cocody Angré 8 eme Tranche.
                </p>
                
                <div class="company-info">
                    <div class="company-card">
                        <h4><i class="fas fa-info-circle"></i> Informations société</h4>
                        <div class="contact-item">
                            <div class="contact-icon"><i class="fas fa-landmark"></i></div>
                            <div>
                                <strong>KKS-TECHNOLOGIES</strong><br>
                                SARL au capital de 5 000,001 €<br>
                                Immatriculée à ABIDJAN
                            </div>
                        </div>
                    </div>
                    
                    <div class="company-card">
                        <h4><i class="fas fa-user-tie"></i> Direction</h4>
                        <div class="contact-item">
                            <div class="contact-icon"><i class="fas fa-user"></i></div>
                            <div>
                                <strong>Directeur de la publication :</strong><br>
                                KADIO KOUAME SERGE
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hébergement -->
            <div class="legal-section" id="hebergement">
                <h2 class="section-title">
                    <span class="section-icon"><i class="fas fa-server"></i></span>
                    Hébergement
                </h2>
                <p class="content-text">
                    L'hébergeur du Site est la société Hostinger, dont le siège social est situé à l'adresse suivante :
                </p>
                
                <div class="contact-info">
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <strong>Hostinger International Ltd.</strong><br>
                            61 Lordou Vironos Street<br>
                            6023 Larnaca, Chypre
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accès au site -->
            <div class="legal-section">
                <h2 class="section-title">
                    <span class="section-icon"><i class="fas fa-wifi"></i></span>
                    Accès au site
                </h2>
                <p class="content-text">
                    Le Site est normalement accessible, à tout moment, à l'utilisateur. Toutefois, l'Éditeur pourra, à tout moment, suspendre, limiter ou interrompre le Site afin de procéder, notamment, à des mises à jour ou des modifications de son contenu.
                </p>
                <div class="info-box">
                    <p class="content-text">
                        <strong>⚠️ Limitation de responsabilité :</strong> L'Éditeur ne pourra en aucun cas être tenu responsable des conséquences éventuelles de cette indisponibilité sur les activités de l'Utilisateur.
                    </p>
                </div>
            </div>

            <!-- Collecte des données -->
            <div class="legal-section" id="donnees">
                <h2 class="section-title">
                    <span class="section-icon"><i class="fas fa-database"></i></span>
                    Collecte et protection des données
                </h2>
                <p class="content-text">
                    Le Site assure à l'Utilisateur une collecte et un traitement des données personnelles dans le respect de la vie privée conformément à la loi n°78-17 du 6 janvier 1978 relative à l'informatique, aux fichiers aux libertés et dans le respect de la réglementation applicable en matière de traitement des données à caractère personnel conformément au règlement (UE) 2016/679 du Parlement européen et du Conseil du 27 avril 2016 (ci-après, ensemble, la "Règlementation applicable en matière de protection des Données à caractère personnel").
                </p>
                
                <div class="legal-reference">
                    <h4>Références légales :</h4>
                    <ul class="content-text">
                        <li>Loi n°78-17 du 6 janvier 1978 <span class="law-badge">Loi Informatique et Libertés</span></li>
                        <li>Règlement (UE) 2016/679 <span class="law-badge">RGPD</span></li>
                    </ul>
                </div>
                
                <h3 class="section-subtitle"><i class="fas fa-user-shield"></i> Droits des utilisateurs</h3>
                <p class="content-text">
                    En vertu de la Règlementation applicable en matière de protection des Données à caractère personnel, l'Utilisateur dispose d'un droit d'accès, de rectification, de suppression et d'opposition de ses données personnelles.
                </p>
                
                <div class="highlight-box">
                    <p class="content-text">
                        <strong>Exercice de vos droits :</strong> L'Utilisateur peut exercer ce droit par l'un des moyens suivants :
                    </p>
                    <div class="company-info">
                        <div class="company-card">
                            <h4><i class="fas fa-envelope"></i> Par email</h4>
                            <div class="contact-item">
                                <div class="contact-icon"><i class="fas fa-at"></i></div>
                                <div>contacts.aft@gmail.com</div>
                            </div>
                        </div>
                        
                        <div class="company-card">
                            <h4><i class="fas fa-mail-bulk"></i> Par courrier</h4>
                            <div class="contact-item">
                                <div class="contact-icon"><i class="fas fa-map-pin"></i></div>
                                <div>7 Avenue Louis Bleriot<br>93120 La Courneuve</div>
                            </div>
                        </div>
                    </div>
                    <div class="content-text" style="margin-top: 15px;">
                        <strong>Autres moyens :</strong>
                        <ul>
                            <li>Depuis le formulaire de contact du site</li>
                            <li>Depuis votre espace personnel (si connecté)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Propriété intellectuelle -->
            <div class="legal-section">
                <h2 class="section-title">
                    <span class="section-icon"><i class="fas fa-copyright"></i></span>
                    Propriété intellectuelle
                </h2>
                <div class="warning-box">
                    <p class="content-text">
                        <strong>⚠️ Protection des contenus :</strong> Toute utilisation, reproduction, diffusion, commercialisation, modification de toute ou partie du Site, sans autorisation expresse de l'Éditeur est prohibée et pourra entraîner des actions et poursuites judiciaires telles que prévues par la règlementation en vigueur.
                    </p>
                </div>
            </div>

            <!-- Liens utiles -->
            <div class="legal-section" id="liens">
                <h2 class="section-title">
                    <span class="section-icon"><i class="fas fa-link"></i></span>
                    Liens utiles et informations complémentaires
                </h2>
                
                <div class="company-info">
                    <div class="company-card">
                        <h4><i class="fas fa-file-contract"></i> Conditions Générales de Vente</h4>
                        <p class="content-text">
                            Pour plus d'informations, se reporter aux CGV du site https://aft-app.com/ accessibles depuis la rubrique "CGV".
                        </p>
                        <a href="{{route('page.condition')}}" class="action-button" style="margin-top: 10px; padding: 8px 15px; font-size: 14px; width: 100%;">
                            <i class="fas fa-external-link-alt" style="margin-right: 8px;"></i> Voir les CGV
                        </a>
                    </div>
                    
                    <div class="company-card">
                        <h4><i class="fas fa-shield-alt"></i> Protection des données</h4>
                        <p class="content-text">
                            Pour plus d'informations en matière de protection des données à caractère personnel, se reporter à la Charte en matière de protection des données à caractère personnel du site https://aft-app.com/ accessible depuis la rubrique "Données personnelles".
                        </p>
                        <a href="{{route('page.politique')}}" class="action-button" style="margin-top: 10px; padding: 8px 15px; font-size: 14px; width: 100%;">
                            <i class="fas fa-external-link-alt" style="margin-right: 8px;"></i> Voir la politique de confidentialité
                        </a>
                    </div>
                </div>
            </div>

            <!-- Coordonnées AFT -->
            <div class="legal-section">
                <h2 class="section-title">
                    <span class="section-icon"><i class="fas fa-address-card"></i></span>
                    Coordonnées AFT
                </h2>
                <div class="contact-info">
                    <h3 style="color: var(--vert); margin-bottom: 15px; display: flex; align-items: center;">
                        <i class="fas fa-map-marker-alt" style="margin-right: 10px;"></i> AFRIQUE FRET TRANSIT IMPORT-EXPORT (AFT)
                    </h3>
                    
                    <div class="company-info">
                        <div class="company-card" style="background: transparent; box-shadow: none; border: none; padding: 0;">
                            <div class="contact-item">
                                <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                                <div>
                                    <strong>Adresse :</strong><br>
                                    7 Avenue Louis Bleriot<br>
                                    93120 La Courneuve, France
                                </div>
                            </div>
                        </div>
                        
                        <div class="company-card" style="background: transparent; box-shadow: none; border: none; padding: 0;">
                            <div class="contact-item">
                                <div class="contact-icon"><i class="fas fa-phone"></i></div>
                                <div>
                                    <strong>Téléphone :</strong><br>
                                    01 86 78 69 67
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
            <p style="font-size: 14px; opacity: 0.7; margin-top: 10px;">
                Dernière mise à jour : 19 septembre 2025
            </p>
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
        
        // Animation des sections au défilement
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
        
        // Appliquer l'animation à toutes les sections
        document.querySelectorAll('.legal-section').forEach(section => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            section.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(section);
        });
        
        // Navigation dans les liens rapides
        document.querySelectorAll('.quick-link').forEach(link => {
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
            const sections = document.querySelectorAll('.legal-section');
            const scrollPosition = window.scrollY + 150;
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                const sectionId = section.getAttribute('id');
                
                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight && sectionId) {
                    // Mettre à jour les liens rapides
                    document.querySelectorAll('.quick-link').forEach(link => {
                        link.style.background = '';
                        link.style.color = '';
                        link.style.borderColor = '';
                    });
                    
                    const activeLink = document.querySelector(`.quick-link[href="#${sectionId}"]`);
                    if (activeLink) {
                        activeLink.style.background = 'var(--vert)';
                        activeLink.style.color = 'var(--blanc)';
                        activeLink.style.borderColor = 'var(--vert)';
                    }
                }
            });
        });
        
        // Afficher l'année en cours dans le footer
        document.addEventListener('DOMContentLoaded', function() {
            const currentYear = new Date().getFullYear();
            const yearElements = document.querySelectorAll('p:contains("2025")');
            yearElements.forEach(element => {
                if (element.textContent.includes('2025')) {
                    element.textContent = element.textContent.replace('2025', currentYear);
                }
            });
        });
    </script>
</body>
</html>