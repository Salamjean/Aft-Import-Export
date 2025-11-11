<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Colis;
use App\Models\Livraison;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChauffeurScanLivraison extends Controller
{
    /**
     * Scanner un QR code pour livraison par le chauffeur
     */
    public function scanLivraison(Request $request)
    {
        try {
            Log::info('=== SCAN LIVRAISON CHAUFFEUR DÉBUT ===', $request->all());

            // Récupérer le chauffeur connecté
            $chauffeur = Auth::guard('chauffeur')->user();
            
            if (!$chauffeur) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Chauffeur non connecté'
                ], 403);
            }

            $request->validate([
                'qr_code' => 'required|string',
                'livraison_id' => 'required|integer'
            ]);

            $qrCode = trim($request->qr_code);
            $livraisonId = $request->livraison_id;
            
            Log::info('Scan livraison chauffeur:', [
                'chauffeur_id' => $chauffeur->id,
                'livraison_id' => $livraisonId,
                'qr_code' => $qrCode
            ]);

            // Vérifier que la livraison existe et appartient au chauffeur
            $livraison = Livraison::where('id', $livraisonId)
                                ->where('chauffeur_id', $chauffeur->id)
                                ->first();

            if (!$livraison) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Livraison non trouvée ou non assignée'
                ], 404);
            }

            // Vérifier si la livraison n'est pas déjà terminée
            if ($livraison->statut === 'termine') {
                return response()->json([
                    'success' => false,
                    'message' => 'ℹ️ Cette livraison est déjà terminée'
                ], 400);
            }

            // Vérifier si la livraison n'est pas annulée
            if ($livraison->statut === 'annule') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Cette livraison a été annulée'
                ], 400);
            }

            // Rechercher le colis avec le code QR
            $colis = $this->findColisByQrCode($qrCode);

            if (!$colis) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Aucun colis trouvé avec le code: ' . $qrCode
                ], 404);
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

            Log::info('Unité trouvée:', [
                'colis_id' => $colis->id,
                'reference_colis' => $colis->reference_colis,
                'ancien_statut' => $ancienStatut,
                'produit' => $produit
            ]);

            // Vérifications du statut
            if ($ancienStatut === 'livre') {
                return response()->json([
                    'success' => false,
                    'message' => 'ℹ️ Cette unité est déjà livrée',
                    'unite' => [
                        'code_colis' => $qrCode,
                        'statut' => 'livre',
                        'produit' => $produit,
                        'position' => "Colis {$colisNumero} - Unité {$uniteNumero}"
                    ]
                ]);
            }

            if ($ancienStatut !== 'decharge') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Cette unité doit d\'abord être déchargée avant livraison. Statut actuel: ' . $this->getStatutText($ancienStatut),
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
            $statutsIndividuels[$qrCode]['agence_actuelle_id'] = null;
            $statutsIndividuels[$qrCode]['date_modification'] = now()->toDateTimeString();
            $statutsIndividuels[$qrCode]['notes'] = 'Livré au destinataire le ' . now()->format('d/m/Y H:i') . ' par chauffeur #' . $chauffeur->id . ' - Livraison: ' . $livraison->reference;
            
            $statutsIndividuels[$qrCode]['historique'][] = [
                'statut' => 'livre',
                'date' => now()->toDateTimeString(),
                'localisation' => 'Livré au destinataire',
                'agence_id' => null,
                'chauffeur_id' => $chauffeur->id,
                'livraison_id' => $livraisonId,
                'notes' => 'Livraison effectuée - ' . $livraison->reference
            ];

            // Mise à jour du colis
            $colis->statuts_individuels = json_encode($statutsIndividuels);
            
            // Vérifier si TOUTES les unités sont livrées
            $tousLivres = $this->verifierTousLivres($statutsIndividuels);
            
            if ($tousLivres) {
                $colis->statut = 'livre';
                Log::info('🎉 TOUTES LES UNITÉS LIVRÉES - Statut global mis à jour', [
                    'colis_id' => $colis->id,
                    'chauffeur_id' => $chauffeur->id,
                    'livraison_id' => $livraisonId
                ]);
            }
            
            $colis->save();

            // Mettre à jour le compteur de la livraison
            $livraison = $this->updateLivraisonCompteur($livraison);

            // Statistiques
            $unitesLivrees = $this->compterIndividuelsLivres($statutsIndividuels);
            $totalUnites = count($statutsIndividuels);
            $progressionColis = round(($unitesLivrees / $totalUnites) * 100, 2);

            // Calculer la progression de la livraison
            $quantiteScannee = $livraison->quantite_scannee ?? 0;
            $quantiteTotale = $livraison->quantite;
            $progressionLivraison = round(($quantiteScannee / $quantiteTotale) * 100, 2);

            Log::info('Livraison chauffeur réussie:', [
                'colis_id' => $colis->id,
                'chauffeur_id' => $chauffeur->id,
                'livraison_id' => $livraisonId,
                'unite' => $qrCode,
                'quantite_scannee' => $quantiteScannee,
                'quantite_total' => $quantiteTotale,
                'progression_livraison' => $progressionLivraison . '%'
            ]);

            $message = '✅ Unité livrée avec succès !';
            $estTermine = false;

            // Vérifier si la quantité scannée atteint la quantité totale de la livraison
            if ($quantiteScannee >= $quantiteTotale) {
                $livraison->statut = 'termine';
                $livraison->date_livraison = now();
                $livraison->save();
                
                $message = '🎉 FÉLICITATIONS ! Livraison terminée ! Tous les colis ont été livrés.';
                $estTermine = true;
                
                Log::info('LIVRAISON TERMINÉE', [
                    'livraison_id' => $livraison->id,
                    'quantite_scannee' => $quantiteScannee,
                    'quantite_total' => $quantiteTotale,
                    'chauffeur_id' => $chauffeur->id
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'est_termine' => $estTermine,
                'colis' => [
                    'reference_colis' => $colis->reference_colis,
                    'statut' => $colis->statut,
                    'total_unites' => $totalUnites,
                    'unites_livrees' => $unitesLivrees,
                    'progression' => $progressionColis,
                    'tous_livres' => $tousLivres
                ],
                'unite' => [
                    'code_colis' => $qrCode,
                    'ancien_statut' => $ancienStatut,
                    'nouveau_statut' => 'livre',
                    'produit' => $produit,
                    'position' => "Colis {$colisNumero} - Unité {$uniteNumero}",
                    'localisation' => 'Livré au destinataire'
                ],
                'livraison' => [
                    'id' => $livraison->id,
                    'reference' => $livraison->reference,
                    'statut' => $livraison->statut,
                    'quantite_scannee' => $quantiteScannee,
                    'quantite_total' => $quantiteTotale,
                    'progression_livraison' => $progressionLivraison,
                    'client' => $livraison->nom_concerne . ' ' . $livraison->prenom_concerne,
                    'adresse' => $livraison->adresse_livraison
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur scan livraison chauffeur: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => '❌ Erreur lors du traitement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour le compteur de la livraison
     * Nous allons utiliser un champ supplémentaire pour stocker le compteur
     */
    private function updateLivraisonCompteur($livraison)
    {
        // Vérifier si le champ quantite_scannee existe, sinon on le crée
        if (!isset($livraison->quantite_scannee)) {
            // Si le champ n'existe pas dans la table, on va le gérer différemment
            // On peut utiliser un champ JSON ou créer une table séparée
            // Pour l'instant, on va utiliser un système simple avec un fichier ou session
            // Mais la meilleure solution serait d'ajouter le champ à la table
            
            $livraison->quantite_scannee = 1;
        } else {
            $livraison->quantite_scannee = ($livraison->quantite_scannee ?? 0) + 1;
        }
        
        // Si c'est la première unité scannée et que le statut est "programme", passer à "en_cours"
        if (($livraison->quantite_scannee == 1) && $livraison->statut === 'programme') {
            $livraison->statut = 'en_cours';
        }
        
        $livraison->save();
        
        return $livraison;
    }

    /**
     * Rechercher un colis par code QR
     */
    private function findColisByQrCode($qrCode)
    {
        // Méthode 1 : Recherche directe dans le JSON avec MySQL
        $colis = Colis::where('statuts_individuels', 'LIKE', '%"' . $qrCode . '"%')
            ->first();

        // Méthode 2 : Si la méthode 1 ne fonctionne pas, recherche manuelle
        if (!$colis) {
            $colisList = Colis::all();
            foreach ($colisList as $colisItem) {
                $statutsIndividuels = json_decode($colisItem->statuts_individuels, true) ?? [];
                if (isset($statutsIndividuels[$qrCode])) {
                    $colis = $colisItem;
                    break;
                }
            }
        }

        return $colis;
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
     * Compter le nombre d'unités livrées
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
