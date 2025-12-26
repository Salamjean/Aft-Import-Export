<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\Colis;
use App\Models\Paiement;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BilanFinancierController extends Controller
{
    public function index()
    {
        // Statistiques globales par devise
        $statsGlobales = $this->getStatsGlobales();

        // Statistiques simplifiées par agence (Encaissements Agents uniquement)
        $statsParAgence = $this->getStatsParAgence();

        // Statistiques mensuelles pour le graphique
        $statsGraphique = $this->getStatsGraphique();

        return view('admin.bilan-financier.index', compact(
            'statsGlobales',
            'statsParAgence',
            'statsGraphique'
        ));
    }

    public function historiquePaiements()
    {
        $paiements = Paiement::with(['colis.agenceExpedition', 'agent'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.bilan-financier.historique', compact('paiements'));
    }

    private function getStatsGlobales()
    {
        $tauxEURversXOF = 655.957;

        // On récupère TOUS les colis avec leur agence pour connaître la devise
        $colis = Colis::with('agenceExpedition')->get();

        $totalXof_MontantTotal = 0;
        $totalXof_MontantPaye = 0;
        $totalXof_MontantImpaye = 0;

        $totalXof_Especes = 0;
        $totalXof_Virement = 0;
        $totalXof_Cheque = 0;
        $totalXof_MobileMoney = 0;
        $totalXof_Livraison = 0;

        $totalColis = $colis->count();
        $totalementPayes = 0;
        $partiellementPayes = 0;
        $nonPayes = 0;

        foreach ($colis as $c) {
            $devise = $c->agenceExpedition->devise ?? 'XOF';
            $coefficient = (strtoupper($devise) === 'EUR' || strtoupper($devise) === 'EURO') ? $tauxEURversXOF : 1;

            $totalXof_MontantTotal += $c->montant_total * $coefficient;
            $totalXof_MontantPaye += $c->montant_paye * $coefficient;
            $totalXof_MontantImpaye += $c->reste_a_payer * $coefficient;

            $totalXof_Especes += ($c->montant_espece ?? 0) * $coefficient;
            $totalXof_Virement += ($c->montant_virement ?? 0) * $coefficient;
            $totalXof_Cheque += ($c->montant_cheque ?? 0) * $coefficient;
            $totalXof_MobileMoney += ($c->montant_mobile_money ?? 0) * $coefficient;
            $totalXof_Livraison += ($c->montant_livraison ?? 0) * $coefficient;

            if ($c->statut_paiement === 'totalement_paye')
                $totalementPayes++;
            elseif ($c->statut_paiement === 'partiellement_paye')
                $partiellementPayes++;
            else
                $nonPayes++;
        }

        $tauxRecouvrement = $totalXof_MontantTotal > 0 ? round(($totalXof_MontantPaye / $totalXof_MontantTotal) * 100, 2) : 0;

        // Préparation des deux blocs
        $statsXof = [
            'devise' => 'XOF',
            'total_colis' => $totalColis,
            'montant_total' => $totalXof_MontantTotal,
            'montant_paye' => $totalXof_MontantPaye,
            'montant_impaye' => $totalXof_MontantImpaye,
            'totalement_payes' => $totalementPayes,
            'partiellement_payes' => $partiellementPayes,
            'non_payes' => $nonPayes,
            'taux_recouvrement' => $tauxRecouvrement,
            'montant_especes' => $totalXof_Especes,
            'montant_virement' => $totalXof_Virement,
            'montant_cheque' => $totalXof_Cheque,
            'montant_mobile_money' => $totalXof_MobileMoney,
            'montant_livraison' => $totalXof_Livraison,
        ];

        $statsEur = [
            'devise' => 'EUR',
            'total_colis' => $totalColis,
            'montant_total' => $totalXof_MontantTotal / $tauxEURversXOF,
            'montant_paye' => $totalXof_MontantPaye / $tauxEURversXOF,
            'montant_impaye' => $totalXof_MontantImpaye / $tauxEURversXOF,
            'totalement_payes' => $totalementPayes,
            'partiellement_payes' => $partiellementPayes,
            'non_payes' => $nonPayes,
            'taux_recouvrement' => $tauxRecouvrement,
            'montant_especes' => $totalXof_Especes / $tauxEURversXOF,
            'montant_virement' => $totalXof_Virement / $tauxEURversXOF,
            'montant_cheque' => $totalXof_Cheque / $tauxEURversXOF,
            'montant_mobile_money' => $totalXof_MobileMoney / $tauxEURversXOF,
            'montant_livraison' => $totalXof_Livraison / $tauxEURversXOF,
        ];

        return [
            'EUR' => $statsEur,
            'XOF' => $statsXof
        ];
    }

    private function getStatsParAgence()
    {
        $agences = Agence::all();
        $stats = [];

        foreach ($agences as $agence) {
            // Nombre de colis de l'agence
            $totalColis = Colis::where('agence_expedition_id', $agence->id)->count();

            // Total encaissé uniquement par les AGENTS de cette agence
            $totalEncaisseAgents = Paiement::where('agent_type', 'agent')
                ->whereHas('agent', function ($q) use ($agence) {
                    $q->where('agence_id', $agence->id);
                })
                ->sum('montant') ?? 0;

            $stats[] = [
                'agence' => $agence,
                'total_colis' => $totalColis,
                'total_encaisse_agents' => $totalEncaisseAgents
            ];
        }

        // Trier par montant encaissé décroissant
        usort($stats, function ($a, $b) {
            return $b['total_encaisse_agents'] <=> $a['total_encaisse_agents'];
        });

        return $stats;
    }

    private function getStatsGraphique()
    {
        $currentYear = date('Y');

        $monthlyData = Colis::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(montant_total) as montant_total'),
            DB::raw('SUM(montant_paye) as montant_paye'),
            DB::raw('SUM(reste_a_payer) as montant_impaye')
        )
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = [];
        $montantsTotaux = [];
        $montantsPayes = [];
        $montantsImpayes = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthData = $monthlyData->firstWhere('month', $i);
            $months[] = \DateTime::createFromFormat('!m', $i)->format('M');
            $montantsTotaux[] = $monthData ? floatval($monthData->montant_total) : 0;
            $montantsPayes[] = $monthData ? floatval($monthData->montant_paye) : 0;
            $montantsImpayes[] = $monthData ? floatval($monthData->montant_impaye) : 0;
        }

        return [
            'months' => $months,
            'montants_totaux' => $montantsTotaux,
            'montants_payes' => $montantsPayes,
            'montants_impayes' => $montantsImpayes,
        ];
    }
}
