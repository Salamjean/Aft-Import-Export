<?php

namespace App\Http\Controllers\Agent\Scan;

use App\Http\Controllers\Controller;
use App\Models\Colis;
use App\Models\Conteneur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AgentChargerController extends Controller
{
     /**
 * Page pour le chargement des colis dans les conteneurs
 */
public function charge(Request $request)
{
    // Récupérer tous les colis d'abord
    $allColis = Colis::with(['agenceExpedition', 'agenceDestination', 'conteneur'])
                    ->orderBy('created_at', 'desc')
                    ->get();

    // Filtrer les colis qui ont au moins une unité "entrepot" (prêts à charger) OU "charge" (déjà chargés)
    $colisFiltres = $allColis->filter(function ($colis) {
        $statutsIndividuels = json_decode($colis->statuts_individuels, true) ?? [];
        
        $aDesUnitesEntrepot = false;
        $aDesUnitesChargees = false;
        
        foreach ($statutsIndividuels as $statut) {
            if (isset($statut['statut'])) {
                if ($statut['statut'] === 'entrepot') {
                    $aDesUnitesEntrepot = true;
                }
                if ($statut['statut'] === 'charge') {
                    $aDesUnitesChargees = true;
                }
            }
        }
        
        // Inclure les colis qui ont des unités en entrepôt (à charger) ou déjà chargées
        return $aDesUnitesEntrepot || $aDesUnitesChargees;
    });

    // Appliquer les filtres supplémentaires
    if ($request->has('search') && !empty($request->search)) {
        $search = strtolower($request->search);
        $colisFiltres = $colisFiltres->filter(function ($colis) use ($search) {
            return str_contains(strtolower($colis->reference_colis), $search) ||
                   str_contains(strtolower($colis->name_expediteur), $search) ||
                   str_contains(strtolower($colis->name_destinataire), $search) ||
                   str_contains(strtolower($colis->email_expediteur), $search) ||
                   str_contains(strtolower($colis->email_destinataire), $search) ||
                   str_contains(strtolower($colis->code_colis), $search);
        });
    }

    if ($request->has('mode_transit') && !empty($request->mode_transit)) {
        $colisFiltres = $colisFiltres->where('mode_transit', $request->mode_transit);
    }

    if ($request->has('paiement') && !empty($request->paiement)) {
        $colisFiltres = $colisFiltres->where('statut_paiement', $request->paiement);
    }

    // Pagination manuelle
    $page = $request->get('page', 1);
    $perPage = 10;
    
    $colis = new \Illuminate\Pagination\LengthAwarePaginator(
        $colisFiltres->forPage($page, $perPage),
        $colisFiltres->count(),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    // Ajouter les métriques
    $colis->getCollection()->transform(function ($item) {
        $colisData = json_decode($item->colis, true);
        $item->nombre_types_colis = is_array($colisData) ? count($colisData) : 0;
        
        $statutsIndividuels = json_decode($item->statuts_individuels, true) ?? [];
        $item->total_individuels = count($statutsIndividuels);
        
        // Compter les statuts individuels
        $item->individuels_valides = $this->compterIndividuelsParStatut($statutsIndividuels, 'valide');
        $item->individuels_charges = $this->compterIndividuelsParStatut($statutsIndividuels, 'charge');
        $item->individuels_entrepot = $this->compterIndividuelsParStatut($statutsIndividuels, 'entrepot');
        $item->individuels_decharges = $this->compterIndividuelsParStatut($statutsIndividuels, 'decharge');
        $item->individuels_livres = $this->compterIndividuelsParStatut($statutsIndividuels, 'livre');
        $item->individuels_annules = $this->compterIndividuelsParStatut($statutsIndividuels, 'annule');
        
        return $item;
    });
    
    return view('agent.scan.charge', compact('colis'));
}
/**
 * Compter le nombre d'unités individuelles par statut
 */
private function compterIndividuelsParStatut($statutsIndividuels, $statutRecherche)
{
    if (empty($statutsIndividuels)) {
        return 0;
    }
    
    $compteur = 0;
    foreach ($statutsIndividuels as $statut) {
        if (isset($statut['statut']) && $statut['statut'] === $statutRecherche) {
            $compteur++;
        }
    }
    
    return $compteur;
}
    /**
 * Scanner un QR code pour charger dans un conteneur
 */
/**
 * Scanner un QR code pour charger dans un conteneur
 */
public function scanQRCodeCharge(Request $request)
{
    try {
        Log::info('=== SCAN QR CODE CHARGE DÉBUT ===');
        Log::info('Données reçues:', $request->all());

        $request->validate([
            'qr_code' => 'required|string',
            'conteneur_id' => 'nullable|integer'
        ]);

        $qrCode = trim($request->qr_code);
        $conteneurId = $request->conteneur_id;
        
        Log::info('Recherche du code QR pour chargement:', ['qr_code' => $qrCode, 'conteneur_id' => $conteneurId]);

        // Rechercher le colis contenant le code QR
        $colisTrouve = null;
        $colisList = Colis::all();

        foreach ($colisList as $colis) {
            $statutsIndividuels = json_decode($colis->statuts_individuels, true) ?? [];
            if (isset($statutsIndividuels[$qrCode])) {
                $colisTrouve = $colis;
                break;
            }
        }

        if (!$colisTrouve) {
            Log::warning('Aucun colis trouvé avec ce code QR', ['qr_code' => $qrCode]);
            return response()->json([
                'success' => false,
                'message' => '❌ Aucun colis trouvé avec le code: ' . $qrCode
            ], 404);
        }

        $colis = $colisTrouve;
        $statutsIndividuels = json_decode($colis->statuts_individuels, true) ?? [];

        if (!isset($statutsIndividuels[$qrCode])) {
            return response()->json([
                'success' => false,
                'message' => '❌ Code QR non reconnu pour ce colis'
            ], 404);
        }

        $ancienStatut = $statutsIndividuels[$qrCode]['statut'];
        $produit = $statutsIndividuels[$qrCode]['produit'] ?? 'Non spécifié';
        $colisNumero = $statutsIndividuels[$qrCode]['colis_numero'] ?? '?';
        $uniteNumero = $statutsIndividuels[$qrCode]['unite_numero'] ?? '?';

        // Vérifier si le colis a déjà un conteneur assigné
        $ancienConteneurId = $colis->conteneur_id;
        
        // Si un conteneur_id est fourni dans la requête, on l'utilise
        // Sinon, on essaie de récupérer le conteneur actuellement ouvert
        if (!$conteneurId) {
            // Récupérer le conteneur actuellement ouvert
            $conteneurOuvert = Conteneur::where('statut', 'ouvert')->first();
            
            if ($conteneurOuvert) {
                $conteneurId = $conteneurOuvert->id;
                Log::info('Conteneur ouvert trouvé automatiquement', [
                    'conteneur_id' => $conteneurId,
                    'conteneur_name' => $conteneurOuvert->name_conteneur
                ]);
            } else {
                // Vérifier si le colis avait un ancien conteneur
                if ($ancienConteneurId) {
                    $ancienConteneur = Conteneur::find($ancienConteneurId);
                    if ($ancienConteneur && $ancienConteneur->statut === 'fermer') {
                        return response()->json([
                            'success' => false,
                            'message' => '❌ Le conteneur précédent (#'.$ancienConteneurId.') est fermé. Veuillez scanner un conteneur ouvert d\'abord.',
                            'colis' => [
                                'id' => $colis->id,
                                'reference_colis' => $colis->reference_colis,
                                'statut' => $colis->statut,
                                'ancien_conteneur_id' => $ancienConteneurId
                            ]
                        ], 400);
                    }
                }
                
                return response()->json([
                    'success' => false,
                    'message' => '❌ Aucun conteneur ouvert disponible. Veuillez ouvrir un conteneur d\'abord.'
                ], 400);
            }
        }

        // Vérifier que le conteneur est ouvert
        $conteneur = Conteneur::find($conteneurId);
        if (!$conteneur) {
            return response()->json([
                'success' => false,
                'message' => '❌ Conteneur non trouvé'
            ], 404);
        }

        if ($conteneur->statut !== 'ouvert') {
            return response()->json([
                'success' => false,
                'message' => '❌ Le conteneur #'.$conteneurId.' est fermé. Veuillez utiliser un conteneur ouvert.'
            ], 400);
        }

        // Vérifier si l'unité était déjà dans un autre conteneur
        $conteneurPrecedent = null;
        if ($ancienConteneurId && $ancienConteneurId != $conteneurId) {
            $conteneurPrecedent = Conteneur::find($ancienConteneurId);
            if ($conteneurPrecedent && $conteneurPrecedent->statut === 'fermer') {
                Log::info('Colis transféré depuis un conteneur fermé', [
                    'ancien_conteneur_id' => $ancienConteneurId,
                    'nouveau_conteneur_id' => $conteneurId
                ]);
            }
        }

        if ($ancienStatut === 'charge') {
            // Si déjà chargé, vérifier si c'est dans le même conteneur
            $localisationActuelle = $statutsIndividuels[$qrCode]['localisation_actuelle'] ?? '';
            if (str_contains($localisationActuelle, 'Conteneur #' . $conteneurId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ℹ️ Cette unité est déjà chargée dans ce conteneur',
                    'colis' => [
                        'id' => $colis->id,
                        'reference_colis' => $colis->reference_colis,
                        'statut' => $colis->statut,
                        'conteneur_id' => $colis->conteneur_id
                    ],
                    'unite' => [
                        'code_colis' => $qrCode,
                        'statut' => 'charge',
                        'produit' => $produit,
                        'position' => "Colis {$colisNumero} - Unité {$uniteNumero}",
                        'localisation' => $localisationActuelle
                    ]
                ]);
            }
        }

        // Vérifications
        // if ($ancienStatut !== 'entrepot') {
        //     return response()->json([
        //         'success' => false,
        //         'message' => '❌ Cette unité doit d\'abord être mise en entrepôt avant chargement. Statut actuel: ' . $this->getStatutText($ancienStatut),
        //         'colis' => [
        //             'id' => $colis->id,
        //             'reference_colis' => $colis->reference_colis,
        //             'statut' => $colis->statut
        //         ],
        //         'unite' => [
        //             'code_colis' => $qrCode,
        //             'statut' => $ancienStatut,
        //             'produit' => $produit,
        //             'position' => "Colis {$colisNumero} - Unité {$uniteNumero}"
        //         ]
        //     ]);
        // }

        // Mise à jour de l'unité individuelle
        $statutsIndividuels[$qrCode]['statut'] = 'charge';
        $statutsIndividuels[$qrCode]['localisation_actuelle'] = 'Conteneur #' . $conteneurId;
        $statutsIndividuels[$qrCode]['date_modification'] = now()->toDateTimeString();
        $statutsIndividuels[$qrCode]['notes'] = 'Chargé dans le conteneur #' . $conteneurId . ' le ' . now()->format('d/m/Y H:i');
        
        $statutsIndividuels[$qrCode]['historique'][] = [
            'statut' => 'charge',
            'date' => now()->toDateTimeString(),
            'localisation' => 'Conteneur #' . $conteneurId,
            'agence_id' => null,
            'notes' => 'Chargé dans le conteneur #' . $conteneurId
        ];

        // Mise à jour du colis avec le nouveau conteneur
        $colis->statuts_individuels = json_encode($statutsIndividuels);
        $colis->conteneur_id = $conteneurId; // Mettre à jour l'ID du conteneur
        
        // ✅ LOGIQUE PRINCIPALE : Vérifier si TOUTES les unités sont chargées
        $tousCharges = $this->verifierTousCharges($statutsIndividuels);
        $ancienStatutGlobal = $colis->statut;
        
        if ($tousCharges) {
            // Si TOUTES les unités sont chargées, mettre à jour le statut global
            $colis->statut = 'charge';
            Log::info('🎉 TOUTES LES UNITÉS CHARGÉES - Statut global mis à jour', [
                'colis_id' => $colis->id,
                'ancien_statut_global' => $ancienStatutGlobal,
                'nouveau_statut_global' => 'charge',
                'conteneur_id' => $conteneurId
            ]);
        } else {
            Log::info('Progression du chargement', [
                'colis_id' => $colis->id,
                'conteneur_id' => $conteneurId,
                'unites_chargees' => $this->compterIndividuelsCharges($statutsIndividuels),
                'total_unites' => count($statutsIndividuels)
            ]);
        }
        
        $colis->save();

        // Statistiques
        $unitesChargees = $this->compterIndividuelsCharges($statutsIndividuels);
        $totalUnites = count($statutsIndividuels);
        $progression = round(($unitesChargees / $totalUnites) * 100, 2);

        Log::info('Scan charge réussi:', [
            'colis_id' => $colis->id,
            'conteneur_id' => $conteneurId,
            'unite' => $qrCode,
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => 'charge',
            'progression' => $unitesChargees . '/' . $totalUnites,
            'tous_charges' => $tousCharges,
            'ancien_conteneur' => $ancienConteneurId
        ]);

        return response()->json([
            'success' => true,
            'message' => $tousCharges ? 
                '🎉 FÉLICITATIONS ! Toutes les unités sont chargées !' : 
                '✅ Unité chargée avec succès !',
            'colis' => [
                'id' => $colis->id,
                'reference_colis' => $colis->reference_colis,
                'statut' => $colis->statut,
                'conteneur_id' => $colis->conteneur_id,
                'total_unites' => $totalUnites,
                'unites_chargees' => $unitesChargees,
                'progression' => $progression,
                'tous_charges' => $tousCharges
            ],
            'unite' => [
                'code_colis' => $qrCode,
                'ancien_statut' => $ancienStatut,
                'nouveau_statut' => 'charge',
                'produit' => $produit,
                'position' => "Colis {$colisNumero} - Unité {$uniteNumero}",
                'localisation' => 'Conteneur #' . $conteneurId
            ],
            'conteneur' => [
                'id' => $conteneurId,
                'name' => $conteneur->name_conteneur,
                'numero' => $conteneur->numero_conteneur
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('❌ Erreur scan QR code charge: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => '❌ Erreur lors du traitement: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Vérifier si tous les statuts individuels sont "charge"
 */
private function verifierTousCharges($statutsIndividuels)
{
    foreach ($statutsIndividuels as $statut) {
        if ($statut['statut'] !== 'charge') {
            return false;
        }
    }
    return true;
}

/**
 * Compter le nombre d'unités chargées (statut = charge)
 */
private function compterIndividuelsCharges($statutsIndividuels)
{
    $count = 0;
    foreach ($statutsIndividuels as $statut) {
        if ($statut['statut'] === 'charge') {
            $count++;
        }
    }
    return $count;
}

/**
 * Obtenir le texte du statut
 */
private function getStatutText($statut)
{
    $statuts = [
        'valide' => 'Validé',
        'charge' => 'Chargé',
        'entrepot' => 'En Entrepôt',
        'decharge' => 'Déchargé',
        'livre' => 'Livré',
        'annule' => 'Annulé'
    ];
    
    return $statuts[$statut] ?? $statut;
}
}
