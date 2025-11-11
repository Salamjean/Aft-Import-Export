<?php

namespace App\Http\Controllers\Agent\Scan;

use App\Http\Controllers\Controller;
use App\Models\Colis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AgentScanController extends Controller
{
    public function entrepot(Request $request)
{
    // Récupérer l'agent connecté et son agence
    $agent = Auth::guard('agent')->user();
    
    if (!$agent || !$agent->agence_id) {
        // Si l'agent n'a pas d'agence, retourner une collection vide
        $colis = new \Illuminate\Pagination\LengthAwarePaginator(collect(), 0, 10, 1);
        return view('agent.scan.entrepot', compact('colis'));
    }

    // Récupérer tous les colis de l'agence de l'agent connecté
    $allColis = Colis::with(['agenceExpedition', 'agenceDestination', 'conteneur'])
                    ->where('agence_expedition_id', $agent->agence_id) // FILTRE PAR AGENCE
                    ->orderBy('created_at', 'desc')
                    ->get();

    // Filtrer manuellement les colis qui ont au moins une unité "entrepot"
    $colisFiltres = $allColis->filter(function ($colis) {
        $statutsIndividuels = json_decode($colis->statuts_individuels, true) ?? [];
        
        foreach ($statutsIndividuels as $statut) {
            if (isset($statut['statut']) && $statut['statut'] === 'entrepot') {
                return true;
            }
        }
        return false;
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
    
    return view('agent.scan.entrepot', compact('colis'));
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
     * Scanner un QR code et mettre à jour le statut individuel
     */
    public function scanQRCode(Request $request)
    {
        try {
            Log::info('=== SCAN QR CODE DÉBUT ===');
            Log::info('Données reçues:', $request->all());

            $request->validate([
                'qr_code' => 'required|string'
            ]);

            $qrCode = trim($request->qr_code);
            Log::info('Recherche du code QR:', ['qr_code' => $qrCode]);

            // Rechercher TOUS les colis pour trouver celui qui contient le code QR
            $colisList = Colis::all();
            $colisTrouve = null;

            foreach ($colisList as $colis) {
                $statutsIndividuels = json_decode($colis->statuts_individuels, true) ?? [];
                
                if (isset($statutsIndividuels[$qrCode])) {
                    $colisTrouve = $colis;
                    Log::info('Colis trouvé avec le code QR:', [
                        'colis_id' => $colis->id,
                        'reference' => $colis->reference_colis
                    ]);
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

            // Vérifier si le code QR existe dans les statuts individuels
            if (!isset($statutsIndividuels[$qrCode])) {
                Log::warning('Code QR non trouvé dans les statuts individuels', [
                    'qr_code' => $qrCode,
                    'colis_id' => $colis->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => '❌ Code QR non reconnu pour ce colis'
                ], 404);
            }

            $ancienStatut = $statutsIndividuels[$qrCode]['statut'];
            Log::info('Ancien statut:', ['ancien_statut' => $ancienStatut]);

            // Vérifier si l'unité est déjà en statut "chargé" - EMPÊCHER LA MODIFICATION
            if ($statutsIndividuels[$qrCode]['statut'] === 'charge') {
                Log::info('Unité déjà chargée - impossible de mettre en entrepôt', ['qr_code' => $qrCode]);
                return response()->json([
                    'success' => false,
                    'message' => '❌ Cette unité est déjà chargée et ne peut pas être mise en entrepôt',
                    'colis' => [
                        'id' => $colis->id,
                        'reference_colis' => $colis->reference_colis,
                        'statut' => $colis->statut
                    ],
                    'unite' => [
                        'code_colis' => $qrCode,
                        'statut' => 'chargé',
                        'produit' => $statutsIndividuels[$qrCode]['produit'] ?? 'Non spécifié',
                        'position' => "Colis " . ($statutsIndividuels[$qrCode]['colis_numero'] ?? '?') . " - Unité " . ($statutsIndividuels[$qrCode]['unite_numero'] ?? '?')
                    ]
                ]);
            }

            // Vérifier si le statut peut être mis à jour (déjà en entrepôt)
            if ($statutsIndividuels[$qrCode]['statut'] === 'entrepot') {
                Log::info('Unité déjà en entrepôt', ['qr_code' => $qrCode]);
                return response()->json([
                    'success' => false,
                    'message' => 'ℹ️ Cette unité est déjà en Entrepôt',
                    'colis' => [
                        'id' => $colis->id,
                        'reference_colis' => $colis->reference_colis,
                        'statut' => $colis->statut
                    ],
                    'unite' => [
                        'code_colis' => $qrCode,
                        'statut' => 'entrepot',
                        'produit' => $statutsIndividuels[$qrCode]['produit'] ?? 'Non spécifié',
                        'position' => "Colis " . ($statutsIndividuels[$qrCode]['colis_numero'] ?? '?') . " - Unité " . ($statutsIndividuels[$qrCode]['unite_numero'] ?? '?')
                    ]
                ]);
            }

            // Mettre à jour le statut individuel
            $statutsIndividuels[$qrCode]['statut'] = 'entrepot';
            $statutsIndividuels[$qrCode]['localisation_actuelle'] = 'Entrepôt Principal';
            $statutsIndividuels[$qrCode]['date_modification'] = now()->toDateTimeString();
            $statutsIndividuels[$qrCode]['notes'] = 'Scanné et mis en entrepôt le ' . now()->format('d/m/Y H:i');
            
            // Ajouter à l'historique
            $statutsIndividuels[$qrCode]['historique'][] = [
                'statut' => 'entrepot',
                'date' => now()->toDateTimeString(),
                'localisation' => 'Entrepôt Principal',
                'agence_id' => null,
                'notes' => 'Scanné et mis en entrepôt'
            ];

            // Mettre à jour le colis
            $colis->statuts_individuels = json_encode($statutsIndividuels);
            
            // Vérifier si tous les statuts individuels sont "entrepot" OU "chargé" pour mettre à jour le statut global
            $tousEnEntrepotOuCharge = $this->verifierTousEnEntrepotOuCharge($statutsIndividuels);
            
            if ($tousEnEntrepotOuCharge && $colis->statut !== 'entrepot') {
                $colis->statut = 'entrepot';
                Log::info('Tous les colis sont en entrepôt ou chargés - Statut global mis à jour à "entrepot"');
            }
            
            $colis->save();

            // Compter les unités scannées (uniquement celles en entrepôt)
            $unitesScannees = $this->compterIndividuelsScannes($statutsIndividuels);
            $totalUnites = count($statutsIndividuels);

            Log::info('Scan réussi:', [
                'colis_id' => $colis->id,
                'unite' => $qrCode,
                'ancien_statut' => $ancienStatut,
                'nouveau_statut' => 'entrepot',
                'progression' => $unitesScannees . '/' . $totalUnites
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Unité scannée avec succès !',
                'colis' => [
                    'id' => $colis->id,
                    'reference_colis' => $colis->reference_colis,
                    'statut' => $colis->statut,
                    'total_unites' => $totalUnites,
                    'unites_scannees' => $unitesScannees,
                    'unites_chargees' => $this->compterUnitesChargees($statutsIndividuels),
                    'progression' => round(($unitesScannees / $totalUnites) * 100, 2)
                ],
                'unite' => [
                    'code_colis' => $qrCode,
                    'ancien_statut' => $ancienStatut,
                    'nouveau_statut' => 'entrepot',
                    'produit' => $statutsIndividuels[$qrCode]['produit'] ?? 'Non spécifié',
                    'position' => "Colis " . ($statutsIndividuels[$qrCode]['colis_numero'] ?? '?') . " - Unité " . ($statutsIndividuels[$qrCode]['unite_numero'] ?? '?'),
                    'localisation' => 'Entrepôt Principal'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur scan QR code: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => '❌ Erreur lors du traitement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifie si tous les statuts individuels sont "entrepot" ou "chargé"
     */
    private function verifierTousEnEntrepotOuCharge(array $statutsIndividuels): bool
    {
        foreach ($statutsIndividuels as $statut) {
            if ($statut['statut'] !== 'entrepot' && $statut['statut'] !== 'chargé') {
                return false;
            }
        }
        return true;
    }

    /**
     * Compte les unités chargées
     */
    private function compterUnitesChargees(array $statutsIndividuels): int
    {
        $count = 0;
        foreach ($statutsIndividuels as $statut) {
            if ($statut['statut'] === 'chargé') {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Compter le nombre d'unités scannées (statut = entrepot)
     */
    private function compterIndividuelsScannes($statutsIndividuels)
    {
        $count = 0;
        foreach ($statutsIndividuels as $statut) {
            if ($statut['statut'] === 'entrepot') {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Vérifier si tous les statuts individuels sont "entrepot"
     */
    private function verifierTousEnEntrepot($statutsIndividuels)
    {
        foreach ($statutsIndividuels as $statut) {
            if ($statut['statut'] !== 'entrepot') {
                return false;
            }
        }
        return true;
    }

    /**
     * API pour récupérer les détails d'un colis avec ses statuts individuels
     */
    public function getColisDetails($id)
    {
        try {
            $colis = Colis::with(['agenceExpedition', 'agenceDestination', 'conteneur'])
                        ->findOrFail($id);

            // Décoder les données
            $colisDetails = json_decode($colis->colis, true);
            $statutsIndividuels = json_decode($colis->statuts_individuels, true) ?? [];
            $codesColis = json_decode($colis->code_colis, true) ?? [];

            // Compter les statuts
            $compteurStatuts = [
                'valide' => 0,
                'charge' => 0,
                'entrepot' => 0,
                'decharge' => 0,
                'livre' => 0,
                'annule' => 0
            ];

            foreach ($statutsIndividuels as $statut) {
                if (isset($compteurStatuts[$statut['statut']])) {
                    $compteurStatuts[$statut['statut']]++;
                }
            }

            return response()->json([
                'id' => $colis->id,
                'reference_colis' => $colis->reference_colis,
                'code_colis' => $codesColis,
                'mode_transit' => $colis->mode_transit,
                'devise' => $colis->devise,
                'name_expediteur' => $colis->name_expediteur,
                'prenom_expediteur' => $colis->prenom_expediteur,
                'email_expediteur' => $colis->email_expediteur,
                'contact_expediteur' => $colis->contact_expediteur,
                'adresse_expediteur' => $colis->adresse_expediteur,
                'name_destinataire' => $colis->name_destinataire,
                'prenom_destinataire' => $colis->prenom_destinataire,
                'email_destinataire' => $colis->email_destinataire,
                'indicatif' => $colis->indicatif,
                'contact_destinataire' => $colis->contact_destinataire,
                'adresse_destinataire' => $colis->adresse_destinataire,
                'agence_expedition' => $colis->agence_expedition,
                'agence_destination' => $colis->agence_destination,
                'statut' => $colis->statut,
                'statut_paiement' => $colis->statut_paiement,
                'montant_total' => $colis->montant_total,
                'montant_paye' => $colis->montant_paye,
                'reste_a_payer' => $colis->reste_a_payer,
                'methode_paiement' => $colis->methode_paiement,
                'montant_colis' => $colis->montant_colis,
                'created_at' => $colis->created_at->format('d/m/Y H:i'),
                'nombre_types_colis' => is_array($colisDetails) ? count($colisDetails) : 0,
                'colis_details' => $colisDetails,
                'statuts_individuels' => $statutsIndividuels,
                'compteur_statuts' => $compteurStatuts,
                'total_individuels' => count($statutsIndividuels),
                'individuels_scannes' => $this->compterIndividuelsScannes($statutsIndividuels),
                'individuels_chargees' => $this->compterUnitesChargees($statutsIndividuels)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Colis non trouvé'
            ], 404);
        }
    }
}
