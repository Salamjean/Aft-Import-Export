<?php

namespace App\Http\Controllers\Agent\Scan;

use App\Http\Controllers\Controller;
use App\Models\Colis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AgentDechargerController extends Controller
{
    /**
     * Page pour le déchargement des colis des conteneurs
     */
    public function decharge(Request $request)
    {
        // Récupérer l'agent connecté et son agence
        $agent = Auth::guard('agent')->user();

        if (!$agent || !$agent->agence_id) {
            // Si l'agent n'a pas d'agence, retourner une collection vide
            $colis = new \Illuminate\Pagination\LengthAwarePaginator(collect(), 0, 10, 1);
            return view('agent.scan.decharge', compact('colis'));
        }

        $query = Colis::with(['agenceExpedition', 'agenceDestination', 'conteneur'])
            ->where(function ($q) use ($agent) {
                $q->where('agence_expedition_id', $agent->agence_id)
                  ->orWhere('agence_destination_id', $agent->agence_id);
            })
            ->where('statuts_individuels', 'LIKE', '%"statut":"decharge"%');

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
        Log::info('Colis filtrés pour déchargement:', [
            'agent_id' => $agent->id,
            'agence_id' => $agent->agence_id,
            'colis_avec_decharge' => $colis->total(),
            'colis_pagines' => $colis->count()
        ]);

        return view('agent.scan.decharge', compact('colis'));
    }

    /**
     * Scanner un QR code pour décharger d'un conteneur
     */
    public function scanQRCodeDecharge(Request $request)
    {
        try {
            Log::info('=== SCAN QR CODE DÉCHARGE DÉBUT ===');
            Log::info('Données reçues:', $request->all());

            // Récupérer l'agent connecté
            $agent = Auth::guard('agent')->user();

            if (!$agent || !$agent->agence_id) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Agent non connecté ou aucune agence associée'
                ], 403);
            }

            $request->validate([
                'qr_code' => 'required|string',
                'agence_destination_id' => 'nullable|integer'
            ]);

            $qrCode = trim($request->qr_code);
            $agenceDestinationId = $request->agence_destination_id;

            Log::info('Recherche du code QR pour déchargement:', [
                'agent_id' => $agent->id,
                'agence_agent' => $agent->agence_id,
                'qr_code' => $qrCode,
                'agence_destination_id' => $agenceDestinationId
            ]);

            // Rechercher le colis contenant le code QR dans l'agence de l'agent
            $colis = Colis::where('agence_expedition_id', $agent->agence_id)
                ->where('statuts_individuels', 'LIKE', '%"' . $qrCode . '"%')
                ->first();

            if (!$colis) {
                // MODIFICATION : Au lieu de retourner une erreur 404, on cherche dans toutes les agences (recherche globale)
                Log::info('Code QR non trouvé dans l\'agence de l\'agent, recherche globale...', [
                    'qr_code' => $qrCode,
                    'agence_agent' => $agent->agence_id
                ]);

                $colis = Colis::where('statuts_individuels', 'LIKE', '%"' . $qrCode . '"%')->first();

                if ($colis) {
                    Log::info('Code QR trouvé dans une autre agence', [
                        'colis_id' => $colis->id,
                        'agence_colis' => $colis->agence_expedition_id
                    ]);
                } else {
                    Log::warning('Code QR non trouvé dans aucun colis', ['qr_code' => $qrCode]);
                    return response()->json([
                        'success' => false,
                        'message' => '❌ Code QR non reconnu dans le système'
                    ], 404);
                }
            }

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
            if ($ancienStatut !== 'charge') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Cette unité doit d\'abord être chargée avant déchargement. Statut actuel: ' . $this->getStatutText($ancienStatut),
                    'colis' => [
                        'id' => $colis->id,
                        'reference_colis' => $colis->reference_colis,
                        'statut' => $colis->statut
                    ],
                    'unite' => [
                        'code_colis' => $qrCode,
                        'statut' => $ancienStatut,
                        'produit' => $produit,
                        'position' => "Colis {$colisNumero} - Unité {$uniteNumero}"
                    ]
                ]);
            }

            if ($ancienStatut === 'decharge') {
                return response()->json([
                    'success' => false,
                    'message' => 'ℹ️ Cette unité est déjà déchargée',
                    'colis' => [
                        'id' => $colis->id,
                        'reference_colis' => $colis->reference_colis,
                        'statut' => $colis->statut
                    ],
                    'unite' => [
                        'code_colis' => $qrCode,
                        'statut' => 'decharge',
                        'produit' => $produit,
                        'position' => "Colis {$colisNumero} - Unité {$uniteNumero}"
                    ]
                ]);
            }

            // Mise à jour de l'unité individuelle
            $statutsIndividuels[$qrCode]['statut'] = 'decharge';
            $statutsIndividuels[$qrCode]['localisation_actuelle'] = $agenceDestinationId ? 'Agence #' . $agenceDestinationId : 'Agence de destination';
            $statutsIndividuels[$qrCode]['agence_actuelle_id'] = $agenceDestinationId;
            $statutsIndividuels[$qrCode]['date_modification'] = now()->toDateTimeString();
            $statutsIndividuels[$qrCode]['notes'] = 'Déchargé du conteneur le ' . now()->format('d/m/Y H:i') . ' par agent #' . $agent->id;

            $statutsIndividuels[$qrCode]['historique'][] = [
                'statut' => 'decharge',
                'date' => now()->toDateTimeString(),
                'localisation' => $agenceDestinationId ? 'Agence #' . $agenceDestinationId : 'Agence de destination',
                'agence_id' => $agenceDestinationId,
                'agent_id' => $agent->id,
                'notes' => 'Déchargé du conteneur vers l\'agence de destination par agent'
            ];

            // Mise à jour du colis
            $colis->statuts_individuels = json_encode($statutsIndividuels);

            // ✅ LOGIQUE PRINCIPALE : Vérifier si TOUTES les unités sont déchargées
            $tousDecharges = $this->verifierTousDecharges($statutsIndividuels);
            $ancienStatutGlobal = $colis->statut;

            if ($tousDecharges) {
                // Si TOUTES les unités sont déchargées, mettre à jour le statut global
                $colis->statut = 'decharge';
                Log::info('🎉 TOUTES LES UNITÉS DÉCHARGÉES - Statut global mis à jour', [
                    'colis_id' => $colis->id,
                    'agent_id' => $agent->id,
                    'agence_agent' => $agent->agence_id,
                    'ancien_statut_global' => $ancienStatutGlobal,
                    'nouveau_statut_global' => 'decharge'
                ]);
            } else {
                Log::info('Progression du déchargement', [
                    'colis_id' => $colis->id,
                    'agent_id' => $agent->id,
                    'unites_dechargees' => $this->compterIndividuelsDecharges($statutsIndividuels),
                    'total_unites' => count($statutsIndividuels)
                ]);
            }

            $colis->save();

            // Statistiques
            $unitesDechargees = $this->compterIndividuelsDecharges($statutsIndividuels);
            $totalUnites = count($statutsIndividuels);
            $progression = round(($unitesDechargees / $totalUnites) * 100, 2);

            Log::info('Scan décharge réussi:', [
                'colis_id' => $colis->id,
                'agent_id' => $agent->id,
                'agence_agent' => $agent->agence_id,
                'unite' => $qrCode,
                'ancien_statut' => $ancienStatut,
                'nouveau_statut' => 'decharge',
                'progression' => $unitesDechargees . '/' . $totalUnites,
                'tous_decharges' => $tousDecharges
            ]);

            return response()->json([
                'success' => true,
                'message' => $tousDecharges ?
                    '🎉 FÉLICITATIONS ! Toutes les unités sont déchargées !' :
                    '✅ Unité déchargée avec succès !',
                'colis' => [
                    'id' => $colis->id,
                    'reference_colis' => $colis->reference_colis,
                    'statut' => $colis->statut,
                    'total_unites' => $totalUnites,
                    'unites_dechargees' => $unitesDechargees,
                    'progression' => $progression,
                    'tous_decharges' => $tousDecharges
                ],
                'unite' => [
                    'code_colis' => $qrCode,
                    'ancien_statut' => $ancienStatut,
                    'nouveau_statut' => 'decharge',
                    'produit' => $produit,
                    'position' => "Colis {$colisNumero} - Unité {$uniteNumero}",
                    'localisation' => $agenceDestinationId ? 'Agence #' . $agenceDestinationId : 'Agence de destination'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur scan QR code décharge: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => '❌ Erreur lors du traitement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier si tous les statuts individuels sont "decharge"
     */
    private function verifierTousDecharges($statutsIndividuels)
    {
        foreach ($statutsIndividuels as $statut) {
            if ($statut['statut'] !== 'decharge') {
                return false;
            }
        }
        return true;
    }

    /**
     * Compter le nombre d'unités déchargées (statut = decharge)
     */
    private function compterIndividuelsDecharges($statutsIndividuels)
    {
        $count = 0;
        foreach ($statutsIndividuels as $statut) {
            if ($statut['statut'] === 'decharge') {
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