@extends('admin.layouts.template')
@section('content')
    <div class="container">
        <div class="header">
            <h1>Choisissez le type de programme à planifier</h1>
            <p>Sélectionnez l'option qui correspond à vos besoins</p>
        </div>
        
        <div class="cards-container">
            <div class="card depot">
                <div class="card-icon">
                    📥
                </div>
                <h3>Dépôt</h3>
                <p>Créez un programme de dépôt pour vos produits. Gérez les emplacements, les horaires et les conditions de dépôt.</p>
                <a href="{{route('depot.create')}}" class="card-btn">Sélectionner</a>
            </div>
            
            <div class="card recuperation">
                <div class="card-icon">
                    📤
                </div>
                <h3>Récupération</h3>
                <p>Configurez un système de récupération pour vos clients. Définissez les points de collecte et les créneaux horaires.</p>
                <a href="{{route('recuperation.create')}}" class="card-btn">Sélectionner</a>
            </div>
            
            <div class="card livraison">
                <div class="card-icon">
                    🚚
                </div>
                <h3>Livraison</h3>
                <p>Mettez en place un service de livraison. Gérez les zones de livraison, les transporteurs et les délais.</p>
                <a href="#" class="card-btn">Sélectionner</a>
            </div>
        </div>
    </div>

    <style>
        /* Ajoutez le CSS ici ou dans un fichier séparé */
        :root {
            --primary: #fea219;
            --secondary: #0e914b;
            --white: #ffffff;
            --light-gray: #f5f5f5;
            --dark-gray: #333333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .header h1 {
            color: var(--dark-gray);
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .header p {
            color: var(--dark-gray);
            font-size: 1.1rem;
            opacity: 0.8;
        }
        
        .cards-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            width: 100%;
        }
        
        .card {
            background-color: var(--white);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 300px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background-color: var(--primary);
        }
        
        .card.depot::before {
            background-color: var(--primary);
        }
        
        .card.recuperation::before {
            background-color: var(--secondary);
        }
        
        .card.livraison::before {
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
        }
        
        .card-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background-color: var(--light-gray);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2rem;
            color: var(--dark-gray);
        }
        
        .card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: var(--dark-gray);
        }
        
        .card p {
            color: var(--dark-gray);
            opacity: 0.7;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        
        .card-btn {
            display: inline-block;
            padding: 10px 25px;
            background-color: var(--primary);
            color: var(--white);
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid var(--primary);
        }
        
        .card-btn:hover {
            background-color: transparent;
            color: var(--primary);
        }
        
        .card.recuperation .card-btn {
            background-color: var(--secondary);
            border-color: var(--secondary);
        }
        
        .card.recuperation .card-btn:hover {
            background-color: transparent;
            color: var(--secondary);
        }
        
        .card.livraison .card-btn {
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            position: relative;
            z-index: 1;
        }
        
        .card.livraison .card-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--white);
            border-radius: 50px;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }
        
        .card.livraison .card-btn:hover {
            color: var(--dark-gray);
        }
        
        .card.livraison .card-btn:hover::before {
            opacity: 1;
        }
        
        @media (max-width: 768px) {
            .cards-container {
                flex-direction: column;
                align-items: center;
            }
            
            .card {
                width: 100%;
                max-width: 350px;
            }
        }
    </style>
@endsection