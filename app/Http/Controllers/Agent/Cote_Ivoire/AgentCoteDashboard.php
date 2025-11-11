<?php

namespace App\Http\Controllers\Agent\Cote_Ivoire;

use App\Http\Controllers\Controller;
use App\Models\Bateau;
use App\Models\Colis;
use App\Models\Conteneur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentCoteDashboard extends Controller
{
    public function dashboard()
{
    $agent = Auth::guard('agent')->user();
    $agenceId = $agent->agence_id;

    // Statistiques des colis
    $totalColis = Colis::where('agence_destination_id', $agenceId)
                      ->orWhere('agence_expedition_id', $agenceId)
                      ->count();

    $colisLivre = Colis::where(function($query) use ($agenceId) {
            $query->where('agence_destination_id', $agenceId)
                  ->orWhere('agence_expedition_id', $agenceId);
        })
        ->where('statut', 'livre')
        ->count();

    $colisEnTransit = Colis::where(function($query) use ($agenceId) {
            $query->where('agence_destination_id', $agenceId)
                  ->orWhere('agence_expedition_id', $agenceId);
        })
         ->where('statut', '!=','livre')
        ->count();

    $colisValide = Colis::where(function($query) use ($agenceId) {
            $query->where('agence_destination_id', $agenceId)
                  ->orWhere('agence_expedition_id', $agenceId);
        })
        ->where('statut', 'valide')
        ->count();

    // Données pour le graphique de progression (7 derniers jours)
    $deliveryData = [];
    $transitData = [];
    $labels = [];
    
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i);
        $labels[] = $date->format('D');
        
        // Colis livrés ce jour
        $deliveryData[] = Colis::where(function($query) use ($agenceId) {
                $query->where('agence_destination_id', $agenceId)
                      ->orWhere('agence_expedition_id', $agenceId);
            })
            ->where('statut', 'livre')
            ->whereDate('updated_at', $date->format('Y-m-d'))
            ->count();
            
        // Colis en transit ce jour
        $transitData[] = Colis::where(function($query) use ($agenceId) {
                $query->where('agence_destination_id', $agenceId)
                      ->orWhere('agence_expedition_id', $agenceId);
            })
             ->where('statut', '!=','livre')
            ->whereDate('updated_at', $date->format('Y-m-d'))
            ->count();
    }

    // Données pour le graphique circulaire
    $pieData = [
        $colisLivre,
        $colisEnTransit,
        $colisValide
    ];

    // Conteneurs actifs
    $conteneursActifs = Conteneur::where('statut', 'fermer')
        ->whereHas('colis', function($query) use ($agenceId) {
            $query->where('agence_destination_id', $agenceId);
        })
        ->count();

    // Engins récents (Bateaux)
    $recentColis = Bateau::with(['conteneur'])
        ->where('agence_id', $agenceId)
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    return view('ivoire.dashboard', compact(
        'totalColis',
        'colisLivre', 
        'colisEnTransit',
        'colisValide',
        'conteneursActifs',
        'recentColis',
        'deliveryData',
        'transitData',
        'labels',
        'pieData'
    ));
}

    public function index()
    {
        $agent = Auth::guard('agent')->user();
        $agenceId = $agent->agence_id;

        $planifications = Bateau::with(['conteneur', 'agence'])
            ->where('agence_id', $agenceId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('ivoire.bateau.index', compact('planifications'));
    }

    public function showConteneur($id)
    {
        // Récupérer le bateau avec les relations conteneur et agence
        $bateau = Bateau::with(['conteneur', 'agence'])->findOrFail($id);
        
        // Vérifier si le bateau a un conteneur associé
        if (!$bateau->conteneur) {
            return redirect()->back()->with('error', 'Aucun conteneur associé à ce bateau.');
        }

        return view('ivoire.bateau.ouvrir', compact('bateau'));
    }

    public function showColis(Request $request, $conteneurId)
    {
        try {
            $conteneur = Conteneur::with(['colis' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])->findOrFail($conteneurId);

            return view('ivoire.conteneur.colis', compact('conteneur'));
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('conteneur.history')
                ->with('error', 'Conteneur non trouvé.');
        }
    }

    public function history()
    {
        // Récupérer l'agent connecté et son agence
        $agent = Auth::guard('agent')->user();
        
        if (!$agent || !$agent->agence_id) {
            // Si l'agent n'a pas d'agence, retourner une collection vide
            $conteneurs = collect()->paginate(10);
            return view('agent.conteneur.history', compact('conteneurs'));
        }

        // Filtrer les conteneurs fermés qui contiennent des colis de l'agence de destination de l'agent
        $conteneurs = Conteneur::where('statut', 'fermer')
            ->whereHas('colis', function($query) use ($agent) {
                $query->where('agence_destination_id', $agent->agence_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('ivoire.conteneur.history', compact('conteneurs'));
    }


    // public function history()
    // {
    //     // Récupérer l'agent connecté et son agence
    //     $agent = Auth::guard('agent')->user();
        
    //     if (!$agent || !$agent->agence_id) {
    //         // Si l'agent n'a pas d'agence, retourner une collection vide
    //         $conteneurs = collect()->paginate(10);
    //         return view('agent.conteneur.history', compact('conteneurs'));
    //     }

    //     // Filtrer les conteneurs ouverts de l'agence de l'agent connecté
    //     $conteneurs = Conteneur::where('statut', 'fermer')
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(10);
        
    //     return view('ivoire.conteneur.history', compact('conteneurs'));
    // }

    public function colis(Request $request)
    {
        $query = Colis::with(['agenceExpedition', 'agenceDestination', 'conteneur'])
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
        if ($request->has('status') && !empty($request->status)) {
            $query->where('statut', $request->status);
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

        return view('ivoire.colis.index', compact('colis'));
    }
}
