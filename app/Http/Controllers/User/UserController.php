<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Colis;
use App\Models\Devis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        
        // Statistiques des colis
        $totalColis = Colis::where('user_id', $user->id)->count();
        $colisValides = Colis::where('user_id', $user->id)->where('statut', 'valide')->count();
        $colisEnTransit = Colis::where('user_id', $user->id)
                            ->whereIn('statut', ['charge', 'entrepot', 'decharge'])
                            ->count();
        $colisLivre = Colis::where('user_id', $user->id)->where('statut', 'livre')->count();
        
        // Statistiques des devis
        $totalDevis = Devis::where('user_id', $user->id)->count();
        $devisEnAttente = Devis::where('user_id', $user->id)
                            ->where('statut', 'en_attente')
                            ->whereNull('montant_devis')
                            ->count();
        $devisConfirmes = Devis::where('user_id', $user->id)
                            ->whereNotNull('montant_devis')
                            ->count();
        
        // Derniers colis
        $recentColis = Colis::where('user_id', $user->id)
                        ->with(['agenceExpedition', 'agenceDestination'])
                        ->orderBy('created_at', 'desc')
                        ->take(3)
                        ->get();
        
        // Derniers devis
        $recentDevis = Devis::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->take(3)
                        ->get();

        return view('user.dashboard', compact(
            'totalColis',
            'colisValides',
            'colisEnTransit',
            'colisLivre',
            'totalDevis',
            'devisEnAttente',
            'devisConfirmes',
            'recentColis',
            'recentDevis',
            'user'
        ));
    }

    public function logout(){
        Auth::guard('web')->logout();
        return redirect()->route('login');
    }
}
