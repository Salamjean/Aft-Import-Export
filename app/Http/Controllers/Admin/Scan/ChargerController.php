<?php

namespace App\Http\Controllers\Admin\Scan;

use App\Http\Controllers\Controller;
use App\Models\Colis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChargerController extends Controller
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
    
    return view('admin.scan.charge', compact('colis'));
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

        if ($ancienStatut === 'charge') {
            return response()->json([
                'success' => false,
                'message' => 'ℹ️ Cette unité est déjà chargée dans un conteneur',
                'colis' => [
                    'id' => $colis->id,
                    'reference_colis' => $colis->reference_colis,
                    'statut' => $colis->statut
                ],
                'unite' => [
                    'code_colis' => $qrCode,
                    'statut' => 'charge',
                    'produit' => $produit,
                    'position' => "Colis {$colisNumero} - Unité {$uniteNumero}"
                ]
            ]);
        }

        // Mise à jour de l'unité individuelle
        $statutsIndividuels[$qrCode]['statut'] = 'charge';
        $statutsIndividuels[$qrCode]['localisation_actuelle'] = $conteneurId ? 'Conteneur #' . $conteneurId : 'Conteneur';
        $statutsIndividuels[$qrCode]['date_modification'] = now()->toDateTimeString();
        $statutsIndividuels[$qrCode]['notes'] = 'Chargé dans le conteneur le ' . now()->format('d/m/Y H:i');
        
        $statutsIndividuels[$qrCode]['historique'][] = [
            'statut' => 'charge',
            'date' => now()->toDateTimeString(),
            'localisation' => $conteneurId ? 'Conteneur #' . $conteneurId : 'Conteneur',
            'agence_id' => null,
            'notes' => 'Chargé dans le conteneur'
        ];

        // Mise à jour du colis
        $colis->statuts_individuels = json_encode($statutsIndividuels);
        
        // ✅ LOGIQUE PRINCIPALE : Vérifier si TOUTES les unités sont chargées
        $tousCharges = $this->verifierTousCharges($statutsIndividuels);
        $ancienStatutGlobal = $colis->statut;
        
        if ($tousCharges) {
            // Si TOUTES les unités sont chargées, mettre à jour le statut global
            $colis->statut = 'charge';
            Log::info('🎉 TOUTES LES UNITÉS CHARGÉES - Statut global mis à jour', [
                'colis_id' => $colis->id,
                'ancien_statut_global' => $ancienStatutGlobal,
                'nouveau_statut_global' => 'charge'
            ]);
        } else {
            Log::info('Progression du chargement', [
                'colis_id' => $colis->id,
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
            'unite' => $qrCode,
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => 'charge',
            'progression' => $unitesChargees . '/' . $totalUnites,
            'tous_charges' => $tousCharges
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
                'localisation' => $conteneurId ? 'Conteneur #' . $conteneurId : 'Conteneur'
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
