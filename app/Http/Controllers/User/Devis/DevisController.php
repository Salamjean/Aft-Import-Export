<?php

namespace App\Http\Controllers\User\Devis;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\Devis;
use App\Models\User;
use App\Notifications\DevisSoumisNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class DevisController extends Controller
{
   public function enAttente(Request $request)
    {
        $user = Auth::user();
    
    // Compter les devis par statut
    $totalDevis = Devis::where('user_id', $user->id)->count();
    $devisEnAttente = Devis::where('user_id', $user->id)->where('statut', 'en_attente') ->where('montant_devis',null)->count();
    $devisTraites = Devis::where('user_id', $user->id)->where('statut', 'traite') ->where('montant_devis','!=',null)->count();
    $devisAnnules = Devis::where('user_id', $user->id)->where('statut', 'annule')->count();
    
    // Récupérer les devis avec filtre
    $devis = Devis::where('user_id', $user->id)
                ->when($request->statut, function($query, $statut) {
                    return $query->where('statut', $statut);
                })
                ->where('montant_devis',null)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
    
    return view('user.devis.attente', compact(
        'devis', 
        'totalDevis', 
        'devisEnAttente', 
        'devisTraites', 
        'devisAnnules'
    ));
    }

   public function confirmed(Request $request)
    {
        $user = Auth::user();
    
    // Compter les devis par statut
    $totalDevis = Devis::where('user_id', $user->id)->count();
    $devisEnAttente = Devis::where('user_id', $user->id)->where('statut', 'en_attente') ->where('montant_devis',null)->count();
    $devisTraites = Devis::where('user_id', $user->id)->where('statut', 'traite') ->where('montant_devis','!=',null)->count();
    $devisAnnules = Devis::where('user_id', $user->id)->where('statut', 'annule')->count();
    
    // Récupérer les devis avec filtre
    $devis = Devis::where('user_id', $user->id)
                ->when($request->statut, function($query, $statut) {
                    return $query->where('statut', $statut);
                })
                ->where('montant_devis','!=',null)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
    
    return view('user.devis.confirme', compact(
        'devis', 
        'totalDevis', 
        'devisEnAttente', 
        'devisTraites', 
        'devisAnnules'
    ));
    }

    public function accepter(Request $request, Devis $devis)
    {
        try {
            // Vérifier que le devis appartient à l'utilisateur connecté
            if ($devis->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé.'
                ], 403);
            }

            // Vérifier que le devis est en attente
            if ($devis->statut !== 'en_attente') {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce devis ne peut pas être accepté.'
                ], 400);
            }

            $modeLivraison = $request->input('mode_livraison');
            $reference = null;

            // Générer une référence seulement si "passe_recuperer" est choisi
            if ($modeLivraison === 'passe_recuperer') {
                $reference = $this->genererReference(Auth::user());
            }

            // Mettre à jour le devis
            $devis->update([
                'statut' => 'traite',
                'reference_devis' => $reference
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Devis accepté avec succès !',
                'reference' => $reference
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'acceptation du devis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Génère une référence aléatoire avec les initiales de l'utilisateur
     */
    private function genererReference($user)
    {
        // Récupérer les initiales
        $initiales = '';
        
        if (!empty($user->name)) {
            // Prendre les 2 premières lettres du name
            $initiales .= strtoupper(substr($user->name, 0, 2));
        }
        
        if (!empty($user->prenom)) {
            // Prendre la première lettre du prénom
            $initiales .= strtoupper(substr($user->prenom, 0, 1));
        }
        
        // Si pas de name, prendre les 2 premières lettres du prénom
        if (empty($initiales) && !empty($user->prenom)) {
            $initiales = strtoupper(substr($user->prenom, 0, 2));
        }
        
        // Si toujours pas d'initiales, utiliser "CL"
        if (empty($initiales)) {
            $initiales = 'CL';
        }

        // Générer un nombre aléatoire sur 4 chiffres
        $nombreAleatoire = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Combiner pour former la référence
        $reference = 'DEV' . $initiales . $nombreAleatoire;

        return $reference;
    }
        
   public function create()
    {
        $agences = Agence::all();
        $user = Auth::user();
        
        return view('user.devis.create', compact('agences', 'user'));
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'mode_transit' => 'required|string|in:Maritime,Aerien',
        'agence_expedition_id' => 'required|exists:agences,id',
        'agence_destination_id' => 'required|exists:agences,id',
        'pays_expedition' => 'required|string|in:France,Chine',
        'name_client' => 'required|string|max:255',
        'prenom_client' => 'required|string|max:255',
        'email_client' => 'required|email',
        'contact_client' => 'required|string',
        'adresse_client' => 'required|string',
        'colis' => 'required|array|min:1',
        'colis.*.quantite' => 'required|integer|min:1',
        'colis.*.produit' => 'required|string|max:255',
        'colis.*.valeur' => 'required|numeric|min:0',
        'colis.*.type_colis' => 'nullable|string',
        'colis.*.longueur' => 'nullable|numeric|min:0',
        'colis.*.largeur' => 'nullable|numeric|min:0',
        'colis.*.hauteur' => 'nullable|numeric|min:0',
        'colis.*.description' => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
        // Récupérer les agences
        $agenceExpedition = Agence::findOrFail($validated['agence_expedition_id']);
        $agenceDestination = Agence::findOrFail($validated['agence_destination_id']);

        $devis = Devis::create([
            'mode_transit' => $validated['mode_transit'],
            'agence_expedition_id' => $validated['agence_expedition_id'],
            'agence_destination_id' => $validated['agence_destination_id'],
            'agence_expedition' => $agenceExpedition->name,
            'agence_destination' => $agenceDestination->name,
            'pays_expedition' => $validated['pays_expedition'],
            'name_client' => $validated['name_client'],
            'prenom_client' => $validated['prenom_client'],
            'email_client' => $validated['email_client'],
            'contact_client' => $validated['contact_client'],
            'adresse_client' => $validated['adresse_client'],
            'devise' => $agenceDestination->devise,
            'colis' => $validated['colis'],
            'user_id' => Auth::id(),
            'statut' => 'en_attente',
        ]);

        DB::commit();

        // CORRECTION : Ajouter un log pour vérifier l'envoi
        Log::info('Devis créé avec succès, ID: ' . $devis->id);
        
        // Envoi des notifications aux administrateurs
        $this->sendNotificationToAdmins($devis);

        return redirect()
            ->route('user.devis.attente')
            ->with('success', 'Votre demande de tarification a été soumise avec succès!');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur création devis', [
            'error' => $e->getMessage(),
            'user_id' => Auth::id(),
            'input' => $request->except(['colis', 'password']),
            'trace' => $e->getTraceAsString() // Ajout du stack trace
        ]);
        
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Erreur lors de la soumission de votre demande. Veuillez réessayer.');
    }
}

    /**
     * Envoi de notification aux administrateurs
     */
    private function sendNotificationToAdmins(Devis $devis)
    {
        try {
            $emails = ['salamjeanlouis3@gmail.com', 'ariellaarchelle@gmail.com'];
            
            foreach ($emails as $email) {
                // Créer un utilisateur temporaire pour la notification
                $tempUser = new User();
                $tempUser->email = $email;
                $tempUser->name = 'Administrateur';
                $tempUser->notify(new DevisSoumisNotification($devis));
            }
            
            Log::info('Notifications de nouveau devis envoyées aux administrateurs');
        } catch (\Exception $e) {
            Log::error('Erreur envoi notification admin: ' . $e->getMessage());
            throw $e;
        }
    }

    public function annuler(Devis $devis)
    {
        // Vérifier que le devis appartient à l'utilisateur connecté
        if ($devis->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que le devis est bien en attente
        if ($devis->statut !== 'en_attente') {
            return redirect()->back()->with('error', 'Impossible d\'annuler ce devis.');
        }

        $devis->update(['statut' => 'annule']);

        return redirect()->route('user.devis.attente')
                        ->with('success', 'La demande de devis a été annulée avec succès.');
    }

    public function getDevisDetails(Devis $devis)
{
    // Vérifier que le devis appartient à l'utilisateur connecté
    if ($devis->user_id !== Auth::id()) {
        return response()->json(['error' => 'Accès non autorisé'], 403);
    }

    return response()->json($devis);
}
}
