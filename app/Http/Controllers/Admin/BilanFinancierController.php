<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\Colis;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BilanFinancierController extends Controller
{
    public function index()
    {
        // Statistiques globales
        $statsGlobales = $this->getStatsGlobales();

        // Statistiques par agence
        $statsParAgence = $this->getStatsParAgence();

        // Statistiques mensuelles pour le graphique
        $statsGraphique = $this->getStatsGraphique();

        // Historique des paiements (les 15 derniers)
        $derniersPaiements = Paiement::with('colis')
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        return view('admin.bilan-financier.index', compact(
            'statsGlobales',
            'statsParAgence',
            'statsGraphique',
            'derniersPaiements'
        ));
    }

    private function getStatsGlobales()
    {
        // Tous les colis
        $totalColis = Colis::count();

        // Montants totaux
        $montantTotal = Colis::sum('montant_total') ?? 0;
        $montantPaye = Colis::sum('montant_paye') ?? 0;
        $montantImpaye = Colis::sum('reste_a_payer') ?? 0;

        // Statistiques par statut de paiement
        $totalementPayes = Colis::where('statut_paiement', 'totalement_paye')->count();
        $partiellementPayes = Colis::where('statut_paiement', 'partiellement_paye')->count();
        $nonPayes = Colis::where('statut_paiement', 'non_paye')->count();

        // Montants par méthode de paiement
        $montantEspeces = Colis::sum('montant_espece') ?? 0;
        $montantVirement = Colis::sum('montant_virement') ?? 0;
        $montantCheque = Colis::sum('montant_cheque') ?? 0;
        $montantMobileMoney = Colis::sum('montant_mobile_money') ?? 0;
        $montantLivraison = Colis::sum('montant_livraison') ?? 0;

        // Taux de recouvrement
        $tauxRecouvrement = $montantTotal > 0 ? ($montantPaye / $montantTotal) * 100 : 0;

        return [
            'total_colis' => $totalColis,
            'montant_total' => $montantTotal,
            'montant_paye' => $montantPaye,
            'montant_impaye' => $montantImpaye,
            'totalement_payes' => $totalementPayes,
            'partiellement_payes' => $partiellementPayes,
            'non_payes' => $nonPayes,
            'montant_especes' => $montantEspeces,
            'montant_virement' => $montantVirement,
            'montant_cheque' => $montantCheque,
            'montant_mobile_money' => $montantMobileMoney,
            'montant_livraison' => $montantLivraison,
            'taux_recouvrement' => round($tauxRecouvrement, 2),
        ];
    }

    private function getStatsParAgence()
    {
        $agences = Agence::all();
        $stats = [];

        foreach ($agences as $agence) {
            // Colis pour l'expédition
            $colisExpedition = Colis::where('agence_expedition_id', $agence->id);

            // Montants
            $montantTotal = $colisExpedition->sum('montant_total') ?? 0;
            $montantPaye = $colisExpedition->sum('montant_paye') ?? 0;
            $montantImpaye = $colisExpedition->sum('reste_a_payer') ?? 0;

            // Nombre de colis
            $totalColis = $colisExpedition->count();
            $totalementPayes = (clone $colisExpedition)->where('statut_paiement', 'totalement_paye')->count();
            $partiellementPayes = (clone $colisExpedition)->where('statut_paiement', 'partiellement_paye')->count();
            $nonPayes = (clone $colisExpedition)->where('statut_paiement', 'non_paye')->count();

            // Taux de recouvrement
            $tauxRecouvrement = $montantTotal > 0 ? ($montantPaye / $montantTotal) * 100 : 0;

            $stats[] = [
                'agence' => $agence,
                'total_colis' => $totalColis,
                'montant_total' => $montantTotal,
                'montant_paye' => $montantPaye,
                'montant_impaye' => $montantImpaye,
                'totalement_payes' => $totalementPayes,
                'partiellement_payes' => $partiellementPayes,
                'non_payes' => $nonPayes,
                'taux_recouvrement' => round($tauxRecouvrement, 2),
            ];
        }

        // Trier par montant total décroissant
        usort($stats, function ($a, $b) {
            return $b['montant_total'] <=> $a['montant_total'];
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
