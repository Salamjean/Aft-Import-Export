<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\Colis;
use App\Models\Conteneur;
use App\Models\Devis;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgentDashboard extends Controller
{
    
    public function dashboard(Request $request)
    {
        // Récupérer l'agent connecté et son agence
        $agent = Auth::guard('agent')->user();
        $agenceId = $agent->agence_id;

        // Statistiques principales filtrées par agence
        $stats = [
            'total_colis' => Colis::where('agence_expedition_id', $agenceId)->count(),
            'colis_valides' => Colis::where('agence_expedition_id', $agenceId)->where('statut', 'valide')->count(),
            'colis_livres' => Colis::where('agence_expedition_id', $agenceId)->where('statut', 'livre')->count(),
            'colis_annules' => Colis::where('agence_expedition_id', $agenceId)->where('statut', 'annule')->count(),
            'conteneurs_ouverts' => Conteneur::where('statut', 'ouvert')->count(),
            
            // Statistiques des devis
            'devis_en_attente' => Devis::where('agence_destination_id', $agenceId)->where('statut', 'en_attente')->where('montant_devis', null)->count(),
            'devis_confirme' => Devis::where('agence_destination_id', $agenceId)->where('statut', 'en_attente')->where('montant_devis', '!=', null)->count(),
            'devis_traites' => Devis::where('agence_destination_id', $agenceId)->where('statut', 'traite')->count(),
            'devis_annules' => Devis::where('agence_destination_id', $agenceId)->where('statut', 'annule')->count(),
            'total_devis' => Devis::where('agence_destination_id', $agenceId)->count(),
            
            'colis_payes' => Colis::where('agence_expedition_id', $agenceId)->where('statut_paiement', 'totalement_paye')->count(),
            'colis_en_attente_paiement' => Colis::where('agence_expedition_id', $agenceId)->where('statut_paiement', 'non_paye')->count(),
        ];

        // Derniers colis de l'agence
        $recentColis = Colis::where('agence_expedition_id', $agenceId)
            ->where('statut', 'valide')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Derniers devis
        $recentDevis = Devis::where('agence_destination_id', $agenceId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Statistiques mensuelles pour le graphique (filtrées par agence)
        $monthlyStats = $this->getMonthlyStats($agenceId);

        // Informations de l'agence
        $agence = Agence::find($agenceId);

        return view('agent.dashboard', compact(
            'stats', 
            'recentColis', 
            'recentDevis',
            'monthlyStats',
            'agence',
            'agent'
        ));
    }

    private function getMonthlyStats($agenceId)
    {
        $currentYear = date('Y');
        
        $monthlyData = Colis::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN statut = "valide" THEN 1 ELSE 0 END) as valides'),
            DB::raw('SUM(CASE WHEN statut = "livre" THEN 1 ELSE 0 END) as livres')
        )
        ->where('agence_expedition_id', $agenceId)
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

    public function getChartData(Request $request)
    {
        $agent = Auth::guard('agent')->user();
        $agenceId = $agent->agence_id;
        
        $period = $request->get('period', 'monthly');
        
        if ($period === 'weekly') {
            $data = $this->getWeeklyStats($agenceId);
        } else {
            $data = $this->getMonthlyStats($agenceId);
        }

        return response()->json($data);
    }

    private function getWeeklyStats($agenceId)
    {
        $weeklyData = Colis::select(
            DB::raw('WEEK(created_at) as week'),
            DB::raw('COUNT(*) as total')
        )
        ->where('agence_expedition_id', $agenceId)
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
            'valides' => $totals,
            'livres' => $totals
        ];
    }

     public function logout(){
        Auth::guard('agent')->logout();
        return redirect()->route('agent.login');
    }
}
