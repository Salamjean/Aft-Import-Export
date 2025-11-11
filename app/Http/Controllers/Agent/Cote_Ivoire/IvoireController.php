<?php

namespace App\Http\Controllers\Agent\Cote_Ivoire;

use App\Http\Controllers\Controller;
use App\Models\Bateau;
use App\Models\Conteneur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PDF;

class IvoireController extends Controller
{
    public function showColis(Request $request, $conteneurId)
    {
        try {
            $conteneur = Conteneur::with(['colis' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])->findOrFail($conteneurId);

            return view('ivoire.conteneur.list', compact('conteneur'));
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('conteneur.history')
                ->with('error', 'Conteneur non trouvé.');
        }
    }

    public function markAsArrived($id)
    {
        Log::info("=== MARK AS ARRIVED CALLED ===");
        Log::info("Bateau ID: " . $id);
        
        try {
            $bateau = Bateau::findOrFail($id);
            Log::info("Bateau trouvé: " . $bateau->reference);
            
            $bateau->statut = 'arrive';
            $bateau->save();
            
            Log::info("Statut mis à jour avec succès");

            return response()->json([
                'success' => true, 
                'message' => 'Bateau marqué comme arrivé'
            ]);
            
        } catch (\Exception $e) {
            Log::error("Erreur markAsArrived: " . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadConteneurPDF($conteneurId)
    {
        try {
            $conteneur = Conteneur::with(['colis' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])->findOrFail($conteneurId);

            // Calculer les statistiques pour chaque colis
            foreach ($conteneur->colis as $colis) {
                // Ces propriétés devraient être définies dans votre modèle Colis
                // ou vous pouvez les calculer ici si nécessaire
            }

            $pdf = PDF::loadView('ivoire.conteneur.pdf-template', compact('conteneur'));
            
            return $pdf->download("conteneur-{$conteneur->name_conteneur}-".now()->format('Y-m-d').".pdf");
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Conteneur non trouvé'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur génération PDF: '.$e->getMessage());
            return response()->json(['error' => 'Erreur lors de la génération du PDF'], 500);
        }
    }
}
