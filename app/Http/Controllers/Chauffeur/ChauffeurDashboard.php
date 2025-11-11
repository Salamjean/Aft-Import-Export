<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use App\Models\Livraison;
use App\Models\Recuperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChauffeurDashboard extends Controller
{
    public function dashboard(Request $request)
    {
        $chauffeurId = Auth::guard('chauffeur')->user()->id;
        
        // Statistiques des programmes en cours
        $depotsEnCours = Depot::where('chauffeur_id', $chauffeurId)
            ->where('statut', 'en_cours')
            ->count();
        
        $recuperationsEnCours = Recuperation::where('chauffeur_id', $chauffeurId)
            ->where('statut', 'en_cours')
            ->count();
        
        $livraisonsEnCours = Livraison::where('chauffeur_id', $chauffeurId)
            ->where('statut', 'en_cours')
            ->count();
        
        $totalEnCours = $depotsEnCours + $recuperationsEnCours + $livraisonsEnCours;
        
        // Statistiques des programmes terminés
        $depotsTermines = Depot::where('chauffeur_id', $chauffeurId)
            ->where('statut', 'termine')
            ->count();
        
        $recuperationsTermines = Recuperation::where('chauffeur_id', $chauffeurId)
            ->where('statut', 'termine')
            ->count();
        
        $livraisonsTermines = Livraison::where('chauffeur_id', $chauffeurId)
            ->where('statut', 'termine')
            ->count();
        
        $totalTermines = $depotsTermines + $recuperationsTermines + $livraisonsTermines;
        
        // Programmes du jour
        $today = now()->format('Y-m-d');
        
        $depotsAujourdhui = Depot::where('chauffeur_id', $chauffeurId)
            ->where('statut', 'en_cours')
            ->whereDate('date_depot', $today)
            ->count();
        
        $recuperationsAujourdhui = Recuperation::where('chauffeur_id', $chauffeurId)
            ->where('statut', 'en_cours')
            ->whereDate('date_recuperation', $today)
            ->count();
        
        $livraisonsAujourdhui = Livraison::where('chauffeur_id', $chauffeurId)
            ->where('statut', 'en_cours')
            ->whereDate('date_livraison', $today)
            ->count();
        
        $totalAujourdhui = $depotsAujourdhui + $recuperationsAujourdhui + $livraisonsAujourdhui;
        
        // Derniers programmes
        $depotsRecents = Depot::where('chauffeur_id', $chauffeurId)
            ->where('statut', 'en_cours')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($depot) {
                $depot->type = 'depot';
                $depot->icon = 'fa-box';
                $depot->color = 'warning';
                return $depot;
            });
        
        $recuperationsRecentes = Recuperation::where('chauffeur_id', $chauffeurId)
            ->where('statut', 'en_cours')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($recuperation) {
                $recuperation->type = 'recuperation';
                $recuperation->icon = 'fa-undo';
                $recuperation->color = 'info';
                return $recuperation;
            });
        
        $livraisonsRecentes = Livraison::where('chauffeur_id', $chauffeurId)
            ->where('statut', 'en_cours')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($livraison) {
                $livraison->type = 'livraison';
                $livraison->icon = 'fa-truck';
                $livraison->color = 'success';
                return $livraison;
            });
        
        // Fusionner les programmes récents
        $programmesRecents = $depotsRecents->merge($recuperationsRecentes)
            ->merge($livraisonsRecentes)
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        return view('chauffeur.dashboard', compact(
            'totalEnCours',
            'totalTermines',
            'totalAujourdhui',
            'depotsEnCours',
            'recuperationsEnCours',
            'livraisonsEnCours',
            'programmesRecents',
            'chauffeurId'
        ));
    }

    public function terminerDepot($id)
    {
        try {
            $depot = Depot::findOrFail($id);
            
            // Vérifier que le dépôt appartient au chauffeur connecté
            if ($depot->chauffeur_id !== Auth::guard('chauffeur')->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à modifier ce dépôt'
                ], 403);
            }
            
            // Mettre à jour le statut
            $depot->update([
                'statut' => 'termine',
                'date_terminaison' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Dépôt marqué comme terminé avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout(){
        Auth::guard('chauffeur')->logout();
        return redirect()->route('chauffeur.login');
    }
}
