<?php

namespace App\Http\Controllers\Agent\Cote_Ivoire;

use App\Http\Controllers\Controller;
use App\Models\Colis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IvoireScanLivrerController extends Controller
{
    /**
     * Page pour la livraison des colis
     */
    public function livrer(Request $request)
    {
        // Récupérer l'agent connecté et son agence
        $agent = Auth::guard('agent')->user();
        
        if (!$agent || !$agent->agence_id) {
            // Si l'agent n'a pas d'agence, retourner une collection vide
            $colis = new \Illuminate\Pagination\LengthAwarePaginator(collect(), 0, 10, 1);
            return view('ivoire.scan.livrer', compact('colis'));
        }

        $query = Colis::with(['agenceExpedition', 'agenceDestination', 'conteneur'])
            ->where(function ($q) use ($agent) {
                $q->where('agence_expedition_id', $agent->agence_id)
                  ->orWhere('agence_destination_id', $agent->agence_id);
            })
            ->where(function ($q) {
                $q->where('statuts_individuels', 'LIKE', '%"statut":"livre"%')
                  ->orWhere('statuts_individuels', 'LIKE', '%"statut": "livre"%');
            });

        // Appliquer les filtres supplémentaires en SQL
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_colis', 'LIKE', "%{$search}%")
                    ->orWhere('name_expediteur', 'LIKE', "%{$search}%")
                    ->orWhere('name_destinataire', 'LIKE', "%{$search}%")
                    ->orWhere('email_expediteur', 'LIKE', "%{$search}%")
                    ->orWhere('email_destinataire', 'LIKE', "%{$search}%")
                    ->orWhere('code_colis', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('mode_transit') && !empty($request->mode_transit)) {
            $query->where('mode_transit', $request->mode_transit);
        }

        if ($request->has('paiement') && !empty($request->paiement)) {
            $query->where('statut_paiement', $request->paiement);
        }

        // Pagination native au niveau SQL
        $colis = $query->orderBy('created_at', 'desc')->paginate(10);

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

        // Log pour débogage
        Log::info('Colis filtrés pour livraison:', [
            'agent_id' => $agent->id,
            'agence_id' => $agent->agence_id,
            'colis_avec_livraison' => $colis->total(),
            'colis_pagines' => $colis->count()
        ]);
        
        return view('ivoire.scan.livrer', compact('colis'));
    }

    /**
     * Scanner un QR code pour livrer un colis
     */
    public function scanQRCodeLivrer(Request $request)
    {
        try {
            Log::info('=== SCAN QR CODE LIVRAISON DÉBUT ===');
            Log::info('Données reçues:', $request->all());

            // Récupérer l'agent connecté
            $agent = Auth::guard('agent')->user();
            
            if (!$agent) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Agent non connecté'
                ], 403);
            }

            $request->validate([
                'qr_code' => 'required|string',
                'notes_livraison' => 'nullable|string'
            ]);

            $qrCode = trim($request->qr_code);
            $notesLivraison = $request->notes_livraison;
            
            Log::info('Recherche du code QR pour livraison:', [
                'agent_id' => $agent->id,
                'agence_agent' => $agent->agence_id,
                'qr_code' => $qrCode, 
                'notes_livraison' => $notesLivraison
            ]);

            // RECHERCHE SIMPLIFIÉE : On cherche dans TOUS les colis sans restriction d'agence
            // Recherche directe dans le JSON avec MySQL
            $colis = Colis::where('statuts_individuels', 'LIKE', '%"' . $qrCode . '"%')->first();

            if (!$colis) {
                Log::warning('Aucun colis trouvé avec ce code QR', [
                    'qr_code' => $qrCode,
                    'agent_id' => $agent->id,
                    'method_used' => 'both_methods_failed'
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => '❌ Aucun colis trouvé avec le code: ' . $qrCode
                ], 404);
            }

            $statutsIndividuels = json_decode($colis->statuts_individuels, true) ?? [];

            if (!isset($statutsIndividuels[$qrCode])) {
                Log::warning('Code QR trouvé dans le colis mais pas dans les statuts individuels', [
                    'colis_id' => $colis->id,
                    'qr_code' => $qrCode,
                    'available_codes' => array_keys($statutsIndividuels)
                ]);
                return response()->json([
                    'success' => false,
                    'message' => '❌ Code QR non reconnu pour ce colis'
                ], 404);
            }

            $ancienStatut = $statutsIndividuels[$qrCode]['statut'];
            $produit = $statutsIndividuels[$qrCode]['produit'] ?? 'Non spécifié';
            $colisNumero = $statutsIndividuels[$qrCode]['colis_numero'] ?? '?';
            $uniteNumero = $statutsIndividuels[$qrCode]['unite_numero'] ?? '?';

            Log::info('Informations de l\'unité trouvée:', [
                'colis_id' => $colis->id,
                'reference_colis' => $colis->reference_colis,
                'ancien_statut' => $ancienStatut,
                'produit' => $produit,
                'colis_numero' => $colisNumero,
                'unite_numero' => $uniteNumero,
                'colis_agence_expedition' => $colis->agence_expedition_id,
                'colis_agence_destination' => $colis->agence_destination_id,
                'agent_agence' => $agent->agence_id
            ]);

            // Vérifications du statut uniquement
            if ($ancienStatut === 'livre') {
                return response()->json([
                    'success' => false,
                    'message' => 'ℹ️ Cette unité est déjà livrée',
                    'colis' => [
                        'id' => $colis->id,
                        'reference_colis' => $colis->reference_colis,
                        'statut' => $colis->statut
                    ],
                    'unite' => [
                        'code_colis' => $qrCode,
                        'statut' => 'livre',
                        'produit' => $produit,
                        'position' => "Colis {$colisNumero} - Unité {$uniteNumero}"
                    ]
                ]);
                
            }
            else if ($ancienStatut !== 'decharge') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Cette unité doit d\'abord être déchargée avant livraison. Statut actuel: ' . $this->getStatutText($ancienStatut),
                    'colis' => [
                        'id' => $colis->id,
                        'reference_colis' => $colis->reference_colis,
                        'statut' => $colis->statut,
                        'agence_expedition' => $colis->agence_expedition_id,
                        'agence_destination' => $colis->agence_destination_id
                    ],
                    'unite' => [
                        'code_colis' => $qrCode,
                        'statut' => $ancienStatut,
                        'produit' => $produit,
                        'position' => "Colis {$colisNumero} - Unité {$uniteNumero}"
                    ]
                ]);
            }

            // Mise à jour de l'unité individuelle
            $statutsIndividuels[$qrCode]['statut'] = 'livre';
            $statutsIndividuels[$qrCode]['localisation_actuelle'] = 'Livré au destinataire';
            $statutsIndividuels[$qrCode]['agence_actuelle_id'] = null; // Plus dans une agence
            $statutsIndividuels[$qrCode]['date_modification'] = now()->toDateTimeString();
            $statutsIndividuels[$qrCode]['notes'] = 'Livré au destinataire le ' . now()->format('d/m/Y H:i') . ' par agent #' . $agent->id;
            
            if ($notesLivraison) {
                $statutsIndividuels[$qrCode]['notes'] .= ' - ' . $notesLivraison;
            }
            
            $statutsIndividuels[$qrCode]['historique'][] = [
                'statut' => 'livre',
                'date' => now()->toDateTimeString(),
                'localisation' => 'Livré au destinataire',
                'agence_id' => null,
                'agent_id' => $agent->id,
                'notes' => $notesLivraison ? 'Livraison: ' . $notesLivraison : 'Livraison effectuée'
            ];

            // Mise à jour du colis
            $colis->statuts_individuels = json_encode($statutsIndividuels);
            
            // ✅ LOGIQUE PRINCIPALE : Vérifier si TOUTES les unités sont livrées
            $tousLivres = $this->verifierTousLivres($statutsIndividuels);
            $ancienStatutGlobal = $colis->statut;
            
            if ($tousLivres) {
                // Si TOUTES les unités sont livrées, mettre à jour le statut global
                $colis->statut = 'livre';
                Log::info('🎉 TOUTES LES UNITÉS LIVRÉES - Statut global mis à jour', [
                    'colis_id' => $colis->id,
                    'agent_id' => $agent->id,
                    'agence_agent' => $agent->agence_id,
                    'ancien_statut_global' => $ancienStatutGlobal,
                    'nouveau_statut_global' => 'livre'
                ]);
            } else {
                Log::info('Progression de la livraison', [
                    'colis_id' => $colis->id,
                    'agent_id' => $agent->id,
                    'unites_livrees' => $this->compterIndividuelsLivres($statutsIndividuels),
                    'total_unites' => count($statutsIndividuels)
                ]);
            }
            
            $colis->save();

            // Statistiques
            $unitesLivrees = $this->compterIndividuelsLivres($statutsIndividuels);
            $totalUnites = count($statutsIndividuels);
            $progression = round(($unitesLivrees / $totalUnites) * 100, 2);

            Log::info('Scan livraison réussi:', [
                'colis_id' => $colis->id,
                'agent_id' => $agent->id,
                'agence_agent' => $agent->agence_id,
                'unite' => $qrCode,
                'ancien_statut' => $ancienStatut,
                'nouveau_statut' => 'livre',
                'progression' => $unitesLivrees . '/' . $totalUnites,
                'tous_livres' => $tousLivres
            ]);

            return response()->json([
                'success' => true,
                'message' => $tousLivres ? 
                    '🎉 FÉLICITATIONS ! Toutes les unités sont livrées !' : 
                    '✅ Unité livrée avec succès !',
                'colis' => [
                    'id' => $colis->id,
                    'reference_colis' => $colis->reference_colis,
                    'statut' => $colis->statut,
                    'total_unites' => $totalUnites,
                    'unites_livrees' => $unitesLivrees,
                    'progression' => $progression,
                    'tous_livres' => $tousLivres,
                    'agence_expedition' => $colis->agence_expedition_id,
                    'agence_destination' => $colis->agence_destination_id
                ],
                'unite' => [
                    'code_colis' => $qrCode,
                    'ancien_statut' => $ancienStatut,
                    'nouveau_statut' => 'livre',
                    'produit' => $produit,
                    'position' => "Colis {$colisNumero} - Unité {$uniteNumero}",
                    'localisation' => 'Livré au destinataire',
                    'notes' => $notesLivraison
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur scan QR code livraison: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => '❌ Erreur lors du traitement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier si tous les statuts individuels sont "livre"
     */
    private function verifierTousLivres($statutsIndividuels)
    {
        foreach ($statutsIndividuels as $statut) {
            if ($statut['statut'] !== 'livre') {
                return false;
            }
        }
        return true;
    }

    /**
     * Compter le nombre d'unités livrées (statut = livre)
     */
    private function compterIndividuelsLivres($statutsIndividuels)
    {
        $count = 0;
        foreach ($statutsIndividuels as $statut) {
            if ($statut['statut'] === 'livre') {
                $count++;
            }
        }
        return $count;
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