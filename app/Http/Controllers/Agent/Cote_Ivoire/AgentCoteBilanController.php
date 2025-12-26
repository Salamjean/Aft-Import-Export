<?php

namespace App\Http\Controllers\Agent\Cote_Ivoire;

use App\Http\Controllers\Controller;
use App\Models\Colis;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AgentCoteBilanController extends Controller
{
    public function index()
    {
        $agent = Auth::guard('agent')->user();
        $agence = $agent->agence;
        $devise = $agence->devise ?? 'XOF';

        // Statistiques de l'agence uniquement
        $statsAgence = $this->getStatsAgence($agence);

        // Statistiques mensuelles pour l'agence
        $statsGraphique = $this->getStatsGraphique($agence);
        $route_prefix = 'agent.cote.bilan_financier';

        return view('ivoire.bilan-financier.index', compact(
            'statsAgence',
            'statsGraphique',
            'agence',
            'devise',
            'route_prefix'
        ));
    }

    public function historique()
    {
        $agent = Auth::guard('agent')->user();
        $route_prefix = 'agent.cote.bilan_financier';

        // Uniquement les paiements effectués par cet agent
        $paiements = Paiement::with(['colis.agenceExpedition'])
            ->where('agent_id', $agent->id)
            ->where('agent_type', 'agent')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('ivoire.bilan-financier.historique', compact('paiements', 'route_prefix'));
    }

    private function getStatsAgence($agence)
    {
        // Tous les colis où cette agence est impliquée
        $queryColis = Colis::where('agence_expedition_id', $agence->id)
            ->orWhere('agence_destination_id', $agence->id);

        // Somme des encaissements effectués par les agents de cette agence
        $paiementsAgence = Paiement::whereHas('agent', function ($q) use ($agence) {
            $q->where('agence_id', $agence->id);
        })->where('agent_type', 'agent');

        $montantEncaisseAgence = (clone $paiementsAgence)->sum('montant') ?? 0;
        $montantTotalColis = (clone $queryColis)->sum('montant_total') ?? 0;
        $montantImpayeGlobal = (clone $queryColis)->sum('reste_a_payer') ?? 0;

        return [
            'total_colis' => (clone $queryColis)->count(),
            'montant_total' => $montantTotalColis,
            'montant_paye' => $montantEncaisseAgence,
            'montant_impaye' => $montantImpayeGlobal,
            'taux_recouvrement' => $montantTotalColis > 0 ? round(($montantEncaisseAgence / $montantTotalColis) * 100, 2) : 0,

            'totalement_payes' => (clone $queryColis)->where('statut_paiement', 'totalement_paye')->count(),
            'partiellement_payes' => (clone $queryColis)->where('statut_paiement', 'partiellement_paye')->count(),
            'non_payes' => (clone $queryColis)->where('statut_paiement', 'non_paye')->count(),

            // Détails des méthodes de paiement pour CETTE agence
            'montant_especes' => (clone $paiementsAgence)->where('methode_paiement', 'espece')->sum('montant') ?? 0,
            'montant_virement' => (clone $paiementsAgence)->where('methode_paiement', 'virement')->sum('montant') ?? 0,
            'montant_cheque' => (clone $paiementsAgence)->where('methode_paiement', 'cheque')->sum('montant') ?? 0,
            'montant_mobile_money' => (clone $paiementsAgence)->where('methode_paiement', 'mobile_money')->sum('montant') ?? 0,
            'montant_livraison' => (clone $paiementsAgence)->where('methode_paiement', 'virement_livraison')->sum('montant') ?? 0,
        ];
    }

    private function getStatsGraphique($agence)
    {
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();

        $stats = Paiement::whereHas('agent', function ($q) use ($agence) {
            $q->where('agence_id', $agence->id);
        })
            ->where('agent_type', 'agent')
            ->where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(montant) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = (int) $date->format('m');
            $year = (int) $date->format('Y');

            $labels[] = $date->translatedFormat('M');
            $found = $stats->where('month', $month)->where('year', $year)->first();
            $data[] = $found ? (int) $found->total : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
