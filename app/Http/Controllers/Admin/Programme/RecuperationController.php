<?php

namespace App\Http\Controllers\Admin\Programme;

use App\Http\Controllers\Controller;
use App\Models\Chauffeur;
use App\Models\Recuperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use PDF;

class RecuperationController extends Controller
{
    public function index()
    {
        $recuperations = Recuperation::with('chauffeur')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $chauffeurs = Chauffeur::all();
        
        // Statistiques
        $totalRecuperations = Recuperation::count();
        $programmesCount = Recuperation::where('statut', 'programme')->count();
        $enCoursCount = Recuperation::where('statut', 'en_cours')->count();
        $terminesCount = Recuperation::where('statut', 'termine')->count();
        
        return view('admin.recuperation.index', compact(
            'recuperations', 
            'chauffeurs',
            'totalRecuperations',
            'programmesCount',
            'enCoursCount',
            'terminesCount'
        ));
    }

    public function create()
    {
        $chauffeurs = Chauffeur::get();
        return view('admin.recuperation.create', compact('chauffeurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'chauffeur_id' => 'required|exists:chauffeurs,id',
            'recuperations' => 'required|array',
            'recuperations.*.nature_objet' => 'required|string',
            'recuperations.*.quantite' => 'required|integer|min:1',
            'recuperations.*.nom_concerne' => 'required|string',
            'recuperations.*.prenom_concerne' => 'required|string',
            'recuperations.*.contact' => 'required|string',
            'recuperations.*.email' => 'nullable|email',
            'recuperations.*.adresse_recuperation' => 'required|string',
        ]);

        $recuperationsCrees = [];
        $totalCodes = 0;

        foreach ($request->recuperations as $recupData) {
            $quantite = $recupData['quantite'];
            $codes = [];
            $qrPaths = [];
            
            // Générer les codes et QR codes selon la quantité
            for ($i = 1; $i <= $quantite; $i++) {
                $codeNature = 'REC-' . date('Ymd') . '-' . strtoupper(Str::random(6));
                $qrCodePath = $this->generateQRCode($codeNature);
                
                $codes[] = $codeNature;
                $qrPaths[] = $qrCodePath;
                $totalCodes++;
            }

            // Créer un seul enregistrement avec tous les codes et QR codes
            $recuperation = Recuperation::create([
                'reference' => 'REC-' . date('YmdHis') . '-' . strtoupper(Str::random(4)),
                'quantite' => $quantite,
                'nature_objet' => $recupData['nature_objet'],
                'nom_concerne' => $recupData['nom_concerne'],
                'prenom_concerne' => $recupData['prenom_concerne'],
                'contact' => $recupData['contact'],
                'email' => $recupData['email'],
                'adresse_recuperation' => $recupData['adresse_recuperation'],
                'code_nature' => implode(',', $codes), // Codes séparés par des virgules
                'path_qrcode' => implode(',', $qrPaths), // Chemins séparés par des virgules
                'chauffeur_id' => $request->chauffeur_id,
                'statut' => 'en_cours',
                'date_recuperation' => $request->date_recuperation,
            ]);
            
            $recuperationsCrees[] = $recuperation;
        }

        return redirect()->route('recuperation.index')
            ->with('success', count($recuperationsCrees) . ' récupération(s) créée(s) avec succès.');
    }

    public function details(Request $request, $id)
    {
        try {
            $recuperation = Recuperation::with('chauffeur')->findOrFail($id);
            
            $codesQr = [];
            if ($recuperation->codes_qr) {
                $codesQr = json_decode($recuperation->codes_qr, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $codesQr = [];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'reference' => $recuperation->reference,
                    'nature_objet' => $recuperation->nature_objet,
                    'quantite' => $recuperation->quantite,
                    'adresse_recuperation' => $recuperation->adresse_recuperation,
                    // Informations du client (corrigées)
                    'nom_concerne' => $recuperation->nom_concerne,
                    'prenom_concerne' => $recuperation->prenom_concerne,
                    'contact' => $recuperation->contact,
                    'email' => $recuperation->email,
                    // Informations du destinataire (nouveaux champs)
                    'nom_destinataire' => $recuperation->nom_destinataire,
                    'prenom_destinataire' => $recuperation->prenom_destinataire,
                    'email_destinataire' => $recuperation->email_destinataire,
                    'indicatif_destinataire' => $recuperation->indicatif_destinataire,
                    'contact_destinataire' => $recuperation->contact_destinataire,
                    'adresse_destinataire' => $recuperation->adresse_destinataire,
                    // Autres champs
                    'codes_qr' => $codesQr,
                    'code_nature' => $recuperation->code_nature,
                    'chauffeur' => $recuperation->chauffeur ? $recuperation->chauffeur->nom . ' ' . $recuperation->chauffeur->prenom : 'Non assigné',
                    'date_recuperation' => $recuperation->date_recuperation ? $recuperation->date_recuperation->format('d/m/Y') : 'Non définie',
                    'statut' => $recuperation->statut,
                    'created_at' => $recuperation->created_at->format('d/m/Y H:i'),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur details récupération: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Récupération non trouvée ou erreur serveur'
            ], 404);
        }
    }
    public function etiquettes($id)
    {
        try {
            $recuperation = Recuperation::with('chauffeur')->findOrFail($id);
            
            $codes = [];
            if ($recuperation->codes_qr) {
                $codesData = json_decode($recuperation->codes_qr, true);
                if (is_array($codesData)) {
                    foreach ($codesData as $codeData) {
                        $codes[] = [
                            'code' => $codeData['code'],
                            'qr_code' => $codeData['qr_code']
                        ];
                    }
                }
            }
            
            return view('admin.recuperation.etiquette', compact('recuperation', 'codes'));
            
        } catch (\Exception $e) {
            return response()->view('admin.recuperation.etiquette', [
                'recuperation' => null,
                'codes' => [],
                'error' => 'Récupération non trouvée: ' . $e->getMessage()
            ]);
        }
    }

    public function downloadEtiquettes($id)
    {
        try {
            $recuperation = Recuperation::with('chauffeur')->findOrFail($id);
            
            $codes = [];
            if ($recuperation->codes_qr) {
                $codesData = json_decode($recuperation->codes_qr, true);
                if (is_array($codesData)) {
                    foreach ($codesData as $codeData) {
                        $codes[] = [
                            'code' => $codeData['code'],
                            'qr_code' => $codeData['qr_code']
                        ];
                    }
                }
            }
            
            $pdf = PDF::loadView('admin.recuperation.etiquette', compact('recuperation', 'codes'))
                      ->setPaper([0, 0, 141.73, 226.77], 'portrait');
            
            return $pdf->download('etiquettes-recuperation-' . $recuperation->reference . '.pdf');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur génération PDF: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            // Ignorer les IDs non numériques
            if (!is_numeric($id)) {
                Log::info("Requête de suppression ignorée pour ID: {$id}");
                
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['success' => true, 'message' => 'Opération ignorée']);
                }
                return redirect()->route('recuperation.index');
            }

            $recuperation = Recuperation::find($id);
            
            if (!$recuperation) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['success' => false, 'error' => 'Récupération non trouvée'], 404);
                }
                return redirect()->route('recuperation.index')->with('error', 'Récupération non trouvée.');
            }

            $reference = $recuperation->reference;
            $recuperation->delete();

            Log::info("Récupération supprimée: {$reference} (ID: {$id})");

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Récupération {$reference} supprimée avec succès."
                ]);
            }

            return redirect()->route('recuperation.index')
                ->with('success', "Récupération {$reference} supprimée avec succès.");

        } catch (\Exception $e) {
            Log::error("Erreur suppression récupération ID {$id}: " . $e->getMessage());

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erreur lors de la suppression: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('recuperation.index')
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    private function generateQRCode($codeNature)
    {
        $qrCodePath = 'qrcodes/recuperation/' . $codeNature . '.svg';
        $fullPath = storage_path('app/public/' . $qrCodePath);
        
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }
        
        try {
            QrCode::format('svg')
                ->size(200)
                ->margin(1)
                ->errorCorrection('M')
                ->generate($codeNature, $fullPath);
            
            if (!file_exists($fullPath)) {
                throw new \Exception("Le fichier QR code n'a pas été créé: " . $fullPath);
            }
            
            return 'storage/' . $qrCodePath;
            
        } catch (\Exception $e) {
            Log::error('Erreur génération QR code: ' . $e->getMessage());
            return null;
        }
    }
}