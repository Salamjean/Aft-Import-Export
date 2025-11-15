<?php

namespace App\Http\Controllers\User\DemandeRecuperation;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\DemandeRecuperation;
use App\Models\User;
use App\Notifications\DemandeRecuperationSoumisNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DemandeRecuperationController extends Controller
{
    public function create()
    {
        $agences = Agence::orderBy('pays')->orderBy('name')->whereIn('pays',['France','Chine'])->get();
        return view('user.demande_recuperation.demande-recuperation', compact('agences'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'agence_id' => 'required|exists:agences,id',
            'nature_objet' => 'required|string|max:255',
            'quantite' => 'required|integer|min:1|max:100',
            'nom_concerne' => 'required|string|max:255',
            'prenom_concerne' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'email' => 'nullable|email',
            'adresse_recuperation' => 'required|string',
            'date_recuperation' => 'nullable|date|after_or_equal:today'
        ]);

        try {
            $demande = new DemandeRecuperation();
            $demande->user_id = Auth::user()->id;
            $demande->agence_id = $validated['agence_id'];
            $demande->nature_objet = $validated['nature_objet'];
            $demande->quantite = $validated['quantite'];
            $demande->nom_concerne = $validated['nom_concerne'];
            $demande->prenom_concerne = $validated['prenom_concerne'];
            $demande->contact = $validated['contact'];
            $demande->email = $validated['email'] ?? null;
            $demande->adresse_recuperation = $validated['adresse_recuperation'];
            $demande->date_recuperation = $validated['date_recuperation'] ?? null;
            $demande->save();

            // Recharger la demande avec les relations
            $demande->load('agence');

            // Envoyer les notifications par email aux administrateurs
            $this->sendNotificationToAdmins($demande);

            return redirect()->back()->with('success', 'Votre demande de récupération a été soumise avec succès! Nous vous contacterons rapidement.');

        } catch (\Exception $e) {
            Log::error('Erreur création demande récupération: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'input' => $request->all()
            ]);
            
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la soumission de votre demande.');
        }
    }

    /**
     * Envoi de notification aux administrateurs
     */
    private function sendNotificationToAdmins(DemandeRecuperation $demande)
    {
        try {
            $emails = ['contact@aft-app.com', 'entrepot.paris@aft-app.com'];
            // $emails = ['salamjeanlouis3@gmail.com', 'ariellaarchelle@gmail.com'];
            foreach ($emails as $email) {
                // Créer un utilisateur temporaire pour la notification
                $tempUser = new User();
                $tempUser->email = $email;
                $tempUser->name = 'Administrateur';
                $tempUser->notify(new DemandeRecuperationSoumisNotification($demande));
            }
            
            Log::info('Notifications de nouvelle demande de récupération envoyées aux administrateurs', [
                'demande_id' => $demande->id,
                'reference' => $demande->reference
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur envoi notification admin demande récupération: ' . $e->getMessage(), [
                'demande_id' => $demande->id
            ]);
        }
    }

    public function index()
    {
        // Récupérer l'agent connecté
        $userId = Auth::user()->id;
        
        // Filtrer les demandes par l'agence de l'agent connecté
        $demandes = DemandeRecuperation::with('agence', 'user')
            ->where('user_id', $userId) // Filtrer par l'agence de l'agent
            ->orderBy('created_at', 'desc')
             ->where('statut','!=','annule') 
            ->paginate(10);

        // Récupérer seulement l'agence de l'agent pour les filtres
        $agences = Agence::get();
        
        // Statistiques filtrées par l'agence de l'agent
        $totalDemandes = DemandeRecuperation::where('user_id', $userId)->count();
        $enAttenteCount = DemandeRecuperation::where('user_id', $userId)
            ->where('statut', 'en_attente')->count();
        $traiteCount = DemandeRecuperation::where('user_id', $userId)
            ->where('statut', 'traite')->count();
        $annuleCount = DemandeRecuperation::where('user_id', $userId)
            ->where('statut', 'annule')->count();

        return view('user.demande_recuperation.index', compact(
            'demandes',
            'agences',
            'totalDemandes',
            'enAttenteCount',
            'traiteCount',
            'annuleCount',
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
