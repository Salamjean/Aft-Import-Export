<?php

namespace App\Http\Controllers\Agent\Programme;

use App\Http\Controllers\Controller;
use App\Models\Chauffeur;
use App\Models\Depot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use PDF;

class AgentDepotController extends Controller
{
    public function index()
    {
        // Récupérer l'agent connecté et son agence_id
        $agent = Auth::guard('agent')->user();
        $agenceId = $agent->agence_id;

        // Filtrer les dépôts par l'agence du chauffeur
        $depots = Depot::with('chauffeur')
            ->whereHas('chauffeur', function($query) use ($agenceId) {
                $query->where('agence_id', $agenceId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Filtrer aussi les chauffeurs par l'agence de l'agent
        $chauffeurs = Chauffeur::where('agence_id', $agenceId)->get();
        
        // Statistiques filtrées par agence
        $totalDepots = Depot::whereHas('chauffeur', function($query) use ($agenceId) {
            $query->where('agence_id', $agenceId);
        })->count();
        
        $programmesCount = Depot::whereHas('chauffeur', function($query) use ($agenceId) {
            $query->where('agence_id', $agenceId);
        })->where('statut', 'programme')->count();
        
        $enCoursCount = Depot::whereHas('chauffeur', function($query) use ($agenceId) {
            $query->where('agence_id', $agenceId);
        })->where('statut', 'en_cours')->count();
        
        $terminesCount = Depot::whereHas('chauffeur', function($query) use ($agenceId) {
            $query->where('agence_id', $agenceId);
        })->where('statut', 'termine')->count();
        
        return view('agent.depot.index', compact(
            'depots', 
            'chauffeurs',
            'totalDepots',
            'programmesCount',
            'enCoursCount',
            'terminesCount'
        ));
    }

    public function depot()
    {
        // Récupérer l'agent connecté et son agence_id
         $agent = Auth::guard('agent')->user();
        $agenceId = $agent->agence_id;

        // Filtrer les chauffeurs par l'agence de l'agent
        $chauffeurs = Chauffeur::where('agence_id', $agenceId)->get();
        
        return view('agent.depot.create', compact('chauffeurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'chauffeur_id' => 'required|exists:chauffeurs,id',
            'depots' => 'required|array',
            'depots.*.nature_objet' => 'required|string',
            'depots.*.quantite' => 'required|integer|min:1',
            'depots.*.nom_concerne' => 'required|string',
            'depots.*.prenom_concerne' => 'required|string',
            'depots.*.contact' => 'required|string',
            'depots.*.email' => 'nullable|email',
            'depots.*.adresse_depot' => 'required|string',
        ]);

        $depotsCrees = [];
        $totalCodes = 0;

        foreach ($request->depots as $depotData) {
            $quantite = $depotData['quantite'];
            $codes = [];
            $qrPaths = [];
            
            // Générer les codes et QR codes selon la quantité
            for ($i = 1; $i <= $quantite; $i++) {
                $codeNature = 'DEP-' . date('Ymd') . '-' . strtoupper(Str::random(6));
                $qrCodePath = $this->generateQRCode($codeNature);
                
                $codes[] = $codeNature;
                $qrPaths[] = $qrCodePath;
                $totalCodes++;
            }

            // Créer un seul enregistrement avec tous les codes et QR codes
            $depot = Depot::create([
                'reference' => 'DEP-' . date('YmdHis') . '-' . strtoupper(Str::random(4)),
                'quantite' => $quantite,
                'nature_objet' => $depotData['nature_objet'],
                'nom_concerne' => $depotData['nom_concerne'],
                'prenom_concerne' => $depotData['prenom_concerne'],
                'contact' => $depotData['contact'],
                'email' => $depotData['email'],
                'adresse_depot' => $depotData['adresse_depot'],
                'code_nature' => implode(',', $codes), // Codes séparés par des virgules
                'path_qrcode' => implode(',', $qrPaths), // Chemins séparés par des virgules
                'chauffeur_id' => $request->chauffeur_id,
                'date_depot' => $request->date_depot,
                'statut' => 'en_cours',
            ]);
            
            $depotsCrees[] = $depot;
        }

        return redirect()->route('agent.depot.index')
            ->with('success', count($depotsCrees) . ' dépôt(s).');
    }

    /**
     * Display depot details for AJAX modal.
     */
    public function details(Request $request, $id)
    {
        try {
            // Trouver le dépôt avec la relation chauffeur
            $depot = Depot::with('chauffeur')->findOrFail($id);
            
            // Décoder les codes QR si ils existent
            $codesQr = [];
            if ($depot->codes_qr) {
                $codesQr = json_decode($depot->codes_qr, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $codesQr = [];
                }
            }

            // Retourner les données en JSON
            return response()->json([
                'success' => true,
                'data' => [
                    'reference' => $depot->reference,
                    'nature_objet' => $depot->nature_objet,
                    'quantite' => $depot->quantite,
                    'adresse_depot' => $depot->adresse_depot,
                    'nom_concerne' => $depot->nom_concerne,
                    'prenom_concerne' => $depot->prenom_concerne,
                    'contact' => $depot->contact,
                    'email' => $depot->email,
                    'codes_qr' => $codesQr,
                    'chauffeur' => $depot->chauffeur ? $depot->chauffeur->nom . ' ' . $depot->chauffeur->prenom : 'Non assigné',
                    'date_depot' => $depot->date_depot ? \Carbon\Carbon::parse($depot->date_depot)->format('d/m/Y') : 'Non définie',
                    'statut' => $depot->statut,
                    'created_at' => $depot->created_at->format('d/m/Y H:i'),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur details dépôt: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Dépôt non trouvé ou erreur serveur'
            ], 404);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $depot = Depot::findOrFail($id);
        $chauffeurs = Chauffeur::all();
        return view('agent.depot.edit', compact('depot', 'chauffeurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $depot = Depot::findOrFail($id);

        $request->validate([
            'chauffeur_id' => 'required|exists:chauffeurs,id',
            'nature_objet' => 'required|string',
            'quantite' => 'required|integer|min:1',
            'nom_concerne' => 'required|string',
            'prenom_concerne' => 'required|string',
            'contact' => 'required|string',
            'email' => 'nullable|email',
            'adresse_depot' => 'required|string',
            'date_depot' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $oldQuantite = $depot->quantite;
            $newQuantite = $request->quantite;

            // Récupérer les codes et QR codes existants
            $existingCodes = $depot->code_nature ? explode(',', $depot->code_nature) : [];
            $existingQrPaths = $depot->path_qrcode ? explode(',', $depot->path_qrcode) : [];

            if ($newQuantite != $oldQuantite) {
                // Gestion de la modification de quantité
                if ($newQuantite > $oldQuantite) {
                    // Ajouter des codes
                    $codesToAdd = [];
                    $qrPathsToAdd = [];
                    
                    for ($i = count($existingCodes); $i < $newQuantite; $i++) {
                        $codeNature = 'DEP-' . date('Ymd') . '-' . strtoupper(Str::random(6));
                        $qrCodePath = $this->generateQRCode($codeNature);
                        
                        $codesToAdd[] = $codeNature;
                        $qrPathsToAdd[] = $qrCodePath;
                    }
                    
                    // Fusionner avec les codes existants
                    $allCodes = array_merge($existingCodes, $codesToAdd);
                    $allQrPaths = array_merge($existingQrPaths, $qrPathsToAdd);
                    
                } else {
                    // Supprimer des codes (garder seulement les premiers)
                    $allCodes = array_slice($existingCodes, 0, $newQuantite);
                    $allQrPaths = array_slice($existingQrPaths, 0, $newQuantite);
                    
                    // Supprimer les fichiers QR code des codes enlevés
                    $codesToRemove = array_slice($existingCodes, $newQuantite);
                    $qrPathsToRemove = array_slice($existingQrPaths, $newQuantite);
                    
                    foreach ($qrPathsToRemove as $qrPath) {
                        if ($qrPath && file_exists(public_path($qrPath))) {
                            unlink(public_path($qrPath));
                        }
                    }
                }
            } else {
                // Quantité inchangée, garder les codes existants
                $allCodes = $existingCodes;
                $allQrPaths = $existingQrPaths;
            }

            // Mise à jour du dépôt
            $depot->update([
                'chauffeur_id' => $request->chauffeur_id,
                'nature_objet' => $request->nature_objet,
                'quantite' => $newQuantite,
                'nom_concerne' => $request->nom_concerne,
                'prenom_concerne' => $request->prenom_concerne,
                'contact' => $request->contact,
                'email' => $request->email,
                'adresse_depot' => $request->adresse_depot,
                'date_depot' => $request->date_depot,
                'code_nature' => implode(',', $allCodes),
                'path_qrcode' => implode(',', $allQrPaths),
            ]);

            DB::commit();

            $message = 'Dépôt modifié avec succès.';
            if ($newQuantite != $oldQuantite) {
                $difference = abs($newQuantite - $oldQuantite);
                $action = $newQuantite > $oldQuantite ? 'ajoutés' : 'supprimés';
                $message .= " $difference code(s) $action.";
            }

            return redirect()->route('agent.depot.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur modification dépôt: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la modification du dépôt: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Generate etiquettes view.
     */
   public function etiquettes($id)
    {
        try {
            $depot = Depot::with('chauffeur')->findOrFail($id);
            
            $codes = [];
            if ($depot->codes_qr) {
                $codesData = json_decode($depot->codes_qr, true);
                if (is_array($codesData)) {
                    foreach ($codesData as $codeData) {
                        $codes[] = [
                            'code' => $codeData['code'],
                            'qr_code' => $codeData['qr_code']
                        ];
                    }
                }
            }
            
            // Si pas de codes dans codes_qr, vérifier l'ancien format
            if (empty($codes) && $depot->code_nature) {
                $codeList = explode(',', $depot->code_nature);
                $qrList = $depot->path_qrcode ? explode(',', $depot->path_qrcode) : [];
                
                foreach ($codeList as $index => $code) {
                    $codes[] = [
                        'code' => trim($code),
                        'qr_code' => isset($qrList[$index]) ? trim($qrList[$index]) : ''
                    ];
                }
            }
            
            return view('agent.depot.etiquette', compact('depot', 'codes'));
            
        } catch (\Exception $e) {
            return response()->view('agent.depot.etiquette', [
                'depot' => null,
                'codes' => [],
                'error' => 'Dépôt non trouvé: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Download etiquettes as PDF.
     */
   public function downloadEtiquettes($id)
{
    try {
        $depot = Depot::with('chauffeur')->findOrFail($id);
        
        $codes = [];
        if ($depot->codes_qr) {
            $codesData = json_decode($depot->codes_qr, true);
            if (is_array($codesData)) {
                foreach ($codesData as $codeData) {
                    $codes[] = [
                        'code' => $codeData['code'],
                        'qr_code' => $codeData['qr_code']
                    ];
                }
            }
        }
        
        // Si pas de codes dans codes_qr, vérifier l'ancien format
        if (empty($codes) && $depot->code_nature) {
            $codeList = explode(',', $depot->code_nature);
            $qrList = $depot->path_qrcode ? explode(',', $depot->path_qrcode) : [];
            
            foreach ($codeList as $index => $code) {
                $codes[] = [
                    'code' => trim($code),
                    'qr_code' => isset($qrList[$index]) ? trim($qrList[$index]) : ''
                ];
            }
        }
        
        $pdf = PDF::loadView('agent.depot.etiquette', compact('depot', 'codes'))
                  ->setPaper([0, 0, 141.73, 226.77], 'portrait'); // 50x80mm en points
        
        return $pdf->download('etiquettes-' . $depot->reference . '.pdf');
        
    } catch (\Exception $e) {
        return back()->with('error', 'Erreur génération PDF: ' . $e->getMessage());
    }
}

    /**
     * Remove the specified resource from storage.
     */
public function destroy($id)
{
    try {
        // Vérifier si l'ID est numérique
        if (!is_numeric($id)) {
            Log::warning("Tentative de suppression avec ID non numérique: {$id}");
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'ID invalide'
                ], 400);
            }
            
            return redirect()->route('depot.index')
                ->with('error', 'ID de dépôt invalide.');
        }

        // Chercher le dépôt avec find() au lieu de findOrFail()
        $depot = Depot::find($id);
        
        if (!$depot) {
            Log::warning("Tentative de suppression d'un dépôt inexistant ID: {$id}");
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Dépôt non trouvé'
                ], 404);
            }
            
            return redirect()->route('agent.depot.index')
                ->with('error', 'Dépôt non trouvé.');
        }

        $depotReference = $depot->reference;
        $depot->delete();

        Log::info("Dépôt supprimé: {$depotReference} (ID: {$id})");

        // Réponse AJAX
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Dépôt {$depotReference} supprimé avec succès."
            ]);
        }

        // Redirection normale
        return redirect()->route('agent.depot.index')
            ->with('success', "Dépôt {$depotReference} supprimé avec succès.");

    } catch (\Exception $e) {
        Log::error("Erreur suppression dépôt ID {$id}: " . $e->getMessage());

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }

        return redirect()->route('agent.depot.index')
            ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
    }
}

    /**
     * Update depot status.
     */
    public function updateStatut(Request $request, Depot $depot)
    {
        $request->validate([
            'statut' => 'required|in:programmé,en_cours,terminé,annulé'
        ]);

        $depot->update(['statut' => $request->statut]);

        return response()->json(['success' => 'Statut mis à jour avec succès.']);
    }

    

    private function generateQRCode($codeNature)
    {
        $qrCodePath = 'qrcodes/depot/' . $codeNature . '.svg';
        $fullPath = storage_path('app/public/' . $qrCodePath);
        
        // Créer le dossier s'il n'existe pas
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }
        
        // Générer le QR code en SVG (pas besoin d'imagick)
        QrCode::format('svg')
            ->size(200)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($codeNature, $fullPath);
        
        return 'storage/' . $qrCodePath;
    }
}
