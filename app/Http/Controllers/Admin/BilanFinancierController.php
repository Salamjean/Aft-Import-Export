<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\Colis;
use App\Models\Paiement;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BilanExport;
use App\Exports\HistoriquePaiementExport;

class BilanFinancierController extends Controller
{
    public function index(Request $request)
    {
        $dateDebut = $request->input('date_debut');
        $dateFin = $request->input('date_fin');
        $agenceId = $request->input('agence_id');

        // Statistiques globales par devise
        $statsGlobales = $this->getStatsGlobales($dateDebut, $dateFin, $agenceId);

        // Statistiques simplifiées par agence
        $statsParAgence = $this->getStatsParAgence($dateDebut, $dateFin);

        // Statistiques mensuelles pour le graphique
        $statsGraphique = $this->getStatsGraphique($dateDebut, $dateFin, $agenceId);

        $agences = Agence::all();

        return view('admin.bilan-financier.index', compact(
            'statsGlobales',
            'statsParAgence',
            'statsGraphique',
            'agences',
            'dateDebut',
            'dateFin',
            'agenceId'
        ));
    }

    public function historiquePaiements()
    {
        $paiements = Paiement::with(['colis.agenceExpedition', 'agent'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.bilan-financier.historique', compact('paiements'));
    }

    private function getStatsGlobales($dateDebut = null, $dateFin = null, $agenceId = null)
    {
        $tauxEURversXOF = 655.957;

        // Base query pour les colis (Valeur Totale / Création)
        $colisQuery = Colis::with('agenceExpedition');
        if ($dateDebut)
            $colisQuery->whereDate('created_at', '>=', $dateDebut);
        if ($dateFin)
            $colisQuery->whereDate('created_at', '<=', $dateFin);
        if ($agenceId)
            $colisQuery->where('agence_expedition_id', $agenceId);

        $colis = $colisQuery->get();

        // Base query pour les paiements (Encaissements réels)
        $paiementQuery = Paiement::with('colis.agenceExpedition');
        if ($dateDebut)
            $paiementQuery->whereDate('created_at', '>=', $dateDebut);
        if ($dateFin)
            $paiementQuery->whereDate('created_at', '<=', $dateFin);
        if ($agenceId) {
            $paiementQuery->whereHas('colis', function ($q) use ($agenceId) {
                $q->where('agence_expedition_id', $agenceId);
            });
        }
        $tousLesPaiements = $paiementQuery->get();

        $stats = ['XOF' => $this->initStatsBlock('XOF'), 'EUR' => $this->initStatsBlock('EUR')];

        // 1. Calculer la Valeur Totale (Billing) à partir des Colis créés dans la période
        foreach ($colis as $c) {
            $devise = $c->agenceExpedition->devise ?? 'XOF';
            $isEur = (strtoupper($devise) === 'EUR' || strtoupper($devise) === 'EURO');

            $montantXof = $c->montant_total * ($isEur ? $tauxEURversXOF : 1);
            $stats['XOF']['montant_total'] += $montantXof;
            $stats['EUR']['montant_total'] += $montantXof / $tauxEURversXOF;

            $stats['XOF']['total_colis']++;
            $stats['EUR']['total_colis']++;

            if ($c->statut_paiement === 'totalement_paye') {
                $stats['XOF']['totalement_payes']++;
                $stats['EUR']['totalement_payes']++;
            } elseif ($c->statut_paiement === 'partiellement_paye') {
                $stats['XOF']['partiellement_payes']++;
                $stats['EUR']['partiellement_payes']++;
            } else {
                $stats['XOF']['non_payes']++;
                $stats['EUR']['non_payes']++;
            }
        }

        // 2. Calculer les Encaissements Réels (Collections) à partir des Paiements reçus dans la période
        foreach ($tousLesPaiements as $p) {
            // Utilisation de la devise réelle de l'encaissement (nouveau champ)
            // Fallback sur l'agence d'expédition si le champ n'est pas encore rempli
            $devisePaiement = $p->devise ?? ($p->colis->agenceExpedition->devise ?? 'XOF');
            $isEur = (strtoupper($devisePaiement) === 'EUR' || strtoupper($devisePaiement) === 'EURO');
            $coeff = $isEur ? $tauxEURversXOF : 1;

            $montantXof = $p->montant * $coeff;
            $stats['XOF']['montant_paye'] += $montantXof;
            $stats['EUR']['montant_paye'] += $montantXof / $tauxEURversXOF;

            // Répartition par méthode
            $methode = $p->methode_paiement;
            $field = $this->getMethodeField($methode);
            if ($field) {
                $stats['XOF'][$field] += $montantXof;
                $stats['EUR'][$field] += $montantXof / $tauxEURversXOF;
            }
        }

        // 3. Calculs finaux
        foreach (['XOF', 'EUR'] as $d) {
            $stats[$d]['montant_impaye'] = max(0, $stats[$d]['montant_total'] - $stats[$d]['montant_paye']);
            $stats[$d]['taux_recouvrement'] = $stats[$d]['montant_total'] > 0
                ? round(($stats[$d]['montant_paye'] / $stats[$d]['montant_total']) * 100, 2)
                : 100;
        }

        return $stats;
    }

    private function initStatsBlock($devise)
    {
        return [
            'devise' => $devise,
            'total_colis' => 0,
            'montant_total' => 0,
            'montant_paye' => 0,
            'montant_impaye' => 0,
            'totalement_payes' => 0,
            'partiellement_payes' => 0,
            'non_payes' => 0,
            'taux_recouvrement' => 0,
            'montant_especes' => 0,
            'montant_virement' => 0,
            'montant_cheque' => 0,
            'montant_mobile_money' => 0,
            'montant_livraison' => 0,
        ];
    }

    private function getMethodeField($methode)
    {
        switch ($methode) {
            case 'espece':
                return 'montant_especes';
            case 'virement_bancaire':
                return 'montant_virement';
            case 'cheque':
                return 'montant_cheque';
            case 'mobile_money':
                return 'montant_mobile_money';
            case 'livraison':
                return 'montant_livraison';
            default:
                return null;
        }
    }

    private function getStatsParAgence($dateDebut = null, $dateFin = null)
    {
        $agences = Agence::all();
        $stats = [];

        foreach ($agences as $agence) {
            // Uniquement les colis expédiés par cette agence (pour les stats de Volume)
            $colisQuery = Colis::where('agence_expedition_id', $agence->id);
            if ($dateDebut)
                $colisQuery->whereDate('created_at', '>=', $dateDebut);
            if ($dateFin)
                $colisQuery->whereDate('created_at', '<=', $dateFin);
            $totalColis = $colisQuery->count();

            // Uniquement les PAIEMENTS encaissés par cette agence (pour les stats de Gestion)
            $paiementQuery = Paiement::where('agence_id', $agence->id);
            if ($dateDebut)
                $paiementQuery->whereDate('created_at', '>=', $dateDebut);
            if ($dateFin)
                $paiementQuery->whereDate('created_at', '<=', $dateFin);

            $totalEncaisse = $paiementQuery->sum('montant') ?? 0;

            $stats[] = [
                'agence' => $agence,
                'total_colis' => $totalColis,
                'total_encaisse' => $totalEncaisse
            ];
        }

        usort($stats, function ($a, $b) {
            return $b['total_encaisse'] <=> $a['total_encaisse'];
        });

        return $stats;
    }

    private function getStatsGraphique($dateDebut = null, $dateFin = null, $agenceId = null)
    {
        $currentYear = date('Y');
        $yearToUse = $dateDebut ? date('Y', strtotime($dateDebut)) : $currentYear;

        // Facturation par mois (Colis)
        $colisQuery = Colis::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(montant_total) as montant_total')
        )
            ->whereYear('created_at', $yearToUse);
        if ($agenceId)
            $colisQuery->where('agence_expedition_id', $agenceId);
        $billingData = $colisQuery->groupBy('month')->get();

        // Encaissements par mois (Paiements)
        $paiementQuery = Paiement::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(montant) as montant_paye')
        )
            ->whereYear('created_at', $yearToUse);
        if ($agenceId) {
            $paiementQuery->where('agence_id', $agenceId);
        }
        $collectionData = $paiementQuery->groupBy('month')->get();

        $months = [];
        $montantsTotaux = [];
        $montantsPayes = [];
        $montantsImpayes = [];

        for ($i = 1; $i <= 12; $i++) {
            $b = $billingData->firstWhere('month', $i);
            $c = $collectionData->firstWhere('month', $i);

            $months[] = \DateTime::createFromFormat('!m', $i)->format('M');
            $total = $b ? floatval($b->montant_total) : 0;
            $paye = $c ? floatval($c->montant_paye) : 0;

            $montantsTotaux[] = $total;
            $montantsPayes[] = $paye;
            $montantsImpayes[] = max(0, $total - $paye);
        }

        return [
            'months' => $months,
            'montants_totaux' => $montantsTotaux,
            'montants_payes' => $montantsPayes,
            'montants_impayes' => $montantsImpayes,
        ];
    }

    public function exportBilanExcel(Request $request)
    {
        $dateDebut = $request->input('date_debut');
        $dateFin = $request->input('date_fin');
        $agenceId = $request->input('agence_id');

        $statsGlobales = $this->getStatsGlobales($dateDebut, $dateFin, $agenceId);
        $statsParAgence = $this->getStatsParAgence($dateDebut, $dateFin);

        $filename = "bilan_financier_" . date('d_m_Y') . ".xlsx";
        return Excel::download(new BilanExport($statsGlobales, $statsParAgence), $filename);
    }

    public function exportHistoriqueExcel()
    {
        $filename = "historique_paiements_" . date('d_m_Y') . ".xlsx";
        return Excel::download(new HistoriquePaiementExport(), $filename);
    }
}
