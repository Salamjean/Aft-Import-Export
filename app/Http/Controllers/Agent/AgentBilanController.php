<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Colis;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AgentBilanController extends Controller
{
    public function index(Request $request)
    {
        $agent = Auth::guard('agent')->user();
        $agence = $agent->agence;
        $devise = $agence->devise ?? 'XOF';
        $route_prefix = 'agent.bilan_financier';

        $dateDebut = $request->input('date_debut');
        $dateFin = $request->input('date_fin');

        // Statistiques de l'agence (basées sur les encaissements effectués par l'agence)
        $statsAgence = $this->getStatsAgence($agence, $dateDebut, $dateFin);

        // Statistiques mensuelles pour le graphique
        $statsGraphique = $this->getStatsGraphique($agence, $dateDebut, $dateFin);

        return view('agent.bilan-financier.index', compact(
            'statsAgence',
            'statsGraphique',
            'agence',
            'devise',
            'route_prefix',
            'dateDebut',
            'dateFin'
        ));
    }

    public function historique(Request $request)
    {
        $agent = Auth::guard('agent')->user();
        $route_prefix = 'agent.bilan_financier';

        // Uniquement les paiements encaissés par l'agence de cet agent
        $paiements = Paiement::with(['colis.agenceExpedition'])
            ->where('agence_id', $agent->agence_id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('agent.bilan-financier.historique', compact('paiements', 'route_prefix'));
    }

    private function getStatsAgence($agence, $dateDebut = null, $dateFin = null)
    {
        // 1. Volume d'activité (Colis expédiés par cette agence)
        $queryColis = Colis::where('agence_expedition_id', $agence->id);
        if ($dateDebut) $queryColis->whereDate('created_at', '>=', $dateDebut);
        if ($dateFin) $queryColis->whereDate('created_at', '<=', $dateFin);
        
        $totalColis = (clone $queryColis)->count();
        $montantTotalFacture = (clone $queryColis)->sum('montant_total') ?? 0;

        // 2. Encaissements Réels (Paiements perçus par cette agence)
        $queryPaiements = Paiement::where('agence_id', $agence->id);
        if ($dateDebut) $queryPaiements->whereDate('created_at', '>=', $dateDebut);
        if ($dateFin) $queryPaiements->whereDate('created_at', '<=', $dateFin);

        $totalEncaisse = (clone $queryPaiements)->sum('montant') ?? 0;

        return [
            'total_colis' => $totalColis,
            'montant_total' => $montantTotalFacture,
            'montant_paye' => $totalEncaisse,
            'montant_impaye' => max(0, $montantTotalFacture - $totalEncaisse),
            'taux_recouvrement' => $montantTotalFacture > 0 ? round(($totalEncaisse / $montantTotalFacture) * 100, 2) : 100,

            'totalement_payes' => (clone $queryColis)->where('statut_paiement', 'totalement_paye')->count(),
            'partiellement_payes' => (clone $queryColis)->where('statut_paiement', 'partiellement_paye')->count(),
            'non_payes' => (clone $queryColis)->where('statut_paiement', 'non_paye')->count(),

            // Répartition par méthode
            'montant_especes' => (clone $queryPaiements)->where('methode_paiement', 'espece')->sum('montant') ?? 0,
            'montant_virement' => (clone $queryPaiements)->where('methode_paiement', 'virement_bancaire')->sum('montant') ?? 0,
            'montant_cheque' => (clone $queryPaiements)->where('methode_paiement', 'cheque')->sum('montant') ?? 0,
            'montant_mobile_money' => (clone $queryPaiements)->where('methode_paiement', 'mobile_money')->sum('montant') ?? 0,
            'montant_livraison' => (clone $queryPaiements)->where('methode_paiement', 'livraison')->sum('montant') ?? 0,
        ];
    }

    private function getStatsGraphique($agence, $dateDebut = null, $dateFin = null)
    {
        $yearToUse = $dateDebut ? date('Y', strtotime($dateDebut)) : date('Y');

        // Encaissements par mois
        $stats = Paiement::where('agence_id', $agence->id)
            ->whereYear('created_at', $yearToUse)
            ->selectRaw('MONTH(created_at) as month, SUM(montant) as total')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $labels = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthName = \DateTime::createFromFormat('!m', $i)->format('M');
            $labels[] = $monthName;
            
            $found = $stats->firstWhere('month', $i);
            $data[] = $found ? (int) $found->total : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
