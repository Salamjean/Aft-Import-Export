<?php

namespace App\Http\Controllers\Agent\Devis;

use App\Http\Controllers\Controller;
use App\Models\Devis;
use App\Notifications\DevisValideNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgentDevisController extends Controller
{
     public function list()
    {
        // Récupérer l'agent connecté et son agence
        $agent = Auth::guard('agent')->user();
        $agenceId = $agent->agence_id;

        // Récupérer les statistiques filtrées par agence de destination
        $totalDevis = Devis::where('agence_destination_id', $agenceId)->count();
        $devisEnAttente = Devis::where('agence_destination_id', $agenceId)
                            ->where('statut', 'en_attente')
                            ->where('montant_devis', null)
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
        $devisTraites = Devis::where('agence_destination_id', $agenceId)
                            ->where('statut', 'traite')
                            ->where('montant_devis','!=',null)
                            ->count();
        $devisAnnules = Devis::where('agence_destination_id', $agenceId)
                            ->where('statut', 'annule')
                            ->count();

        return view('agent.devis.list', compact(
            'devisEnAttente',
            'totalDevis',
            'devisTraites',
            'devisAnnules',
            'agent'
        ));
    }

    public function confirmed()
    {
        // Récupérer l'agent connecté et son agence
        $agent = Auth::guard('agent')->user();
        $agenceId = $agent->agence_id;

        // Récupérer les statistiques filtrées par agence de destination
        $totalDevis = Devis::where('agence_destination_id', $agenceId)->count();
        $devisEnAttente = Devis::where('agence_destination_id', $agenceId)
                            ->where('montant_devis','!=',null)
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
        $devisTraites = Devis::where('agence_destination_id', $agenceId)
                            ->where('statut', 'traite')
                            ->where('montant_devis','!=',null)
                            ->count();
        $devisAnnules = Devis::where('agence_destination_id', $agenceId)
                            ->where('statut', 'annule')
                            ->count();

        return view('agent.devis.confirme', compact(
            'devisEnAttente',
            'totalDevis',
            'devisTraites',
            'devisAnnules',
            'agent'
        ));
    }

    public function getDevisDetails(Devis $devis)
    {
        // Vérifier que le devis appartient à l'agence de l'agent
        $agent = Auth::guard('agent')->user();
        if ($devis->agence_destination_id !== $agent->agence_id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        return response()->json($devis);
    }

    public function validerDevis(Request $request, Devis $devis)
    {
        // Vérifier que le devis appartient à l'agence de l'agent
        $agent = Auth::guard('agent')->user();
        if ($devis->agence_destination_id !== $agent->agence_id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé à ce devis.'
            ], 403);
        }

        $request->validate([
            'montant_devis' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Mise à jour des champs
            $devis->montant_devis = $request->montant_devis;
            $devis->statut = 'en_attente';
           

            // Sauvegarde en base
            $devis->save();

            // Envoi de la notification au client
            if ($devis->user) {
                $devis->user->notify(new DevisValideNotification($devis));
            } else {
                // Fallback si pas de relation user
                $this->sendEmailToClient($devis);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Devis validé avec succès ! Le client a été notifié par email.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur validation devis agent: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation du devis : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fallback pour envoyer l'email si pas de relation user
     */
    private function sendEmailToClient(Devis $devis)
    {
        try {
            // Créer un utilisateur temporaire pour la notification
            $tempUser = new \App\Models\User();
            $tempUser->email = $devis->email_client;
            $tempUser->name = $devis->name_client;
            $tempUser->notify(new DevisValideNotification($devis));
            
            Log::info('Email envoyé via fallback à: ' . $devis->email_client);
        } catch (\Exception $e) {
            Log::error('Erreur envoi email fallback: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Afficher tous les devis de l'agence (pour statistiques)
     */
    public function allDevis()
    {
        $agent = Auth::guard('agent')->user();
        $agenceId = $agent->agence_id;

        $devis = Devis::where('agence_destination_id', $agenceId)
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);

        return view('agent.devis.all', compact('devis', 'agent'));
    }

    /**
     * Marquer un devis comme traité
     */
    public function markAsTraite(Devis $devis)
    {
        $agent = Auth::guard('agent')->user();
        if ($devis->agence_destination_id !== $agent->agence_id) {
            return redirect()->back()->with('error', 'Accès non autorisé.');
        }

        try {
            $devis->statut = 'traite';
            $devis->save();

            return redirect()->back()->with('success', 'Devis marqué comme traité avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur marquage devis traité: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors du traitement du devis.');
        }
    }

    /**
     * Annuler un devis
     */
    public function annulerDevis(Request $request, Devis $devis)
    {
        $agent = Auth::guard('agent')->user();
        if ($devis->agence_destination_id !== $agent->agence_id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé.'
            ], 403);
        }

        $request->validate([
            'raison_annulation' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $devis->statut = 'annule';
            $devis->raison_annulation = $request->raison_annulation;
            $devis->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Devis annulé avec succès.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur annulation devis: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation du devis.'
            ], 500);
        }
    }
}
