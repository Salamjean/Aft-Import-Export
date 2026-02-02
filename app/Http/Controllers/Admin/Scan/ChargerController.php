<?php

namespace App\Http\Controllers\Admin\Scan;

use App\Http\Controllers\Controller;
use App\Models\Colis;
use App\Models\Conteneur;
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
            $statutsIndividuels = is_array($colis->statuts_individuels) ? $colis->statuts_individuels : (json_decode($colis->statuts_individuels, true) ?? []);

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
            $colisData = $item->colis;
            $item->nombre_types_colis = is_array($colisData) ? count($colisData) : 0;

            $statutsIndividuels = is_array($item->statuts_individuels) ? $item->statuts_individuels : (json_decode($item->statuts_individuels, true) ?? []);
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
            $statutsIndividuels = is_array($colis->statuts_individuels) ? $colis->statuts_individuels : (json_decode($colis->statuts_individuels, true) ?? []);

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

            // Déterminer le type de conteneur requis pour ce colis
            $typeConteneurRequis = $this->determinerTypeConteneur($colis->mode_transit);
            $agenceColisId = $colis->agence_expedition_id;

            Log::info('Type de conteneur requis pour ce colis:', [
                'mode_transit' => $colis->mode_transit,
                'type_requis' => $typeConteneurRequis,
                'agence_id' => $agenceColisId
            ]);

            // LOGIQUE DE GESTION DES CONTENEURS AVEC AGENCES
            if (!$conteneurId) {
                // Si aucun conteneur n'est spécifié dans la requête

                // 1. Vérifier si le colis a déjà un conteneur assigné
                if ($ancienConteneurId) {
                    $ancienConteneur = Conteneur::find($ancienConteneurId);

                    if ($ancienConteneur) {
                        Log::info('Ancien conteneur trouvé:', [
                            'id' => $ancienConteneur->id,
                            'type' => $ancienConteneur->type_conteneur,
                            'statut' => $ancienConteneur->statut,
                            'agence_id' => $ancienConteneur->agence_id,
                            'name' => $ancienConteneur->name_conteneur
                        ]);

                        // Vérifier que l'ancien conteneur appartient à la même agence que le colis
                        if ($ancienConteneur->agence_id !== $agenceColisId) {
                            Log::warning('Ancien conteneur appartient à une autre agence', [
                                'conteneur_agence_id' => $ancienConteneur->agence_id,
                                'colis_agence_id' => $agenceColisId
                            ]);

                            // Chercher un conteneur de l'agence du colis
                            $conteneurAgenceColis = $this->trouverOuCreerConteneurAgence($colis, $agenceColisId, $typeConteneurRequis);
                            $conteneurId = $conteneurAgenceColis->id;
                        }
                        // Si le conteneur précédent est OUVERT, du bon type et de la bonne agence
                        else if (
                            $ancienConteneur->statut === 'ouvert' &&
                            $ancienConteneur->type_conteneur === $typeConteneurRequis &&
                            $ancienConteneur->agence_id === $agenceColisId
                        ) {
                            $conteneurId = $ancienConteneurId;
                            Log::info('Utilisation du conteneur précédent ouvert, bon type et bonne agence', [
                                'conteneur_id' => $conteneurId,
                                'type' => $ancienConteneur->type_conteneur,
                                'statut' => $ancienConteneur->statut,
                                'agence_id' => $ancienConteneur->agence_id
                            ]);
                        }
                        // Si le conteneur précédent est FERMÉ, mauvais type ou mauvaise agence
                        else {
                            Log::info('Ancien conteneur non utilisable, recherche d\'un conteneur ouvert du bon type et de la bonne agence', [
                                'ancien_conteneur_id' => $ancienConteneurId,
                                'ancien_type' => $ancienConteneur->type_conteneur,
                                'ancien_statut' => $ancienConteneur->statut,
                                'ancien_agence_id' => $ancienConteneur->agence_id,
                                'type_requis' => $typeConteneurRequis,
                                'agence_requise' => $agenceColisId
                            ]);

                            // Chercher un conteneur de l'agence du colis
                            $conteneurAgenceColis = $this->trouverOuCreerConteneurAgence($colis, $agenceColisId, $typeConteneurRequis);
                            $conteneurId = $conteneurAgenceColis->id;
                        }
                    } else {
                        // L'ancien conteneur n'existe plus, chercher un conteneur de l'agence du colis
                        Log::info('Ancien conteneur non trouvé (id: ' . $ancienConteneurId . '), recherche d\'un conteneur de l\'agence du colis');

                        $conteneurAgenceColis = $this->trouverOuCreerConteneurAgence($colis, $agenceColisId, $typeConteneurRequis);
                        $conteneurId = $conteneurAgenceColis->id;
                    }
                } else {
                    // Le colis n'a pas de conteneur assigné, chercher un conteneur de l'agence du colis
                    Log::info('Colis sans conteneur assigné, recherche conteneur pour agence et type:', [
                        'agence_id' => $agenceColisId,
                        'type_conteneur_requis' => $typeConteneurRequis
                    ]);

                    $conteneurAgenceColis = $this->trouverOuCreerConteneurAgence($colis, $agenceColisId, $typeConteneurRequis);
                    $conteneurId = $conteneurAgenceColis->id;
                }
            }

            // Vérifier que le conteneur est ouvert et appartient à la bonne agence
            $conteneur = Conteneur::find($conteneurId);
            if (!$conteneur) {
                Log::error('Conteneur non trouvé', ['conteneur_id' => $conteneurId]);
                return response()->json([
                    'success' => false,
                    'message' => '❌ Conteneur non trouvé'
                ], 404);
            }

            // Vérifier que le conteneur appartient à l'agence du colis
            if ($conteneur->agence_id !== $agenceColisId) {
                Log::warning('Tentative d\'utilisation d\'un conteneur d\'une autre agence', [
                    'conteneur_id' => $conteneurId,
                    'conteneur_agence_id' => $conteneur->agence_id,
                    'colis_agence_id' => $agenceColisId
                ]);

                // Chercher un conteneur de l'agence du colis
                $conteneurAgenceColis = $this->trouverOuCreerConteneurAgence($colis, $agenceColisId, $typeConteneurRequis);
                $conteneurId = $conteneurAgenceColis->id;
                $conteneur = $conteneurAgenceColis;
            }

            // Vérifier que le conteneur est ouvert
            if ($conteneur->statut !== 'ouvert') {
                Log::warning('Conteneur fermé tenté d\'être utilisé', [
                    'conteneur_id' => $conteneurId,
                    'statut' => $conteneur->statut,
                    'name' => $conteneur->name_conteneur,
                    'agence_id' => $conteneur->agence_id
                ]);

                // Chercher un conteneur ouvert de la même agence et du même type
                $conteneurOuvertAgence = Conteneur::where('statut', 'ouvert')
                    ->where('type_conteneur', $conteneur->type_conteneur)
                    ->where('agence_id', $agenceColisId)
                    ->first();

                if ($conteneurOuvertAgence) {
                    $conteneurId = $conteneurOuvertAgence->id;
                    $conteneur = $conteneurOuvertAgence;
                    Log::info('Conteneur remplacé par un ouvert de la même agence et type', [
                        'nouveau_conteneur_id' => $conteneurId,
                        'type' => $conteneur->type_conteneur,
                        'agence_id' => $conteneur->agence_id
                    ]);
                } else {
                    // Créer un nouveau conteneur pour l'agence
                    $nouveauConteneur = $this->creerNouveauConteneur($colis, $typeConteneurRequis, $agenceColisId);
                    $conteneurId = $nouveauConteneur->id;
                    $conteneur = $nouveauConteneur;
                    Log::info('Nouveau conteneur créé pour l\'agence (ancien fermé)', [
                        'conteneur_id' => $conteneurId,
                        'type' => $conteneur->type_conteneur,
                        'agence_id' => $conteneur->agence_id
                    ]);
                }
            }

            // Vérifier que le conteneur est du bon type
            if ($conteneur->type_conteneur !== $typeConteneurRequis) {
                Log::warning('Type de conteneur incorrect', [
                    'conteneur_type' => $conteneur->type_conteneur,
                    'type_requis' => $typeConteneurRequis
                ]);

                // Chercher un conteneur ouvert du bon type et de la bonne agence
                $conteneurCorrectType = Conteneur::where('statut', 'ouvert')
                    ->where('type_conteneur', $typeConteneurRequis)
                    ->where('agence_id', $agenceColisId)
                    ->first();

                if ($conteneurCorrectType) {
                    $conteneurId = $conteneurCorrectType->id;
                    $conteneur = $conteneurCorrectType;
                    Log::info('Conteneur corrigé vers le bon type (même agence)', [
                        'nouveau_conteneur_id' => $conteneurId,
                        'type' => $conteneur->type_conteneur,
                        'name' => $conteneur->name_conteneur,
                        'agence_id' => $conteneur->agence_id
                    ]);
                } else {
                    // Créer un nouveau conteneur du bon type pour l'agence
                    $nouveauConteneur = $this->creerNouveauConteneur($colis, $typeConteneurRequis, $agenceColisId);
                    $conteneurId = $nouveauConteneur->id;
                    $conteneur = $nouveauConteneur;
                    Log::info('Nouveau conteneur créé pour type correct et agence', [
                        'conteneur_id' => $conteneurId,
                        'type' => $conteneur->type_conteneur,
                        'agence_id' => $conteneur->agence_id
                    ]);
                }
            }

            // Vérifier si l'unité était déjà dans un autre conteneur
            $conteneurPrecedent = null;
            if ($ancienConteneurId && $ancienConteneurId != $conteneurId) {
                $conteneurPrecedent = Conteneur::find($ancienConteneurId);
                if ($conteneurPrecedent) {
                    Log::info('Colis transféré d\'un autre conteneur', [
                        'ancien_conteneur_id' => $ancienConteneurId,
                        'ancien_type' => $conteneurPrecedent->type_conteneur,
                        'ancien_statut' => $conteneurPrecedent->statut,
                        'ancien_agence_id' => $conteneurPrecedent->agence_id,
                        'nouveau_conteneur_id' => $conteneurId,
                        'nouveau_type' => $conteneur->type_conteneur,
                        'nouveau_statut' => $conteneur->statut,
                        'nouveau_agence_id' => $conteneur->agence_id
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
                            'conteneur_id' => $colis->conteneur_id,
                            'type_conteneur' => $conteneur->type_conteneur ?? null,
                            'agence_id' => $colis->agence_expedition_id
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

            // Mise à jour de l'unité individuelle
            $statutsIndividuels[$qrCode]['statut'] = 'charge';
            $statutsIndividuels[$qrCode]['localisation_actuelle'] = 'Conteneur #' . $conteneurId . ' (' . $conteneur->type_conteneur . ' - Agence ' . $conteneur->agence_id . ')';
            $statutsIndividuels[$qrCode]['date_modification'] = now()->toDateTimeString();
            $statutsIndividuels[$qrCode]['notes'] = 'Chargé dans le conteneur #' . $conteneurId . ' (' . $conteneur->type_conteneur . ') de l\'agence ' . $conteneur->agence_id . ' le ' . now()->format('d/m/Y H:i');

            // Ajouter à l'historique
            $historiqueEntry = [
                'statut' => 'charge',
                'date' => now()->toDateTimeString(),
                'localisation' => 'Conteneur #' . $conteneurId,
                'conteneur_type' => $conteneur->type_conteneur,
                'agence_id' => $conteneur->agence_id,
                'notes' => 'Chargé dans le conteneur #' . $conteneurId . ' (' . $conteneur->type_conteneur . ') - Agence ' . $conteneur->agence_id
            ];

            if ($ancienConteneurId && $ancienConteneurId != $conteneurId && $conteneurPrecedent) {
                $historiqueEntry['transfert'] = 'Transfert depuis conteneur #' . $ancienConteneurId . ' (' . $conteneurPrecedent->type_conteneur . ' - Agence ' . $conteneurPrecedent->agence_id . ')';
            }

            $statutsIndividuels[$qrCode]['historique'][] = $historiqueEntry;

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
                    'conteneur_id' => $conteneurId,
                    'type_conteneur' => $conteneur->type_conteneur,
                    'agence_id' => $conteneur->agence_id
                ]);
            } else {
                Log::info('Progression du chargement', [
                    'colis_id' => $colis->id,
                    'conteneur_id' => $conteneurId,
                    'type_conteneur' => $conteneur->type_conteneur,
                    'agence_id' => $conteneur->agence_id,
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
                'conteneur_name' => $conteneur->name_conteneur,
                'type_conteneur' => $conteneur->type_conteneur,
                'conteneur_statut' => $conteneur->statut,
                'conteneur_agence_id' => $conteneur->agence_id,
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
                    '✅ Unité chargée avec succès dans ' . $conteneur->type_conteneur . ' "' . $conteneur->name_conteneur . '" de l\'agence ' . $conteneur->agence_id . '!',
                'colis' => [
                    'id' => $colis->id,
                    'reference_colis' => $colis->reference_colis,
                    'statut' => $colis->statut,
                    'conteneur_id' => $colis->conteneur_id,
                    'type_conteneur' => $conteneur->type_conteneur,
                    'agence_id' => $conteneur->agence_id,
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
                    'localisation' => 'Conteneur #' . $conteneurId . ' (' . $conteneur->type_conteneur . ') - Agence ' . $conteneur->agence_id
                ],
                'conteneur' => [
                    'id' => $conteneurId,
                    'name' => $conteneur->name_conteneur,
                    'numero' => $conteneur->numero_conteneur,
                    'type' => $conteneur->type_conteneur,
                    'statut' => $conteneur->statut,
                    'agence_id' => $conteneur->agence_id
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur scan QR code charge: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());

            return response()->json([
                'success' => false,
                'message' => '❌ Erreur lors du traitement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Trouver ou créer un conteneur pour une agence spécifique
     */
    private function trouverOuCreerConteneurAgence($colis, $agenceId, $typeConteneur)
    {
        // Chercher d'abord un conteneur ouvert du bon type et de la bonne agence
        $conteneurExistant = Conteneur::where('statut', 'ouvert')
            ->where('type_conteneur', $typeConteneur)
            ->where('agence_id', $agenceId)
            ->first();

        if ($conteneurExistant) {
            Log::info('Conteneur existant trouvé pour l\'agence', [
                'conteneur_id' => $conteneurExistant->id,
                'type' => $conteneurExistant->type_conteneur,
                'agence_id' => $conteneurExistant->agence_id,
                'statut' => $conteneurExistant->statut
            ]);
            return $conteneurExistant;
        }

        // Si aucun conteneur ouvert n'existe, créer un nouveau pour cette agence
        Log::info('Aucun conteneur ouvert trouvé pour l\'agence, création d\'un nouveau', [
            'agence_id' => $agenceId,
            'type_conteneur' => $typeConteneur
        ]);

        return $this->creerNouveauConteneur($colis, $typeConteneur, $agenceId);
    }

    /**
     * Créer un nouveau conteneur basé sur le type spécifié et l'agence
     */
    private function creerNouveauConteneur($colis, $typeConteneur, $agenceId)
    {
        // Récupérer le nom de l'agence d'expédition du colis
        $agenceNom = $colis->agence_expedition;

        // Compter le nombre de conteneurs existants pour cette agence et ce type
        $count = Conteneur::where('agence_id', $agenceId)
            ->where('type_conteneur', $typeConteneur)
            ->count();

        $numero = $count + 1;

        // Générer un nom de conteneur
        $prefix = $typeConteneur === 'Ballon' ? 'BAL' : 'CTN';
        $nameConteneur = $agenceNom . ' - ' . $typeConteneur . ' ' . $numero;

        $nouveauConteneur = Conteneur::create([
            'name_conteneur' => $nameConteneur,
            'type_conteneur' => $typeConteneur,
            'statut' => 'ouvert',
            'agence_id' => $agenceId,
            'numero_conteneur' => $prefix . '-' . str_pad($agenceId, 3, '0', STR_PAD_LEFT) . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT)
        ]);

        Log::info('Nouveau conteneur créé pour agence', [
            'id' => $nouveauConteneur->id,
            'name' => $nouveauConteneur->name_conteneur,
            'type' => $typeConteneur,
            'statut' => $nouveauConteneur->statut,
            'agence_id' => $agenceId,
            'agence_nom' => $agenceNom
        ]);

        return $nouveauConteneur;
    }

    /**
     * Déterminer le type de conteneur basé sur le mode de transit
     */
    private function determinerTypeConteneur($modeTransit)
    {
        $modeTransit = strtolower(trim($modeTransit));

        // Logique pour déterminer le type de conteneur
        if (in_array($modeTransit, ['express', 'rapide', 'Aerien'])) {
            return 'Ballon';
        }

        // Par défaut, retourner "Conteneur"
        return 'Conteneur';
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
