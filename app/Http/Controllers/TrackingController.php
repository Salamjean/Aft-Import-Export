<?php

namespace App\Http\Controllers;

use App\Models\Colis;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function trackColis($reference)
    {
        try {
            $colis = Colis::where('reference_colis', $reference)
                ->orWhere('code_colis', 'LIKE', '%' . $reference . '%')
                ->first();

            if (!$colis) {
                return response()->json([
                    'error' => 'Colis non trouvé. Vérifiez votre référence.'
                ], 404);
            }

            return response()->json([
                'reference_colis' => $colis->reference_colis,
                'code_colis' => $colis->code_colis,
                'statut' => $colis->statut,
                'name_expediteur' => $colis->name_expediteur,
                'prenom_expediteur' => $colis->prenom_expediteur,
                'name_destinataire' => $colis->name_destinataire,
                'prenom_destinataire' => $colis->prenom_destinataire,
                'agence_expedition' => $colis->agence_expedition,
                'agence_destination' => $colis->agence_destination,
                'mode_transit' => $colis->mode_transit,
                'created_at' => $colis->created_at->format('d/m/Y H:i'),
                'updated_at' => $colis->updated_at->format('d/m/Y H:i'),
                'statuts_individuels' => json_decode($colis->statuts_individuels, true)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la recherche du colis'
            ], 500);
        }
    }
}
