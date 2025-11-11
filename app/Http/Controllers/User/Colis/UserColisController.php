<?php

namespace App\Http\Controllers\User\Colis;

use App\Http\Controllers\Controller;
use App\Models\Colis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class UserColisController extends Controller
{
   public function index(Request $request)
    {
        // Récupérer l'user connecté
        $user = Auth::user();
        $userId = $user->id;
        $userEmail = $user->email;
        $userContact = $user->contact; // Assurez-vous que ce champ existe

        // Filtrer uniquement les colis où l'user est l'expéditeur
        $query = Colis::with(['agenceExpedition', 'agenceDestination', 'conteneur'])
                    ->where(function($q) use ($userId, $userEmail, $userContact) {
                        // Soit l'user est l'expéditeur par user_id
                        $q->where('user_id', $userId)
                        
                        // Soit l'user est l'expéditeur par email
                        ->orWhere('email_expediteur', $userEmail)
                        
                        // Soit l'user est l'expéditeur par contact
                        ->orWhere('contact_expediteur', $userContact);
                    })
                    ->orderBy('created_at', 'desc');

        // Filtre de recherche
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference_colis', 'LIKE', "%{$search}%")
                ->orWhere('name_expediteur', 'LIKE', "%{$search}%")
                ->orWhere('name_destinataire', 'LIKE', "%{$search}%")
                ->orWhere('email_expediteur', 'LIKE', "%{$search}%")
                ->orWhere('email_destinataire', 'LIKE', "%{$search}%")
                ->orWhere('code_colis', 'LIKE', "%{$search}%");
            });
        }

        // Filtre par statut
        if ($request->has('statut') && !empty($request->statut)) {
            $query->where('statut', $request->statut);
        }

        // Filtre par mode de transit
        if ($request->has('mode_transit') && !empty($request->mode_transit)) {
            $query->where('mode_transit', $request->mode_transit);
        }

        // Filtre par statut de paiement
        if ($request->has('paiement') && !empty($request->paiement)) {
            $query->where('statut_paiement', $request->paiement);
        }

        $colis = $query->paginate(10);
        
        // Compter le nombre de tableaux (types de colis) dans le champ colis (JSON)
        $colis->getCollection()->transform(function ($item) {
            $colisData = json_decode($item->colis, true);
            $item->nombre_types_colis = is_array($colisData) ? count($colisData) : 0;
            return $item;
        });

        return view('user.colis.index', compact('colis'));
    }

    public function show($id)
    {
        try {
            $colis = Colis::with(['agenceExpedition', 'agenceDestination', 'conteneur', 'service'])
                        ->findOrFail($id);

            // Décoder les données des colis
            $colisDetails = json_decode($colis->colis, true);
            $nombreTypesColis = is_array($colisDetails) ? count($colisDetails) : 0;
            
            // Décoder les statuts individuels
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

            // Préparer les données des colis
            $colisData = [
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
                'nombre_types_colis' => $nombreTypesColis,
                'colis_details' => $colisDetails,
                'statuts_individuels' => $statutsIndividuels,
                'compteur_statuts' => $compteurStatuts,
                'total_individuels' => count($statutsIndividuels)
            ];

            return response()->json($colisData);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Colis non trouvé'
            ], 404);
        }
    }

    /**
     * Générer la facture
     */
    public function generateFacture($id)
    {
        try {
            // Récupérer le colis avec toutes les relations nécessaires
            $colis = Colis::with(['conteneur', 'agenceExpedition', 'agenceDestination', 'service'])
                          ->findOrFail($id);

            // Décoder les données JSON des colis
            $colisDetails = json_decode($colis->colis, true);
            $codesColis = json_decode($colis->code_colis, true);

            // Calculer les totaux
            $montantTotal = $colis->montant_total ?? 0;
            $montantPaye = $colis->montant_paye ?? 0;
            $devise = $colis->devise;
            $resteAPayer = $colis->reste_a_payer ?? ($montantTotal - $montantPaye);

            $data = [
                'colis' => $colis,
                'colisDetails' => $colisDetails,
                'codesColis' => $codesColis,
                'montantTotal' => $montantTotal,
                'montantPaye' => $montantPaye,
                'resteAPayer' => $resteAPayer,
                'devise' => $devise,
                'dateFacture' => now()->format('d/m/Y'),
                'numeroFacture' => 'FACT-' . $colis->reference_colis . '-' . now()->format('Ymd'),
                'entreprise' => [
                    'nom' => 'AFT IMPORT EXPORT',
                    'adresse' => '7 AVENUE LOUIS BLERIOT LA COURNEUVE 93120 France',
                    'telephone' => '+33171894551',
                    'email' => 'contact@aft-import-export.com',
                    'siret' => '81916365',
                    'tva' => 'FR86681916365'
                ]
            ];

            $action = request('action', 'preview');

            // Générer le PDF
            $pdf = PDF::loadView('agent.colis.documents.facture', $data);

            if ($action === 'download') {
                return $pdf->download('facture-' . $colis->reference_colis . '.pdf');
            }

            // Par défaut, retourner le PDF en stream pour impression/prévisualisation
            return $pdf->stream('facture-' . $colis->reference_colis . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la génération de la facture: ' . $e->getMessage());
        }
    }
}
