<?php

namespace App\Http\Controllers\Admin\Devis;

use App\Http\Controllers\Controller;
use App\Models\Devis;
use App\Models\User;
use App\Notifications\DevisValideNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminDevisController extends Controller
{
    public function list()
    {
        // Récupérer les statistiques
        $totalDevis = Devis::count();
        $devisEnAttente = Devis::where('statut', 'en_attente')
                            ->where('montant_devis', null)
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
        $devisTraites = Devis::where('statut', 'traite')->where('montant_devis','!=',null)->count();
        $devisAnnules = Devis::where('statut', 'annule')->count();

        return view('admin.devis.list', compact(
            'devisEnAttente',
            'totalDevis',
            'devisTraites',
            'devisAnnules'
        ));
    }

    public function confirmed()
    {
        // Récupérer les statistiques
        $totalDevis = Devis::count();
        $devisEnAttente = Devis::where('montant_devis','!=',null)
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
        $devisTraites = Devis::where('statut', 'traite')->where('montant_devis','!=',null)->count();
        $devisAnnules = Devis::where('statut', 'annule')->count();

        return view('admin.devis.confirme', compact(
            'devisEnAttente',
            'totalDevis',
            'devisTraites',
            'devisAnnules'
        ));
    }

    public function getDevisDetails(Devis $devis)
    {
        return response()->json($devis);
    }

    public function validerDevis(Request $request, Devis $devis)
    {
        $request->validate([
            'montant_devis' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Mise à jour des champs
            $devis->montant_devis = $request->montant_devis;
            $devis->statut = 'en_attente'; // Correction importante

            // Sauvegarde en base
            $devis->save();

            // Envoi de la notification au client via la relation user
            if ($devis->user) {
                $devis->user->notify(new DevisValideNotification($devis));
            } else {
                // Fallback si pas de relation user (pour les anciens devis)
                $this->sendEmailToClient($devis);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Devis validé avec succès ! Le client a été notifié par email.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur validation devis: ' . $e->getMessage());

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

}
