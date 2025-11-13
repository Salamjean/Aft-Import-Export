<?php

use App\Http\Controllers\Admin\AdminDashboard;
use App\Http\Controllers\Admin\Agence\AgenceController;
use App\Http\Controllers\Admin\AuthenticateAdmin;
use App\Http\Controllers\Admin\Bateau\BateauController;
use App\Http\Controllers\Admin\Chauffeur\ChauffeurController;
use App\Http\Controllers\Admin\Client\ClientController;
use App\Http\Controllers\Admin\Client\EmailController;
use App\Http\Controllers\Agent\Colis\AgentEtiquetteController;
use App\Http\Controllers\Admin\Colis\ColisController;
use App\Http\Controllers\Admin\Colis\EtiquetteController;
use App\Http\Controllers\Admin\Colis\ProduitController;
use App\Http\Controllers\Admin\Colis\ServiceController;
use App\Http\Controllers\Admin\Conteneur\ConteneurController;
use App\Http\Controllers\Admin\Devis\AdminDevisController;
use App\Http\Controllers\Admin\Programme\DepotController;
use App\Http\Controllers\Admin\Programme\ProgrammeController;
use App\Http\Controllers\Admin\Programme\RecuperationController;
use App\Http\Controllers\Admin\Scan\ChargerController;
use App\Http\Controllers\Admin\Scan\DechargerController;
use App\Http\Controllers\Admin\Scan\ScannerController;
use App\Http\Controllers\Agent\AgentController;
use App\Http\Controllers\Agent\AgentDashboard;
use App\Http\Controllers\Agent\AuthenticateAgent;
use App\Http\Controllers\Agent\Bateau\AgentBateauController;
use App\Http\Controllers\Agent\Chauffeur\AgentChauffeurController;
use App\Http\Controllers\Agent\Client\AgentClientController;
use App\Http\Controllers\Agent\Colis\AgentColisController;
use App\Http\Controllers\Agent\Conteneur\AgentConteneurController;
use App\Http\Controllers\Agent\Cote_Ivoire\AgentCoteDashboard;
use App\Http\Controllers\Agent\Cote_Ivoire\IvoireChauffeurController;
use App\Http\Controllers\Agent\Cote_Ivoire\IvoireController;
use App\Http\Controllers\Agent\Cote_Ivoire\IvoireScanController;
use App\Http\Controllers\Agent\Cote_Ivoire\IvoireScanDechargerController;
use App\Http\Controllers\Agent\Cote_Ivoire\IvoireScanLivrerController;
use App\Http\Controllers\Agent\Cote_Ivoire\LivraisonController;
use App\Http\Controllers\Agent\Devis\AgentDevisController;
use App\Http\Controllers\Agent\Programme\AgentDepotController;
use App\Http\Controllers\Agent\Programme\AgentProgrammeController;
use App\Http\Controllers\Agent\Programme\AgentRecuperationController;
use App\Http\Controllers\Agent\Scan\AgentChargerController;
use App\Http\Controllers\Agent\Scan\AgentDechargerController;
use App\Http\Controllers\Agent\Scan\AgentScanController;
use App\Http\Controllers\Chauffeur\AuthenticateChauffeur;
use App\Http\Controllers\Chauffeur\ChauffeurDashboard;
use App\Http\Controllers\Chauffeur\ChauffeurProgrammeController;
use App\Http\Controllers\Chauffeur\ChauffeurScanLivraison;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\User\Colis\UserColisController;
use App\Http\Controllers\User\Contact\ContactController;
use App\Http\Controllers\User\Devis\DevisController;
use App\Http\Controllers\User\UserAuthenticate;
use App\Http\Controllers\User\UserController;
use App\Models\Colis;
use Illuminate\Support\Facades\Route;
//Les routes des pages du site
Route::prefix('/')->group(function(){
    Route::get('/',[HomeController::class,'home'])->name('page.home');
    Route::get('/about',[HomeController::class,'about'])->name('page.about');
    Route::get('/services',[HomeController::class,'services'])->name('page.services');
    Route::get('/agency',[HomeController::class,'agence'])->name('page.agence');
    Route::get('/contact',[HomeController::class,'contact'])->name('page.contact');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
});

//Les routes pour un utilisateurs @users

//Les routes de gestion du @admin
Route::prefix('admin')->group(function () {
    Route::get('/', [AuthenticateAdmin::class, 'login'])->name('admin.login');
    Route::post('/', [AuthenticateAdmin::class, 'handleLogin'])->name('admin.handleLogin');
});

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/logout', [AdminDashboard::class, 'logout'])->name('admin.logout');

        //les routes de gestion des agences par l'admin 
        Route::prefix('agence')->group(function(){
            Route::get('/index',[AgenceController::class,'index'])->name('agence.index');
            Route::get('/create',[AgenceController::class,'create'])->name('agence.create');
            Route::post('/create',[AgenceController::class,'store'])->name('agence.store');
            Route::get('/{agence}/edit', [AgenceController::class, 'edit'])->name('agence.edit');
            Route::put('/{agence}', [AgenceController::class, 'update'])->name('agence.update');
            Route::delete('/{agence}', [AgenceController::class, 'destroy'])->name('agence.destroy');
        });

        //Les routes de gestion des agents 
        Route::prefix('agent')->group(function(){
            Route::get('/agentadd', [AgentController::class, 'create'])->name('agent.create');
            Route::post('/create', [AgentController::class, 'store'])->name('agent.store');
            Route::get('/edit/{id}', [AgentController::class, 'edit'])->name('agent.edit'); // Modifié
            Route::put('/update/{id}', [AgentController::class, 'update'])->name('agent.update'); // Modifié
            Route::delete('delete/{id}', [AgentController::class, 'destroy'])->name('agent.destroy');
        });

        //Les routes de gestion des devis 
        Route::prefix('quote')->group(function(){
            Route::get('/warning',[AdminDevisController::class,'list'])->name('admin.devis.list.attente');
            Route::get('/confirmed',[AdminDevisController::class,'confirmed'])->name('admin.devis.list.confirmed');
            Route::get('/{devis}/details', [AdminDevisController::class, 'getDevisDetails'])->name('admin.devis.details');
            Route::post('/{devis}/valider', [AdminDevisController::class, 'validerDevis'])->name('admin.devis.valider');
        });

        //Les routes pour la gestion d'un conteneur
        Route::prefix('container')->group(function(){
            Route::get('/conteneurindex',[ConteneurController::class,'index'])->name('conteneur.index');
            Route::get('/conteneurhistory',[ConteneurController::class,'history'])->name('conteneur.history');
            Route::get('/conteneur/{conteneur}/colis', [ConteneurController::class, 'showColis'])->name('conteneur.colis.show');
            Route::get('/{colis}/details', [ConteneurController::class, 'getColisDetails'])->name('colis.details');
            Route::get('/conteneuradd',[ConteneurController::class,'create'])->name('conteneur.create');
            Route::post('/create',[ConteneurController::class,'store'])->name('conteneur.store');
            Route::get('/conteneur/{id}/edit', [ConteneurController::class, 'edit'])->name('conteneur.edit');
            Route::put('/conteneur/{id}', [ConteneurController::class, 'update'])->name('conteneur.update');
            Route::delete('/conteneur/{id}', [ConteneurController::class, 'destroy'])->name('conteneur.destroy');
            Route::get('/{id}/details', [ConteneurController::class, 'getDetails'])->name('conteneur.details');
            Route::get('/conteneur/{conteneurId}/pdf', [ConteneurController::class, 'downloadConteneurPDF'])->name('conteneur.pdf');
        });

        //Les routes de gestion des colis 
        Route::prefix('parcel')->group(function(){
            Route::get('/parcelindex',[ColisController::class,'index'])->name('colis.index');
            Route::get('/parcelhistory',[ColisController::class,'history'])->name('colis.history');
            Route::get('/parceladd',[ColisController::class,'create'])->name('colis.create');
            Route::post('/create',[ColisController::class,'store'])->name('colis.store');
            Route::get('/{id}/edit', [ColisController::class, 'edit'])->name('colis.edit');
            Route::put('/{id}', [ColisController::class, 'update'])->name('colis.update');
            Route::delete('/{id}', [ColisController::class, 'destroy'])->name('colis.destroy');
            Route::get('/{colis}', [ColisController::class, 'show'])->name('colis.show');
            Route::post('/{colis}/paiement', [ColisController::class, 'enregistrerPaiement'])->name('colis.paiement');
            // Routes séparées pour chaque document
            Route::get('/export/pdf', [EtiquetteController::class, 'exportPDF'])->name('colis.export.pdf');
            Route::get('/{id}/etiquettes', [EtiquetteController::class, 'genererEtiquettes'])->name('colis.etiquettes');
            Route::get('/{id}/facture', [EtiquetteController::class, 'generateFacture'])->name('colis.facture');
            Route::get('/{id}/bon-livraison', [EtiquetteController::class, 'generateBonLivraison'])->name('colis.bon-livraison');
        });

        Route::post('/scan/scan-qr', [ScannerController::class, 'scanQRCode'])->name('scan.qr');
        Route::get('/scan/entrepot', [ScannerController::class, 'entrepot'])->name('scan.entrepot');
        Route::get('/scan/charged', [ChargerController::class, 'charge'])->name('scan.charge');
        Route::post('/scan/scan-qr-charge', [ChargerController::class, 'scanQRCodeCharge'])->name('scan.qr.charge');
        Route::get('/scan/colis/{id}', [ScannerController::class, 'getColisDetails'])->name('scan.colis.details');
        Route::get('/scan/dechargement', [DechargerController::class, 'decharge'])->name('scan.decharge');
        Route::post('/scan/scan-qr-decharge', [DechargerController::class, 'scanQRCodeDecharge'])->name('scan.qr.decharge');

        // Routes pour fermer/ouvrir les conteneurs
        Route::post('/conteneur/{id}/close', [ConteneurController::class, 'close'])->name('conteneur.close');
        Route::post('/conteneur/{id}/open', [ConteneurController::class, 'open'])->name('conteneur.open');

         //Les routes de gestion des mode de transit par l'admin
         Route::prefix('bateaux')->group(function(){
            Route::get('/planifier',[BateauController::class,'planifier'])->name('bateau.planifier');
            Route::get('/indexPlan',[BateauController::class,'index'])->name('bateau.index');
            Route::get('/{id}/conteneur', [BateauController::class, 'showConteneur'])->name('admin.bateau.conteneur');
            Route::delete('/{id}', [BateauController::class, 'destroy'])->name('admin.bateau.destroy');
            Route::post('/admin/bateaux/store', [BateauController::class, 'store'])->name('admin.bateau.store');
            Route::get('/{type}', [BateauController::class, 'getConteneursByType']);
         });

         //Les routes pour l'enregistrement des chauffeurs par l'admin 
         Route::prefix('driver')->group(function(){
            // Routes principales
            Route::get('chauffeurs', [ChauffeurController::class, 'index'])->name('chauffeur.index');
            Route::get('chauffeurs/createDriver', [ChauffeurController::class, 'create'])->name('chauffeur.create');
            Route::post('chauffeurs', [ChauffeurController::class, 'store'])->name('chauffeur.store');
            Route::get('chauffeurs/{chauffeur}', [ChauffeurController::class, 'show'])->name('chauffeur.show');
            Route::get('chauffeurs/{chauffeur}/edit', [ChauffeurController::class, 'edit'])->name('chauffeur.edit');
            Route::put('chauffeurs/{chauffeur}', [ChauffeurController::class, 'update'])->name('chauffeur.update');
            Route::delete('chauffeurs/{chauffeur}', [ChauffeurController::class, 'destroy'])->name('chauffeur.destroy');
         });

         //Les routes pour planifier un depot ou enlevement 
        Route::prefix('schedule')->group(function(){
            Route::get('/type',[ProgrammeController::class,'type'])->name('programme.type');
            Route::get('/listType',[ProgrammeController::class,'list'])->name('programme.list');

            //Les routes de depot 
            Route::get('/indexDepot',[DepotController::class,'index'])->name('depot.index');
            Route::get('/deposit',[DepotController::class,'depot'])->name('depot.create');
            Route::post('/deposit',[DepotController::class,'store'])->name('depot.store');
            Route::get('/{depot}/details', [DepotController::class, 'details'])->name('details');
            Route::get('/{depot}/edit', [DepotController::class, 'edit'])->name('edit');

            // Routes pour les étiquettes
            Route::get('/{depot}/etiquettes', [DepotController::class, 'etiquettes'])->name('etiquettes');
            Route::get('/{depot}/download-etiquettes', [DepotController::class, 'downloadEtiquettes'])->name('download-etiquettes');
            
             // Routes supplémentaires pour la gestion complète
            Route::put('/{depot}', [DepotController::class, 'update'])->name('update');
            Route::delete('/{depot}', [DepotController::class, 'destroy'])->name('destroy');
            Route::patch('/{depot}/statut', [DepotController::class, 'updateStatut'])->name('update-statut');

              // Récupération
            Route::get('/indexRecuperation', [RecuperationController::class, 'index'])->name('recuperation.index');
            Route::get('/recuperation', [RecuperationController::class, 'create'])->name('recuperation.create');
            Route::post('/recuperation', [RecuperationController::class, 'store'])->name('recuperation.store');
            Route::get('/recuperation/{id}/details', [RecuperationController::class, 'details'])->name('recuperation.details');
            Route::get('/recuperation/{id}/download-etiquettes', [RecuperationController::class, 'downloadEtiquettes'])->name('recuperation.download-etiquettes');
            Route::delete('/recuperation/{id}', [RecuperationController::class, 'destroy'])->name('recuperation.destroy');
    });

    //Les routes pour voir les clients et prospects
    Route::prefix('client')->group(function(){
        Route::get('/AllUsers',[ClientController::class,'client'])->name('client.all');
        Route::get('/AllProspects',[ClientController::class,'prospect'])->name('prospect.all');
    });
});

Route::get('/colis/get-conteneur-reference', [ColisController::class, 'getConteneurAndReference'])->name('colis.get-conteneur-reference');

// Routes pour les produits
Route::prefix('admin/produits')->group(function () {
    Route::post('/', [ProduitController::class, 'store'])->name('admin.produits.store');
});

// Routes pour les services
Route::prefix('admin/services')->group(function () {
    Route::post('/', [ServiceController::class, 'store'])->name('admin.services.store');
    Route::get('/search', [ServiceController::class, 'search'])->name('admin.services.search');
});

//Les routes de gestion des @agents
Route::prefix('agent')->group(function() {
    Route::get('/', [AuthenticateAgent::class, 'login'])->name('agent.login');
    Route::post('/', [AuthenticateAgent::class, 'handleLogin'])->name('agent.handleLogin');
});
Route::middleware('agent')->prefix('agent')->group(function(){
    Route::get('/dashboard', [AgentDashboard::class, 'dashboard'])->name('agent.dashboard');
    Route::get('/logout', [AgentDashboard::class, 'logout'])->name('agent.logout');

    //Les routes pour la gestions des bateaux 
    Route::prefix('ivory')->group(function(){
        Route::get('/parcelindex',[AgentCoteDashboard::class,'colis'])->name('agent.cote.colis.index');
        Route::get('/dashboard', [AgentCoteDashboard::class, 'dashboard'])->name('agent.cote.dashboard');
        Route::get('/indexPlan',[AgentCoteDashboard::class,'index'])->name('agent.cote.bateau.index');
        Route::get('/history',[AgentCoteDashboard::class,'history'])->name('agent.cote.conteneur.history');
        Route::get('/{conteneur}/colis', [AgentCoteDashboard::class, 'showColis'])->name('agent.cote.colis.show');
        Route::get('/{id}/conteneur', [AgentCoteDashboard::class, 'showConteneur'])->name('agent.cote.bateau.conteneur');
        Route::get('/conteneur/{conteneurId}/pdf', [IvoireController::class, 'downloadConteneurPDF'])->name('agent.conteneur.pdf');

        Route::put('/{id}/mark-arrived', [IvoireController::class, 'markAsArrived'])->name('agent.bateaux.mark-arrived');
        Route::get('/conteneur/{conteneur}/colis', [IvoireController::class, 'showColis'])->name('agent.conteneur.cote.colis.show');

        //Les scans de cote d'ivoire 
        Route::get('/scan/colis/{id}', [AgentScanController::class, 'getColisDetails'])->name('ivoire.scan.colis.details');
        Route::get('/scan/dechargement', [IvoireScanDechargerController::class, 'decharge'])->name('ivoire.scan.decharge');
        Route::post('/scan-qr-decharge', [IvoireScanDechargerController::class, 'scanQRCodeDecharge'])->name('ivoire.scan.qr.decharge');
        Route::get('/scan/livraison', [IvoireScanLivrerController::class, 'livrer'])->name('ivoire.scan.livrer');
        Route::post('/scan/scan-qr-livrer', [IvoireScanLivrerController::class, 'scanQRCodeLivrer'])->name('ivoire.scan.qr.livrer');

        Route::prefix('driver')->group(function(){
            // Routes principales
            Route::get('chauffeurs', [IvoireChauffeurController::class, 'index'])->name('ivoire.chauffeur.index');
            Route::get('chauffeurs/createDriver', [IvoireChauffeurController::class, 'create'])->name('ivoire.chauffeur.create');
            Route::post('chauffeurs', [IvoireChauffeurController::class, 'store'])->name('ivoire.chauffeur.store');
            Route::get('chauffeurs/{chauffeur}', [IvoireChauffeurController::class, 'show'])->name('ivoire.chauffeur.show');
            Route::get('chauffeurs/{chauffeur}/edit', [IvoireChauffeurController::class, 'edit'])->name('ivoire.chauffeur.edit');
            Route::put('chauffeurs/{chauffeur}', [IvoireChauffeurController::class, 'update'])->name('ivoire.chauffeur.update');
            Route::delete('chauffeurs/{chauffeur}', [IvoireChauffeurController::class, 'destroy'])->name('ivoire.chauffeur.destroy');
        });

        //Les routes de programmes de livraison par les agences de cote d'ivoire 
        Route::prefix('delivery')->group(function(){
            Route::get('/createDelivery',[LivraisonController::class,'create'])->name('livraison.create');
            Route::get('/indexDelivery',[LivraisonController::class,'index'])->name('livraison.index');
            Route::post('/createDelivery',[LivraisonController::class,'store'])->name('livraison.store');
            Route::get('/{id}/details', [LivraisonController::class, 'details'])->name('livraison.details');
            Route::delete('/{id}', [LivraisonController::class, 'destroy'])->name('livraison.destroy');
        });
    });
    
    //Les routes de gestion des devis 
    Route::prefix('quote')->group(function(){
        Route::get('/warning',[AgentDevisController::class,'list'])->name('agent.devis.list.attente');
        Route::get('/confirmed',[AgentDevisController::class,'confirmed'])->name('agent.devis.list.confirmed');
        Route::get('/{devis}/details', [AgentDevisController::class, 'getDevisDetails'])->name('agent.devis.details');
        Route::post('/{devis}/valider', [AgentDevisController::class, 'validerDevis'])->name('agent.devis.valider');
    });

     //Les routes pour la gestion d'un conteneur
    Route::prefix('container')->group(function(){
        Route::get('/conteneurindex',[AgentConteneurController::class,'index'])->name('agent.conteneur.index');
        Route::get('/conteneurhistory',[AgentConteneurController::class,'history'])->name('agent.conteneur.history');
        Route::get('/conteneur/{conteneur}/colis', [AgentConteneurController::class, 'showColis'])->name('agent.conteneur.colis.show');
        Route::get('/conteneur/{conteneurId}/pdf', [AgentConteneurController::class, 'downloadConteneurPDF'])->name('agent.conteneur.pdf');
        Route::get('/{colis}/details', [AgentConteneurController::class, 'getColisDetails'])->name('agent.colis.details');
        Route::get('/conteneuradd',[AgentConteneurController::class,'create'])->name('agent.conteneur.create');
        Route::post('/create',[AgentConteneurController::class,'store'])->name('agent.conteneur.store');
        Route::get('/conteneur/{id}/edit', [AgentConteneurController::class, 'edit'])->name('agent.conteneur.edit');
        Route::put('/conteneur/{id}', [AgentConteneurController::class, 'update'])->name('agent.conteneur.update');
        Route::delete('/conteneur/{id}', [AgentConteneurController::class, 'destroy'])->name('agent.conteneur.destroy');
        Route::get('/{id}/details', [AgentConteneurController::class, 'getDetails'])->name('agent.conteneur.details');
    });
    // Routes pour fermer/ouvrir les conteneurs
    Route::post('/conteneur/{id}/close', [AgentConteneurController::class, 'close'])->name('agent.conteneur.close');
    Route::post('/conteneur/{id}/open', [AgentConteneurController::class, 'open'])->name('agent.conteneur.open');

    //Les routes de gestion des colis 
    Route::prefix('parcel')->group(function(){
        Route::get('/parcelindex',[AgentColisController::class,'index'])->name('agent.colis.index');
        Route::get('/parcelhistory',[AgentColisController::class,'history'])->name('agent.colis.history');
        Route::get('/parceladd',[AgentColisController::class,'create'])->name('agent.colis.create');
        Route::post('/create',[AgentColisController::class,'store'])->name('agent.colis.store');
        Route::get('/{id}/edit', [AgentColisController::class, 'edit'])->name('agent.colis.edit');
        Route::put('/{id}', [AgentColisController::class, 'update'])->name('agent.colis.update');
        Route::delete('/{id}', [AgentColisController::class, 'destroy'])->name('agent.colis.destroy');
        Route::get('/{colis}', [AgentColisController::class, 'show'])->name('agent.colis.show');
        Route::post('/{colis}/paiement', [AgentColisController::class, 'enregistrerPaiement'])->name('agent.colis.paiement');
        // Routes séparées pour chaque document
        Route::get('/export/pdf', [AgentEtiquetteController::class, 'exportPDF'])->name('agent.colis.export.pdf');
        Route::get('/{id}/etiquettes', [AgentEtiquetteController::class, 'genererEtiquettes'])->name('agent.colis.etiquettes');
        Route::get('/{id}/facture', [AgentEtiquetteController::class, 'generateFacture'])->name('agent.colis.facture');
        Route::get('/{id}/bon-livraison', [AgentEtiquetteController::class, 'generateBonLivraison'])->name('agent.colis.bon-livraison');
    });
    Route::get('/colis/get-conteneur-reference', [AgentColisController::class, 'getConteneurAndReference'])->name('agent.colis.get-conteneur-reference');

    Route::post('/scan/scan-qr', [AgentScanController::class, 'scanQRCode'])->name('agent.scan.qr');
    Route::get('/scan/entrepot', [AgentScanController::class, 'entrepot'])->name('agent.scan.entrepot');
    Route::get('/scan/charged', [AgentChargerController::class, 'charge'])->name('agent.scan.charge');
    Route::post('/scan/scan-qr-charge', [AgentChargerController::class, 'scanQRCodeCharge'])->name('agent.scan.qr.charge');
    Route::get('/scan/colis/{id}', [AgentScanController::class, 'getColisDetails'])->name('agent.scan.colis.details');
    Route::get('/scan/dechargement', [AgentDechargerController::class, 'decharge'])->name('agent.scan.decharge');
    Route::post('/scan/scan-qr-decharge', [AgentDechargerController::class, 'scanQRCodeDecharge'])->name('agent.scan.qr.decharge');

    //Les routes de gestion des mode de transit par l'agent
    Route::prefix('bateaux')->group(function(){
        Route::get('/planifier',[AgentBateauController::class,'planifier'])->name('agent.bateau.planifier');
        Route::get('/indexPlan',[AgentBateauController::class,'index'])->name('agent.bateau.index');
        Route::get('/{id}/conteneur', [AgentBateauController::class, 'showConteneur'])->name('agent.bateau.conteneur');
        Route::delete('/{id}', [AgentBateauController::class, 'destroy'])->name('agent.bateau.destroy');
        Route::post('/admin/bateaux/store', [AgentBateauController::class, 'store'])->name('agent.bateau.store');
        Route::get('/{type}', [AgentBateauController::class, 'getConteneursByType']);
    });

    //Les routes pour l'enregistrement des chauffeurs par l'admin 
    Route::prefix('driver')->group(function(){
        // Routes principales
        Route::get('chauffeurs', [AgentChauffeurController::class, 'index'])->name('agent.chauffeur.index');
        Route::get('chauffeurs/createDriver', [AgentChauffeurController::class, 'create'])->name('agent.chauffeur.create');
        Route::post('chauffeurs', [AgentChauffeurController::class, 'store'])->name('agent.chauffeur.store');
        Route::get('chauffeurs/{chauffeur}', [AgentChauffeurController::class, 'show'])->name('agent.chauffeur.show');
        Route::get('chauffeurs/{chauffeur}/edit', [AgentChauffeurController::class, 'edit'])->name('agent.chauffeur.edit');
        Route::put('chauffeurs/{chauffeur}', [AgentChauffeurController::class, 'update'])->name('agent.chauffeur.update');
        Route::delete('chauffeurs/{chauffeur}', [AgentChauffeurController::class, 'destroy'])->name('agent.chauffeur.destroy');
    });

     //Les routes pour planifier un depot ou enlevement 
        Route::prefix('schedule')->group(function(){
            Route::get('/type',[AgentProgrammeController::class,'type'])->name('agent.programme.type');
            Route::get('/listType',[AgentProgrammeController::class,'list'])->name('agent.programme.list');

            //Les routes de depot 
            Route::get('/indexDepot',[AgentDepotController::class,'index'])->name('agent.depot.index');
            Route::get('/deposit',[AgentDepotController::class,'depot'])->name('agent.depot.create');
            Route::post('/deposit',[AgentDepotController::class,'store'])->name('agent.depot.store');
            Route::get('/{depot}/details', [AgentDepotController::class, 'details'])->name('agent.details');
            Route::get('/{depot}/edit', [AgentDepotController::class, 'edit'])->name('agent.edit');

            // Routes pour les étiquettes
            Route::get('/{depot}/etiquettes', [AgentDepotController::class, 'etiquettes'])->name('agent.etiquettes');
            Route::get('/{depot}/download-etiquettes', [AgentDepotController::class, 'downloadEtiquettes'])->name('agent.download-etiquettes');
            
             // Routes supplémentaires pour la gestion complète
            Route::put('/{depot}', [AgentDepotController::class, 'update'])->name('agent.update');
            Route::delete('/{depot}', [AgentDepotController::class, 'destroy'])->name('agent.destroy');
            Route::patch('/{depot}/statut', [AgentDepotController::class, 'updateStatut'])->name('agent.update-statut');

            // Récupération
            Route::get('/indexRecuperation', [AgentRecuperationController::class, 'index'])->name('agent.recuperation.index');
            Route::get('/recuperation', [AgentRecuperationController::class, 'create'])->name('agent.recuperation.create');
            Route::post('/recuperation', [AgentRecuperationController::class, 'store'])->name('agent.recuperation.store');
            Route::get('/recuperation/{id}/details', [AgentRecuperationController::class, 'details'])->name('agent.recuperation.details');
            Route::get('/recuperation/{id}/download-etiquettes', [AgentRecuperationController::class, 'downloadEtiquettes'])->name('agent.recuperation.download-etiquettes');
            Route::delete('/recuperation/{id}', [AgentRecuperationController::class, 'destroy'])->name('agent.recuperation.destroy');
    });

    //Les routes pour voir les clients et prospects
    Route::prefix('client')->group(function(){
        Route::get('/AllUsers',[AgentClientController::class,'client'])->name('agent.client.all');
        Route::get('/AllProspects',[AgentClientController::class,'prospect'])->name('agent.prospect.all');
    });
});

//Les routes de gestion des @chauffeur
Route::prefix('chauffeur')->group(function() {
    Route::get('/', [AuthenticateChauffeur::class, 'login'])->name('chauffeur.login');
    Route::post('/', [AuthenticateChauffeur::class, 'handleLogin'])->name('chauffeur.handleLogin');
});
Route::middleware('chauffeur')->prefix('driver')->group(function(){
    Route::get('/dashboard', [ChauffeurDashboard::class, 'dashboard'])->name('chauffeur.dashboard');
    Route::get('/logout', [ChauffeurDashboard::class, 'logout'])->name('chauffeur.logout');
    Route::post('/depot/{id}/terminer', [ChauffeurDashboard::class, 'terminerDepot'])->name('chauffeur.depot.terminer');

    //Les routes des programmes du chauffeur
    Route::prefix('planing')->group(function(){
        Route::get('/all',[ChauffeurProgrammeController::class,'index'])->name('chauffeur.programme');
        Route::get('/history',[ChauffeurProgrammeController::class,'history'])->name('chauffeur.history');
        Route::get('/{type}/{id}/details', [ChauffeurProgrammeController::class, 'showDetails'])->name('chauffeur.programmes.details');
        Route::get('/{type}/{id}/download-etiquettes', [ChauffeurProgrammeController::class, 'downloadEtiquettes'])->name('chauffeur.programmes.download-etiquettes');
    });
});

//Les routes de gestion @users 
Route::prefix('user')->group(function(){
    Route::get('/login',[UserAuthenticate::class,'login'])->name('login');
    Route::post('/login',[UserAuthenticate::class,'handleLogin'])->name('user.handleLogin');
    Route::get('/register',[UserAuthenticate::class,'register'])->name('user.register');
    Route::post('/register',[UserAuthenticate::class,'handleRegister'])->name('user.handleRegister');
});
Route::post('/scan/livrer', [ChauffeurScanLivraison::class, 'scanLivraison'])->name('chauffeur.scan.livrer');
Route::middleware('auth')->prefix('user')->group(function(){
     Route::get('/dashboard',[UserController::class,'dashboard'])->name('user.dashboard');
     Route::get('/logout', [UserController::class, 'logout'])->name('user.logout');

     //les routes pour faire les demandes de devis
    Route::prefix('quote')->group(function(){
        Route::get('/warning', [DevisController::class, 'enAttente'])->name('user.devis.attente');
        Route::get('/confirmed',[DevisController::class,'confirmed'])->name('user.devis.confirmed');
        Route::get('/create',[DevisController::class,'create'])->name('user.devis.create');
        Route::post('/create', [DevisController::class, 'store'])->name('user.devis.store');
        Route::delete('/devis/{devis}/annuler', [DevisController::class, 'annuler'])->name('user.devis.annuler');
        Route::get('/{devis}/details', [DevisController::class, 'getDevisDetails'])->name('user.devis.details');
        Route::post('/{devis}/accepter', [DevisController::class, 'accepter'])->name('user.devis.accepter');
    });

    //les routes pour les suivies des colis 
    Route::prefix('parcel')->group(function(){
        Route::get('/parcelindex',[UserColisController::class,'index'])->name('user.colis.index');
        Route::get('/{colis}', [UserColisController::class, 'show'])->name('user.colis.show');
        Route::get('/{id}/facture', [UserColisController::class, 'generateFacture'])->name('agent.colis.facture');
    });
});

Route::get('users/search', [ColisController::class, 'searchUsers'])->name('admin.users.search');
Route::get('produits/search', [ProduitController::class, 'search'])->name('admin.produits.search');
Route::get('/admin/depots/search', [ProgrammeController::class, 'search'])->name('depots.search');
Route::get('/admin/depots/{id}/details', [ProgrammeController::class, 'getDetails'])->name('depots.details');
Route::post('/admin/preview-recipients', [EmailController::class, 'previewRecipients'])->name('admin.preview-recipients');
Route::post('/admin/send-individual-email', [EmailController::class, 'sendIndividualEmail'])->name('admin.send-individual-email');
Route::post('/admin/send-group-email', [EmailController::class, 'sendGroupEmail'])->name('admin.send-group-email');
Route::post('/admin/send-prospect-group-email', [EmailController::class, 'sendProspectGroupEmail'])->name('admin.send-prospect-group-email');
Route::post('/admin/send-prospect-individual-email', [EmailController::class, 'sendProspectIndividualEmail'])->name('admin.send-prospect-individual-email');
Route::get('/api/track-colis/{reference}', [TrackingController::class, 'trackColis'])->name('recherche.colis');

