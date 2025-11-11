<?php

namespace App\Http\Controllers\Admin\Colis;

use App\Http\Controllers\Controller;
use App\Models\Colis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PDF;

class EtiquetteController extends Controller
{
    public function genererEtiquettes($id)
    {
        try {
            $colis = Colis::with(['agenceExpedition', 'agenceDestination'])->findOrFail($id);
            
            // Décoder les données des colis
            $colisDetails = json_decode($colis->colis, true);
            $quantiteTotale = collect($colisDetails)->sum('quantite');
            
            // Récupérer les QR codes
            $qrCodesData = json_decode($colis->qr_codes, true);
            $qrCodes = $qrCodesData['qr_individuels'] ?? [];

            // Créer une collection d'étiquettes (une par unité)
            $etiquettesCollection = collect();
            
            // Parcourir chaque unité pour assigner le bon type de colis
            $uniteCounter = 0;
            foreach ($colisDetails as $item) {
                for ($i = 1; $i <= $item['quantite']; $i++) {
                    $uniteCounter++;
                    
                    $qrCode = $qrCodes[$uniteCounter - 1] ?? null;
                    
                    $qrCodeAbsolutePath = null;
                    if ($qrCode && !empty($qrCode['qr_code_path'])) {
                        $qrCodeAbsolutePath = storage_path('app/public/' . $qrCode['qr_code_path']);
                    }

                    // ✅ CORRECTION : Ajouter une valeur par défaut pour type_colis
                    $typeColis = $item['type_colis'] ?? 'Standard'; // Valeur par défaut

                    $etiquettesCollection->push((object)[
                        'reference_colis' => $colis->reference_colis,
                        'name_destinataire' => $colis->name_destinataire,
                        'prenom_destinataire' => $colis->prenom_destinataire,
                        'contact_destinataire' => $colis->contact_destinataire,
                        'agence_destination' => $colis->agence_destination,
                        'name_expediteur' => $colis->name_expediteur,
                        'prenom_expediteur' => $colis->prenom_expediteur,
                        'contact_expediteur' => $colis->contact_expediteur,
                        'created_at' => $colis->created_at,
                        'qr_code_absolute_path' => $qrCodeAbsolutePath,
                        'qr_code_path' => $qrCode['qr_code_path'] ?? null,
                        'quantite_totale' => $quantiteTotale,
                        'numero_etiquette' => $uniteCounter,
                        'type_colis' => $typeColis // ✅ Utiliser la variable avec valeur par défaut
                    ]);
                }
            }

            $action = request('action', 'preview');

            // Toujours utiliser la même vue
            $data = ['colis_collection' => $etiquettesCollection];

            // Pour le PDF (download et print)
            if (in_array($action, ['download', 'print'])) {
                $pdf = PDF::loadView('admin.colis.documents.etiquettes', $data)
                        ->setPaper('a6', 'landscape')
                        ->setOption('isRemoteEnabled', true);

                if ($action === 'download') {
                    return $pdf->download('etiquettes-' . $colis->reference_colis . '.pdf');
                }
                
                return $pdf->stream('etiquettes-' . $colis->reference_colis . '.pdf');
            }

            // Pour l'aperçu HTML, utiliser une vue différente
            return view('admin.colis.documents.etiquettes-preview', [
                'colis_collection' => $etiquettesCollection,
                'colis' => $colis,
                'quantiteTotale' => $quantiteTotale
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur génération étiquettes: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la génération des étiquettes: ' . $e->getMessage());
        }
    }

    /**
     * Générer la facture
     */
    public function generateFacture($id)
    {
        try {
            // Récupérer le colis avec toutes les relations nécessaires
            $colis = Colis::with(['conteneur', 'agenceExpedition', 'agenceDestination', 'service'])
                          ->findOrFail($id);

            // Décoder les données JSON des colis
            $colisDetails = json_decode($colis->colis, true);
            $codesColis = json_decode($colis->code_colis, true);

            // Calculer les totaux
            $montantTotal = $colis->montant_total ?? 0;
            $montantPaye = $colis->montant_paye ?? 0;
            $devise = $colis->devise;
            $resteAPayer = $colis->reste_a_payer ?? ($montantTotal - $montantPaye);

            $data = [
                'colis' => $colis,
                'colisDetails' => $colisDetails,
                'codesColis' => $codesColis,
                'montantTotal' => $montantTotal,
                'montantPaye' => $montantPaye,
                'resteAPayer' => $resteAPayer,
                'devise' => $devise,
                'dateFacture' => now()->format('d/m/Y'),
                'numeroFacture' => 'FACT-' . $colis->reference_colis . '-' . now()->format('Ymd'),
                'entreprise' => [
                    'nom' => 'AFT IMPORT EXPORT',
                    'adresse' => '7 AVENUE LOUIS BLERIOT LA COURNEUVE 93120 France',
                    'telephone' => '+33171894551',
                    'email' => 'contact@aft-import-export.com',
                    'siret' => '81916365',
                    'tva' => 'FR86681916365'
                ],
                'service' => $colis->service
            ];

            $action = request('action', 'preview');

            // Générer le PDF
            $pdf = PDF::loadView('admin.colis.documents.facture', $data);

            if ($action === 'download') {
                return $pdf->download('facture-' . $colis->reference_colis . '.pdf');
            }

            // Par défaut, retourner le PDF en stream pour impression/prévisualisation
            return $pdf->stream('facture-' . $colis->reference_colis . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la génération de la facture: ' . $e->getMessage());
        }
    }

    /**
     * Générer le bon de livraison
     */
    public function generateBonLivraison($id)
    {
        try {
            // Récupérer le colis avec toutes les relations nécessaires
            $colis = Colis::with(['conteneur', 'agenceExpedition', 'agenceDestination', 'service'])
                          ->findOrFail($id);

            // Décoder les données JSON des colis
            $colisDetails = json_decode($colis->colis, true);

            $data = [
                'colis' => $colis,
                'colisDetails' => $colisDetails,
                'dateLivraison' => now()->format('d/m/Y'),
                'numeroBonLivraison' => 'BL-' . $colis->reference_colis . '-' . now()->format('Ymd'),
                'entreprise' => [
                    'nom' => 'AFT IMPORT EXPORT',
                    'adresse' => '7 AVENUE LOUIS BLERIOT LA COURNEUVE 93120 France',
                    'telephone' => '+33171894551',
                    'email' => 'contact@aft-import-export.com',
                    'siret' => '81916365',
                    'tva' => 'FR86681916365'
                ]
            ];

            $action = request('action', 'preview');

            // Générer le PDF
            $pdf = PDF::loadView('admin.colis.documents.bon-livraison', $data);

            if ($action === 'download') {
                return $pdf->download('bon-livraison-' . $colis->reference_colis . '.pdf');
            }

            // Par défaut, retourner le PDF en stream pour impression/prévisualisation
            return $pdf->stream('bon-livraison-' . $colis->reference_colis . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la génération du bon de livraison: ' . $e->getMessage());
        }
    }

public function exportPDF(Request $request)
{
    try {
        // Utiliser la même logique de requête que dans index()
        $query = Colis::query();
        
        // Appliquer les filtres de recherche
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference_colis', 'LIKE', '%'.$search.'%')
                  ->orWhere('name_expediteur', 'LIKE', '%'.$search.'%')
                  ->orWhere('name_destinataire', 'LIKE', '%'.$search.'%')
                  ->orWhere('contact_expediteur', 'LIKE', '%'.$search.'%')
                  ->orWhere('contact_destinataire', 'LIKE', '%'.$search.'%');
            });
        }
        
        // Filtre par statut
        if ($request->has('status') && $request->status != '') {
            $query->where('statut', $request->status);
        }
        
        // Filtre par mode de transit
        if ($request->has('mode_transit') && $request->mode_transit != '') {
            $query->where('mode_transit', $request->mode_transit);
        }
        
        // Filtre par statut de paiement
        if ($request->has('paiement') && $request->paiement != '') {
            $query->where('statut_paiement', $request->paiement);
        }
        
        $colis = $query->orderBy('created_at', 'desc')->get();
        
        // Préparer les données pour la vue PDF
        $data = [
            'colis' => $colis,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
                'mode_transit' => $request->mode_transit,
                'paiement' => $request->paiement,
            ],
            'dateExport' => now()->format('d/m/Y H:i'),
            'totalColis' => $colis->count(),
            'totalMontant' => $colis->sum('montant_total'),
            'entreprise' => [
                'nom' => 'AFT IMPORT EXPORT',
                'adresse' => '7 AVENUE LOUIS BLERIOT LA COURNEUVE 93120 France',
                'telephone' => '+33171894551'
            ]
        ];
        
        // Générer le PDF
        $pdf = PDF::loadView('admin.colis.documents.export-pdf', $data)
                 ->setPaper('a4', 'landscape')
                 ->setOption('isRemoteEnabled', true);
        
        $filename = 'liste-colis-' . now()->format('Y-m-d-H-i') . '.pdf';
        
        return $pdf->download($filename);
        
    } catch (\Exception $e) {
        Log::error('Erreur export PDF colis: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
    }
}
    
}
