<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Depot;
use App\Models\Livraison;
use App\Models\Recuperation;
use Illuminate\Support\Facades\Auth;
use PDF;

class ChauffeurProgrammeController extends Controller
{
    public function index(){
        $chauffeurId = Auth::guard('chauffeur')->user()->id;
        
        // Récupérer les dépôts du chauffeur
        $depots = Depot::where('chauffeur_id', $chauffeurId)
            ->where('statut', 'en_cours')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($depot) {
                $depot->type = 'depot';
                $depot->date_programme = $depot->date_depot;
                $depot->adresse = $depot->adresse_depot;
                return $depot;
            });
        
        // Récupérer les récupérations du chauffeur
        $recuperations = Recuperation::where('chauffeur_id', $chauffeurId)
            ->where('statut', 'en_cours')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($recuperation) {
                $recuperation->type = 'recuperation';
                $recuperation->date_programme = $recuperation->date_recuperation;
                $recuperation->adresse = $recuperation->adresse_recuperation;
                return $recuperation;
            });

        // Récupérer les livraisons du chauffeur
        $livraisons = Livraison::where('chauffeur_id', $chauffeurId)
            ->where('statut', 'en_cours')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($livraison) {
                $livraison->type = 'livraison';
                $livraison->date_programme = $livraison->date_livraison;
                $livraison->adresse = $livraison->adresse_livraison;
                return $livraison;
            });
        
        // Fusionner et trier par date de création
        $programmes = $depots->concat($recuperations)
            ->concat($livraisons)
            ->sortByDesc('created_at')
            ->values();
        
        return view('chauffeur.programme.index', compact('programmes'));
    }

    public function showDetails($type, $id)
{
    try {
        if ($type === 'depot') {
            $programme = Depot::with('chauffeur')->findOrFail($id);
            $adresse = $programme->adresse_depot;
            $date_programme = $programme->date_depot;
        } else if ($type === 'recuperation') { // CORRECTION: 'recuperation' au lieu de 'depot'
            $programme = Recuperation::with('chauffeur')->findOrFail($id);
            $adresse = $programme->adresse_recuperation;
            $date_programme = $programme->date_recuperation;
        } else if ($type === 'livraison') { // CORRECTION: 'livraison' au lieu de 'depot'
            $programme = Livraison::with('chauffeur')->findOrFail($id);
            $adresse = $programme->adresse_livraison; // CORRECTION: adresse_livraison
            $date_programme = $programme->date_livraison; // CORRECTION: date_livraison
        } else {
            throw new \Exception('Type de programme non reconnu');
        }

        return response()->json([
            'success' => true,
            'data' => [
                'type' => $type,
                'reference' => $programme->reference,
                'nature_objet' => $programme->nature_objet,
                'quantite' => $programme->quantite,
                'adresse' => $adresse,
                'nom_concerne' => $programme->nom_concerne,
                'prenom_concerne' => $programme->prenom_concerne,
                'contact' => $programme->contact,
                'email' => $programme->email,
                'statut' => $programme->statut,
                'date_programme' => $date_programme ? \Carbon\Carbon::parse($date_programme)->format('d/m/Y H:i') : 'Non définie',
                'created_at' => $programme->created_at->format('d/m/Y H:i'),
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Programme non trouvé: ' . $e->getMessage()
        ], 404);
    }
}

    public function downloadEtiquettes($type, $id)
    {
        try {
            if ($type === 'depot') {
                $programme = Depot::where('chauffeur_id', Auth::guard('chauffeur')->user()->id)->findOrFail($id);
                $codes = $this->getCodesFromProgramme($programme);
                $filename = 'etiquettes-depot-' . $programme->reference . '.pdf';
                
            } else {
                $programme = Recuperation::where('chauffeur_id', Auth::guard('chauffeur')->user()->id)->findOrFail($id);
                
                // Vérifier si les informations du destinataire sont déjà renseignées
                if (!$programme->nom_destinataire || !$programme->contact_destinataire) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Informations du destinataire manquantes',
                        'requires_destination' => true
                    ], 422);
                }
                
                $codes = $this->getCodesFromProgramme($programme);
                $filename = 'etiquettes-recuperation-' . $programme->reference . '.pdf';
                
                // Marquer la récupération comme terminée
                $programme->update(['statut' => 'termine']);
            }

            $pdf = PDF::loadView('chauffeur.programme.etiquettes', [
                'programme' => $programme,
                'codes' => $codes,
                'type' => $type
            ])->setPaper([0, 0, 141.73, 226.77], 'portrait');

            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    // Nouvelle méthode pour sauvegarder les informations du destinataire
    public function saveDestinationInfo(Request $request, $id)
    {
        try {
            $request->validate([
                'nom_destinataire' => 'required|string|max:255',
                'prenom_destinataire' => 'required|string|max:255',
                'email_destinataire' => 'nullable|email',
                'indicatif_destinataire' => 'required|string|max:10',
                'contact_destinataire' => 'required|string|max:20',
                'adresse_destinataire' => 'required|string'
            ]);

            $recuperation = Recuperation::where('chauffeur_id', Auth::guard('chauffeur')->user()->id)->findOrFail($id);
            
            $recuperation->update([
                'nom_destinataire' => $request->nom_destinataire,
                'prenom_destinataire' => $request->prenom_destinataire,
                'email_destinataire' => $request->email_destinataire,
                'indicatif_destinataire' => $request->indicatif_destinataire,
                'contact_destinataire' => $request->contact_destinataire,
                'adresse_destinataire' => $request->adresse_destinataire
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Informations du destinataire sauvegardées avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getCodesFromProgramme($programme)
    {
        $codes = [];
        
        // Essayer d'abord avec code_nature (nouveau format)
        if ($programme->code_nature) {
            $codeList = explode(',', $programme->code_nature);
            $qrList = $programme->path_qrcode ? explode(',', $programme->path_qrcode) : [];
            
            foreach ($codeList as $index => $code) {
                $codes[] = [
                    'code' => trim($code),
                    'qr_code' => isset($qrList[$index]) ? trim($qrList[$index]) : ''
                ];
            }
        }
        
        return $codes;
    }

    public function history(){
        $chauffeurId = Auth::guard('chauffeur')->user()->id;
        
        // Récupérer les dépôts du chauffeur
        $depots = Depot::where('chauffeur_id', $chauffeurId)
            ->where('statut',  'termine')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($depot) {
                $depot->type = 'depot';
                $depot->date_programme = $depot->date_depot;
                $depot->adresse = $depot->adresse_depot;
                return $depot;
            });
        
        // Récupérer les récupérations du chauffeur
        $recuperations = Recuperation::where('chauffeur_id', $chauffeurId)
            ->where('statut', 'termine')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($recuperation) {
                $recuperation->type = 'recuperation';
                $recuperation->date_programme = $recuperation->date_recuperation;
                $recuperation->adresse = $recuperation->adresse_recuperation;
                return $recuperation;
            });
        // Récupérer les récupérations du chauffeur
        $livraisons = Livraison::where('chauffeur_id', $chauffeurId)
            ->where('statut', 'termine')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($livraison) {
                $livraison->type = 'livraison';
                $livraison->date_programme = $livraison->date_livraison;
                $livraison->adresse = $livraison->adresse_livraison;
                return $livraison;
            });
        
        // Fusionner et trier par date de création
        $programmes = $depots->merge($recuperations)
            ->merge($livraisons)
            ->sortByDesc('created_at')
            ->values();
        
        return view('chauffeur.programme.history', compact('programmes'));
    }
}