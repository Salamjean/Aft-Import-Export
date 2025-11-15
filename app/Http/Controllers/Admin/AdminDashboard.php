<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\Colis;
use App\Models\Conteneur;
use App\Models\DemandeRecuperation;
use App\Models\Devis;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Controller
{
   public function dashboard()
    {
        // Statistiques principales
        $stats = [
            'total_users' => User::count(),
            'total_colis' => Colis::count(),
            'colis_valides' => Colis::where('statut', 'valide')->count(),
            'colis_livres' => Colis::where('statut', 'livre')->count(),
            'colis_annules' => Colis::where('statut', 'annule')->count(),
            'conteneurs_ouverts' => Conteneur::where('statut', 'ouvert')->count(),
            'conteneurs_fermes' => Conteneur::where('statut', 'fermer')->count(),
            'devis_en_attente' => Devis::where('statut', 'en_attente')->where('montant_devis', null)->count(),
            'devis_traites' => Devis::where('statut', 'traite')->where('montant_devis', '!=', null)->count(),
            'demandes_recuperation_total' => DemandeRecuperation::count(),
            'demandes_recuperation_en_attente' => DemandeRecuperation::where('statut', 'en_attente')->count(),
            'demandes_recuperation_traitees' => DemandeRecuperation::where('statut', 'traite')->count(),
            'demandes_recuperation_annulees' => DemandeRecuperation::where('statut', 'annule')->count(),
        ];

        // Derniers colis
        $recentColis = Colis::with(['agenceExpedition', 'agenceDestination'])
            ->where('statut', 'valide')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Derniers utilisateurs inscrits
        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $recentDemandes = DemandeRecuperation::with('agence')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

        // Statistiques mensuelles pour le graphique
        $monthlyStats = $this->getMonthlyStats();

        // Top agences
        $topAgences = $this->getTopAgences();

        return view('admin.dashboard', compact(
            'stats', 
            'recentColis', 
            'recentUsers',
            'monthlyStats',
            'topAgences',
            'recentDemandes'
        ));
    }

    private function getMonthlyStats()
    {
        $currentYear = date('Y');
        
        $monthlyData = Colis::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN statut = "valide" THEN 1 ELSE 0 END) as valides'),
            DB::raw('SUM(CASE WHEN statut = "livre" THEN 1 ELSE 0 END) as livres')
        )
        ->whereYear('created_at', $currentYear)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $months = [];
        $totals = [];
        $valides = [];
        $livres = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthData = $monthlyData->firstWhere('month', $i);
            $months[] = DateTime::createFromFormat('!m', $i)->format('M');
            $totals[] = $monthData ? $monthData->total : 0;
            $valides[] = $monthData ? $monthData->valides : 0;
            $livres[] = $monthData ? $monthData->livres : 0;
        }

        return [
            'months' => $months,
            'totals' => $totals,
            'valides' => $valides,
            'livres' => $livres
        ];
    }

   private function getTopAgences()
    {
        // Version simple sans count
        return Agence::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function getChartData(Request $request)
    {
        $period = $request->get('period', 'monthly');
        
        if ($period === 'weekly') {
            $data = $this->getWeeklyStats();
        } else {
            $data = $this->getMonthlyStats();
        }

        return response()->json($data);
    }

    private function getWeeklyStats()
    {
        // Implémentation pour les stats hebdomadaires
        $weeklyData = Colis::select(
            DB::raw('WEEK(created_at) as week'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('week')
        ->orderBy('week')
        ->get();

        $weeks = [];
        $totals = [];

        foreach ($weeklyData as $data) {
            $weeks[] = 'Sem ' . $data->week;
            $totals[] = $data->total;
        }

        return [
            'months' => $weeks,
            'totals' => $totals,
            'valides' => $totals, // À adapter selon vos besoins
            'livres' => $totals   // À adapter selon vos besoins
        ];
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

}
