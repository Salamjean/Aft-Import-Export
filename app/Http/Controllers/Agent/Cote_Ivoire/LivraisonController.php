<?php

namespace App\Http\Controllers\Agent\Cote_Ivoire;

use App\Http\Controllers\Controller;
use App\Models\Chauffeur;
use App\Models\Livraison;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LivraisonController extends Controller
{
    public function index()
    {
        // Récupérer l'agent connecté et son agence_id
        $agent = Auth::guard('agent')->user();
        $agenceId = $agent->agence_id;

        // Filtrer les livraisons par l'agence du chauffeur
        $livraisons = Livraison::with('chauffeur')
            ->whereHas('chauffeur', function($query) use ($agenceId) {
                $query->where('agence_id', $agenceId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Filtrer les chauffeurs par l'agence de l'agent
        $chauffeurs = Chauffeur::where('agence_id', $agenceId)->get();
        
        // Statistiques filtrées par agence
        $totalLivraisons = Livraison::whereHas('chauffeur', function($query) use ($agenceId) {
            $query->where('agence_id', $agenceId);
        })->count();
        
        $programmesCount = Livraison::whereHas('chauffeur', function($query) use ($agenceId) {
            $query->where('agence_id', $agenceId);
        })->where('statut', 'programme')->count();
        
        $enCoursCount = Livraison::whereHas('chauffeur', function($query) use ($agenceId) {
            $query->where('agence_id', $agenceId);
        })->where('statut', 'en_cours')->count();
        
        $terminesCount = Livraison::whereHas('chauffeur', function($query) use ($agenceId) {
            $query->where('agence_id', $agenceId);
        })->where('statut', 'termine')->count();
        
        return view('ivoire.livraison.index', compact(
            'livraisons', 
            'chauffeurs',
            'totalLivraisons',
            'programmesCount',
            'enCoursCount',
            'terminesCount'
        ));
    }
    public function create()
    {
        $agent = Auth::guard('agent')->user();
        $agenceId = $agent->agence_id;
        // Filtrer les chauffeurs par l'agence de l'agent
        $chauffeurs = Chauffeur::where('agence_id', $agenceId)->get();
        return view('ivoire.livraison.create', compact('chauffeurs'));
    }

    /**
     * Enregistrer les livraisons
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'chauffeur_id' => 'required|exists:chauffeurs,id',
            'date_livraison' => 'nullable|date|after_or_equal:today',
            'livraisons' => 'required|array|min:1',
            'livraisons.*.nature_objet' => 'required|string|max:255',
            'livraisons.*.quantite' => 'required|integer|min:1',
            'livraisons.*.adresse_livraison' => 'required|string|max:500',
            'livraisons.*.nom_concerne' => 'required|string|max:255',
            'livraisons.*.prenom_concerne' => 'required|string|max:255',
            'livraisons.*.contact' => 'required|string|max:255',
            'livraisons.*.email' => 'nullable|email|max:255',
        ]);

        try {
            // Démarrer une transaction
            DB::beginTransaction();

            $livraisonsCrees = [];

            foreach ($request->livraisons as $index => $livraisonData) {
                // Générer une référence unique
                $reference = $this->generateReference();

                // Préparer la date de livraison
                $dateLivraison = $request->date_livraison 
                    ? Carbon::parse($request->date_livraison)
                    : null;

                // Créer la livraison
                $livraison = Livraison::create([
                    'reference' => $reference,
                    'chauffeur_id' => $request->chauffeur_id,
                    'quantite' => $livraisonData['quantite'],
                    'nature_objet' => $livraisonData['nature_objet'],
                    'nom_concerne' => $livraisonData['nom_concerne'],
                    'prenom_concerne' => $livraisonData['prenom_concerne'],
                    'contact' => $livraisonData['contact'],
                    'email' => $livraisonData['email'] ?? null,
                    'adresse_livraison' => $livraisonData['adresse_livraison'],
                    'date_livraison' => $dateLivraison,
                    'statut' => 'en_cours',
                ]);

                $livraisonsCrees[] = $livraison;
            }

            // Commit de la transaction
            DB::commit();

            // Message de succès
            $message = count($livraisonsCrees) . ' livraison(s) programmée(s) avec succès !';

            return redirect()
                ->route('livraison.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            // Rollback en cas d'erreur
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la programmation des livraisons: ' . $e->getMessage());
        }
    }

    /**
     * Générer une référence unique
     */
    private function generateReference()
    {
        do {
            $reference = 'LIV-' . strtoupper(Str::random(3)) . '-' . date('Ymd') . '-' . rand(1000, 9999);
        } while (Livraison::where('reference', $reference)->exists());

        return $reference;
    }

    public function details(Request $request, $id)
    {
        try {
            $livraison = Livraison::with('chauffeur')->findOrFail($id);
            
            $codesQr = [];
            if ($livraison->codes_qr) {
                $codesQr = json_decode($livraison->codes_qr, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $codesQr = [];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'reference' => $livraison->reference,
                    'nature_objet' => $livraison->nature_objet,
                    'quantite' => $livraison->quantite,
                    'adresse_livraison' => $livraison->adresse_livraison,
                    'nom_client' => $livraison->nom_client,
                    'prenom_client' => $livraison->prenom_client,
                    'contact_client' => $livraison->contact_client,
                    'email_client' => $livraison->email_client,
                    'codes_qr' => $codesQr,
                    'chauffeur' => $livraison->chauffeur ? $livraison->chauffeur->nom . ' ' . $livraison->chauffeur->prenom : 'Non assigné',
                    'date_livraison' => $livraison->date_livraison ? $livraison->date_livraison->format('d/m/Y') : 'Non définie',
                    'statut' => $livraison->statut,
                    'created_at' => $livraison->created_at->format('d/m/Y H:i'),
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

     public function destroy($id)
    {
        try {
            // Ignorer les IDs non numériques
            if (!is_numeric($id)) {
                Log::info("Requête de suppression ignorée pour ID: {$id}");
                
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['success' => true, 'message' => 'Opération ignorée']);
                }
                return redirect()->route('livraison.index');
            }

            $livraison = Livraison::find($id);
            
            if (!$livraison) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['success' => false, 'error' => 'Récupération non trouvée'], 404);
                }
                return redirect()->route('livraison.index')->with('error', 'Récupération non trouvée.');
            }

            $reference = $livraison->reference;
            $livraison->delete();

            Log::info("Récupération supprimée: {$reference} (ID: {$id})");

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Récupération {$reference} supprimée avec succès."
                ]);
            }

            return redirect()->route('livraison.index')
                ->with('success', "Récupération {$reference} supprimée avec succès.");

        } catch (\Exception $e) {
            Log::error("Erreur suppression récupération ID {$id}: " . $e->getMessage());

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erreur lors de la suppression: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('livraison.index')
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
}
