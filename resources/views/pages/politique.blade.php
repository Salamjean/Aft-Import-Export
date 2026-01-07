<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/img/aft.jpg') }}" />
    <title>Politique de Confidentialité - AFT</title>
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
            width: 80%;
            margin: 0 10%;
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
        }

        .last-update strong {
            color: var(--orange);
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

        .content-text ul,
        .content-text ol {
            padding-left: 25px;
            margin: 15px 0;
        }

        .content-text li {
            margin-bottom: 10px;
        }

        .highlight-box {
            background: var(--vert-clair);
            border-left: 4px solid var(--vert);
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

        .contact-icon {
            color: var(--vert);
            margin-right: 15px;
            font-size: 20px;
            min-width: 30px;
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

        .back-button {
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
            margin-top: 30px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .back-button:hover {
            background: var(--vert);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(15, 145, 75, 0.2);
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

            .article-title {
                font-size: 20px;
            }

            .footer-links {
                flex-direction: column;
                gap: 15px;
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
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="header">
        <div class="container">
            <div class="logo-container">
                <img src="{{ asset('images/LOGOAFT.png') }}" alt="AFT Logo" class="logo">
            </div>
            <h1>Politique de Confidentialité</h1>
            <p>Protection de vos données personnelles et respect de votre vie privée</p>
        </div>
    </div>

    <div class="container">
        <div class="content">
            <div class="last-update">
                <strong>📅 Dernière mise à jour :</strong> 17 octobre 2025
            </div>

            <div class="article">
                <h2 class="article-title">
                    <span class="article-number">1</span>
                    PRÉAMBULE
                </h2>
                <p class="content-text">
                    La présente politique de confidentialité a pour but d'informer les utilisateurs du site
                    <a href="https://aft-app.com/"
                        style="color: var(--orange); text-decoration: none;">https://aft-app.com/</a> :
                </p>
                <ul class="content-text">
                    <li>Sur la manière dont sont collectées leurs données personnelles. Sont considérées comme des
                        données personnelles, toute information permettant d'identifier un utilisateur. À ce titre, il
                        peut s'agir : de ses noms et prénoms, de son âge, de son adresse postale ou email, de sa
                        localisation ou encore de son adresse IP (liste non-exhaustive) ;</li>
                    <li>Sur les droits dont ils disposent concernant ces données ;</li>
                    <li>Sur la personne responsable du traitement des données à caractère personnel collectées et
                        traitées ;</li>
                    <li>Sur les destinataires de ces données personnelles ;</li>
                    <li>Sur la politique du site en matière de cookies.</li>
                </ul>
                <p class="content-text">
                    Cette politique complète les mentions légales et les Conditions Générales d'Utilisation consultables
                    par les utilisateurs.
                </p>
            </div>

            <div class="article">
                <h2 class="article-title">
                    <span class="article-number">2</span>
                    PRINCIPES RELATIFS À LA COLLECTE ET AU TRAITEMENT DES DONNÉES PERSONNELLES
                </h2>
                <p class="content-text">
                    Conformément à l'article 5 du Règlement européen 2016/679, les données à caractère personnel sont :
                </p>
                <div class="highlight-box">
                    <ul>
                        <li>Traitées de manière licite, loyale et transparente au regard de la personne concernée ;</li>
                        <li>Collectées pour des finalités déterminées, explicites et légitimes, et ne pas être traitées
                            ultérieurement d'une manière incompatible avec ces finalités ;</li>
                        <li>Adéquates, pertinentes et limitées à ce qui est nécessaire au regard des finalités pour
                            lesquelles elles sont traitées ;</li>
                        <li>Exactes et, si nécessaire, tenues à jour. Toutes les mesures raisonnables doivent être
                            prises pour que les données à caractère personnel qui sont inexactes, eu égard aux finalités
                            pour lesquelles elles sont traitées, soient effacées ou rectifiées sans tarder ;</li>
                        <li>Conservées sous une forme permettant l'identification des personnes concernées pendant une
                            durée n'excédant pas celle nécessaire au regard des finalités pour lesquelles elles sont
                            traitées ;</li>
                        <li>Traitées de façon à garantir une sécurité appropriée des données collectées, y compris la
                            protection contre le traitement non autorisé ou illicite et contre la perte, la destruction
                            ou les dégâts d'origine accidentelle, à l'aide de mesures techniques ou organisationnelles
                            appropriées.</li>
                    </ul>
                </div>
                <p class="content-text">
                    Le traitement n'est licite que si, et dans la mesure où, au moins une des conditions suivantes est
                    remplie :
                </p>
                <ul class="content-text">
                    <li>La personne concernée a consenti au traitement de ses données à caractère personnel pour une ou
                        plusieurs finalités spécifiques ;</li>
                    <li>Le traitement est nécessaire à l'exécution d'un contrat auquel la personne concernée est partie
                        ou à l'exécution de mesures précontractuelles prises à la demande de celle-ci ;</li>
                    <li>Le traitement est nécessaire au respect d'une obligation légale à laquelle le responsable du
                        traitement est soumis ;</li>
                    <li>Le traitement est nécessaire à la sauvegarde des intérêts vitaux de la personne concernée ou
                        d'une autre personne physique ;</li>
                    <li>Le traitement est nécessaire à l'exécution d'une mission d'intérêt public ou relevant de
                        l'exercice de l'autorité publique dont est investi le responsable du traitement ;</li>
                    <li>Le traitement est nécessaire aux fins des intérêts légitimes poursuivis par le responsable du
                        traitement ou par un tiers, à moins que ne prévalent les intérêts ou les libertés et droits
                        fondamentaux de la personne concernée qui exigent une protection des données à caractère
                        personnel, notamment lorsque la personne concernée est un enfant.</li>
                </ul>
            </div>

            <div class="article">
                <h2 class="article-title">
                    <span class="article-number">3</span>
                    DONNÉES À CARACTÈRE PERSONNEL COLLECTÉES ET TRAITÉES
                </h2>

                <h3 class="section-title">Article 3.1 : Données collectées</h3>
                <p class="content-text">
                    Les données personnelles collectées dans le cadre de notre activité sont les suivantes :
                </p>
                <ul class="content-text">
                    <li>Nom et prénom</li>
                    <li>Adresse e-mail</li>
                    <li>Numéro de téléphone</li>
                    <li>Informations liées à votre entreprise ou activité professionnelle (si applicable)</li>
                    <li>Informations de connexion et mot de passe</li>
                </ul>
                <p class="content-text">
                    La collecte et le traitement de ces données répond aux finalités suivantes :
                </p>
                <ul class="content-text">
                    <li>Créer et gérer votre compte utilisateur</li>
                    <li>Communiquer avec vous concernant votre compte ou nos services</li>
                    <li>Améliorer nos services et personnaliser votre expérience</li>
                    <li>Respecter nos obligations légales et réglementaires</li>
                </ul>

                <h3 class="section-title">Article 3.2 : Mode de collecte des données</h3>
                <p class="content-text">
                    Les données susmentionnées sont collectées lorsque l'utilisateur crée un compte client. Nous
                    conservons vos informations personnelles aussi longtemps que nécessaire pour fournir nos services et
                    respecter nos obligations légales.
                </p>
                <p class="content-text">
                    La société est susceptible de conserver certaines données à caractère personnel au-delà des délais
                    annoncés ci-dessus afin de remplir ses obligations légales ou réglementaires.
                </p>

                <h3 class="section-title">Article 3.3 : Hébergement des données</h3>
                <p class="content-text">
                    Le site <a href="https://aft-app.com/"
                        style="color: var(--orange); text-decoration: none;">https://aft-app.com/</a> est hébergé par :
                </p>
                <div class="highlight-box">
                    <strong>Hostinger</strong><br>
                    61 Lordou Vironos Street<br>
                    6023 Larnaca, Chypre
                </div>
            </div>

            <div class="article">
                <h2 class="article-title">
                    <span class="article-number">4</span>
                    RESPONSABLE DU TRAITEMENT DES DONNÉES
                </h2>

                <h3 class="section-title">Article 4.1 : Le responsable du traitement des données</h3>
                <p class="content-text">
                    Les données à caractère personnel sont collectées par AFT, SAS au capital de 3000 euros, dont le
                    numéro d'immatriculation est le 881916365.
                </p>

                <div class="contact-info">
                    <h3 style="color: var(--vert); margin-bottom: 20px;">Contact du responsable des données :</h3>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>7 AVENUE LOUIS BLERIOT, 93120 LA COURNEUVE</div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-phone"></i></div>
                        <div>01 86 78 69 67</div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                        <div>contacts.aft@gamail.com</div>
                    </div>
                </div>

                <h3 class="section-title">Article 4.2 : Le délégué à la protection des données</h3>
                <p class="content-text">
                    Le délégué à la protection des données de l'entreprise ou du responsable est : Adama Sylla, 7 Avenue
                    Louis Bleriot 93120 La Courneuve, 01 86 78 69 67, contacts.aft@gamail.com
                </p>
                <p class="content-text">
                    Si vous estimez, après nous avoir contactés, que vos droits "Informatique et Libertés", ne sont pas
                    respectés, vous pouvez adresser une information à la CNIL.
                </p>
            </div>

            <div class="article">
                <h2 class="article-title">
                    <span class="article-number">5</span>
                    LES DROITS DE L'UTILISATEUR
                </h2>
                <p class="content-text">
                    Tout utilisateur concerné par le traitement de ses données personnelles peut se prévaloir des droits
                    suivants, en application du règlement européen 2016/679 et de la Loi Informatique et Liberté (Loi
                    78-17 du 6 janvier 1978) :
                </p>
                <div class="highlight-box">
                    <ul>
                        <li>Droit d'accès, de rectification et droit à l'effacement des données ;</li>
                        <li>Droit à la portabilité des données ;</li>
                        <li>Droit à la limitation et à l'opposition du traitement des données ;</li>
                        <li>Droit de ne pas faire l'objet d'une décision fondée exclusivement sur un procédé automatique
                            ;</li>
                        <li>Droit de déterminer le sort des données après la mort ;</li>
                        <li>Droit de saisir l'autorité de contrôle compétente.</li>
                    </ul>
                </div>
                <p class="content-text">
                    Pour exercer vos droits, veuillez adresser votre courrier à AFT 7 Avenue Louis Bleriot 93120 La
                    Courneuve ou par mail à contacts.aft@gamail.com.
                </p>
                <p class="content-text">
                    Afin que le responsable du traitement des données puisse faire droit à sa demande, l'utilisateur
                    peut être tenu de lui communiquer certaines informations telles que : ses noms et prénoms, son
                    adresse e-mail ainsi que son numéro de compte, d'espace personnel ou d'abonné.
                </p>
                <p class="content-text">
                    Consultez le site <a href="https://www.cnil.fr"
                        style="color: var(--orange); text-decoration: none;">cnil.fr</a> pour plus d'informations sur
                    vos droits.
                </p>
            </div>

            <div class="article">
                <h2 class="article-title">
                    <span class="article-number">6</span>
                    MODIFICATION DE LA POLITIQUE DE CONFIDENTIALITÉ
                </h2>
                <p class="content-text">
                    L'éditeur du site AFT se réserve le droit de pouvoir modifier la présente Politique à tout moment
                    afin d'assurer aux utilisateurs du site sa conformité avec le droit en vigueur.
                </p>
                <p class="content-text">
                    Les éventuelles modifications ne sauraient avoir d'incidence sur les achats antérieurement effectués
                    sur le site, lesquels restent soumis à la Politique en vigueur au moment de l'achat et telle
                    qu'acceptée par l'utilisateur lors de la validation de l'achat.
                </p>
                <p class="content-text">
                    L'utilisateur est invité à prendre connaissance de cette Politique à chaque fois qu'il utilise nos
                    services, sans qu'il soit nécessaire de l'en prévenir formellement.
                </p>
                <p class="content-text">
                    La présente politique, éditée le 17/10/2025, a été mise à jour le 17/10/2025.
                </p>
            </div>

            <div style="text-align: center; margin-top: 40px;">
                <a href="{{route('user.register')}}">
                    <button class="back-button">
                        <i class="fas fa-arrow-left" style="margin-right: 10px;"></i> Retour
                    </button>
                </a>
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
                <img src="{{ asset('images/LOGOAFT.png') }}" alt="AFT Logo"
                    style="height: 50px; filter: brightness(0) invert(1); opacity: 0.8;">
            </div>
            <div class="footer-links">
                <a href="/">Accueil</a>
                <a href="{{ route('login') }}">Connexion</a>
                <a href="{{ route('user.register') }}">Inscription</a>
                <a href="{{route('page.condition')}}">Condition générale de vente</a>
                <a href="{{route('page.legal')}}">Mentions légales</a>
            </div>
            <p>© {{ date('Y') }} AFT. Tous droits réservés.</p>
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
        document.querySelectorAll('.article').forEach(article => {
            article.style.opacity = '0';
            article.style.transform = 'translateY(20px)';
            article.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(article);
        });

        // Impression
        function printPage() {
            window.print();
        }

        // Ajouter des ancres pour une navigation facile
        document.querySelectorAll('.article-title').forEach(title => {
            title.style.cursor = 'pointer';
            title.addEventListener('click', function() {
                const articleNumber = this.querySelector('.article-number').textContent;
                alert(`Article ${articleNumber} - Vous pouvez utiliser cette section comme référence`);
            });
        });
    </script>
</body>

</html>
