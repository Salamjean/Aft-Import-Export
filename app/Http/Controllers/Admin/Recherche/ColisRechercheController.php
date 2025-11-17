<?php

namespace App\Http\Controllers\Admin\Recherche;

use App\Http\Controllers\Controller;
use App\Models\Recuperation;
use Illuminate\Http\Request;

class ColisRechercheController extends Controller
{
    // Recherche de récupération par référence
    public function search(Request $request)
    {
        $reference = $request->get('reference');
        
        $recuperation = Recuperation::where('statut','termine')->where('reference', $reference)->first();
        
        if ($recuperation) {
            return response()->json([
                'success' => true,
                'recuperation' => [
                    'id' => $recuperation->id,
                    'reference' => $recuperation->reference,
                    'nature_objet' => $recuperation->nature_objet,
                    'quantite' => $recuperation->quantite,
                    'nom_concerne' => $recuperation->nom_concerne,
                    'prenom_concerne' => $recuperation->prenom_concerne,
                    'contact' => $recuperation->contact,
                    'email' => $recuperation->email,
                    // AJOUTER LES CHAMPS DESTINATAIRE
                    'nom_destinataire' => $recuperation->nom_destinataire,
                    'prenom_destinataire' => $recuperation->prenom_destinataire,
                    'email_destinataire' => $recuperation->email_destinataire,
                    'indicatif_destinataire' => $recuperation->indicatif_destinataire,
                    'contact_destinataire' => $recuperation->contact_destinataire,
                    'adresse_destinataire' => $recuperation->adresse_destinataire,
                ]
            ]);
        }
        
        return response()->json([
            'success' => false,
            'error' => 'Récupération non trouvée'
        ], 404);
    }

    // Détails d'une récupération
    public function details($id)
    {
        try {
            $recuperation = Recuperation::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $recuperation->id,
                    'reference' => $recuperation->reference,
                    'nature_objet' => $recuperation->nature_objet,
                    'quantite' => $recuperation->quantite,
                    'nom_concerne' => $recuperation->nom_concerne,
                    'prenom_concerne' => $recuperation->prenom_concerne,
                    'contact' => $recuperation->contact,
                    'email' => $recuperation->email,
                    'adresse_recuperation' => $recuperation->adresse_recuperation,
                    // AJOUTER LES CHAMPS DESTINATAIRE
                    'nom_destinataire' => $recuperation->nom_destinataire,
                    'prenom_destinataire' => $recuperation->prenom_destinataire,
                    'email_destinataire' => $recuperation->email_destinataire,
                    'indicatif_destinataire' => $recuperation->indicatif_destinataire,
                    'contact_destinataire' => $recuperation->contact_destinataire,
                    'adresse_destinataire' => $recuperation->adresse_destinataire,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Récupération non trouvée'
            ], 404);
        }
    }
}
