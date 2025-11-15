<?php

namespace App\Http\Controllers\Admin\DemandeRecuperation;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\DemandeRecuperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminDeamndeRecuperation extends Controller
{
    public function index()
    {
        $demandes = DemandeRecuperation::with('agence', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $agences = Agence::orderBy('pays')->whereIn('pays',['France','Chine'])->orderBy('name')->get();
        
        // Statistiques
        $totalDemandes = DemandeRecuperation::count();
        $enAttenteCount = DemandeRecuperation::where('statut', 'en_attente')->count();
        $traiteCount = DemandeRecuperation::where('statut', 'traite')->count();
        $annuleCount = DemandeRecuperation::where('statut', 'annule')->count();

        return view('admin.recuperation.indexRecuperation', compact(
            'demandes',
            'agences',
            'totalDemandes',
            'enAttenteCount',
            'traiteCount',
            'annuleCount'
        ));
    }

    public function details($id)
{
    try {
        Log::info('Détails demande récupération ID: ' . $id);
        
        $demande = DemandeRecuperation::with('agence')->findOrFail($id);
        
        Log::info('Demande trouvée: ' . $demande->reference);
        
        return response()->json([
            'success' => true,
            'data' => [
                'reference' => $demande->reference,
                'nature_objet' => $demande->nature_objet,
                'quantite' => $demande->quantite,
                'nom_concerne' => $demande->nom_concerne,
                'prenom_concerne' => $demande->prenom_concerne,
                'contact' => $demande->contact,
                'email' => $demande->email,
                'type_recuperation' => $demande->type_recuperation,
                'adresse_recuperation' => $demande->adresse_recuperation,
                'date_recuperation' => $demande->date_recuperation ? $demande->date_recuperation: null,
                'statut' => $demande->statut,
                'created_at' => $demande->created_at->format('d/m/Y à H:i'),
                'agence' => [
                    'name' => $demande->agence->name,
                    'pays' => $demande->agence->pays,
                    'adresse' => $demande->agence->adresse,
                    'devise' => $demande->agence->devise,
                ]
            ]
        ]);
        
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        Log::error('Demande non trouvée ID: ' . $id);
        return response()->json([
            'success' => false,
            'error' => 'Demande non trouvée'
        ], 404);
        
    } catch (\Exception $e) {
        Log::error('Erreur détails demande récupération ID ' . $id . ': ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'error' => 'Erreur serveur: ' . $e->getMessage()
        ], 500);
    }
}

    public function traiter($id)
    {
        try {
            $demande = DemandeRecuperation::findOrFail($id);
            $demande->update(['statut' => 'traite']);

            return response()->json([
                'success' => true,
                'message' => 'Demande marquée comme traitée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function annuler($id)
    {
        try {
            $demande = DemandeRecuperation::findOrFail($id);
            $demande->update(['statut' => 'annule']);

            return response()->json([
                'success' => true,
                'message' => 'Demande annulée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}
