<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GroupEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    protected $emailService;
    
    public function __construct(GroupEmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    
    public function previewRecipients(Request $request)
    {
        $request->validate([
            'conteneur_id' => 'nullable|exists:conteneurs,id',
            'type_destinataire' => 'required|in:tous,expediteurs,destinataires'
        ]);
        
        // Debug: Voir ce qui est reçu
        Log::info('Preview recipients request:', $request->all());
        
        $emails = $this->emailService->getFilteredEmailsQuery($request->all());
        $count = count($emails);
        
        // Debug: Voir les emails récupérés
        Log::info('Emails found:', ['count' => $count, 'emails' => $emails]);
        
        // Pour debug, récupérer aussi les détails
        $debugInfo = $this->emailService->debugEmails($request->all());
        Log::info('Debug info:', $debugInfo);
        
        // Limiter l'affichage des emails pour ne pas surcharger l'interface
        $emailsPreview = $count > 10 ? array_slice($emails, 0, 10) : $emails;
        $moreCount = $count > 10 ? $count - 10 : 0;
        
        return response()->json([
            'count' => $count,
            'emails' => $emailsPreview,
            'moreCount' => $moreCount,
            'debug' => $debugInfo // À enlever en production
        ]);
    }
    
    public function sendGroupEmail(Request $request)
    {
        $request->validate([
            'sujet' => 'required|string|max:255',
            'contenu' => 'required|string',
            'conteneur_id' => 'nullable|exists:conteneurs,id',
            'type_destinataire' => 'nullable|in:tous,expediteurs,destinataires'
        ]);
        
        $filtres = [
            'conteneur_id' => $request->conteneur_id,
            'type_destinataire' => $request->type_destinataire
        ];
        
        $nombreDestinataires = $this->emailService->sendGroupEmail(
            $request->sujet,
            $request->contenu,
            $filtres
        );
        
        return back()->with('success', "Email envoyé avec succès à {$nombreDestinataires} destinataire(s).");
    }

    public function sendIndividualEmail(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'sujet' => 'required|string|max:255',
            'contenu' => 'required|string',
            'type_destinataire' => 'required|in:expediteur,destinataire'
        ]);
        
        $user = User::findOrFail($request->user_id);
        
        // Si c'est pour le destinataire, on prend l'email du destinataire du dernier colis
        if ($request->type_destinataire === 'destinataire') {
            $lastColis = $user->colis()->latest()->first();
            if (!$lastColis) {
                return back()->with('error', 'Aucun colis trouvé pour cet utilisateur.');
            }
            
            $email = $lastColis->email_destinataire;
            $name = $lastColis->name_destinataire;
        } else {
            // Pour l'expéditeur, on utilise l'utilisateur lui-même
            $email = $user->email;
            $name = $user->name;
        }
        
        // Envoyer l'email
        Mail::send('emails.individual_email', [
            'contenu' => $request->contenu,
            'name' => $name,
            'sujet' => $request->sujet
        ], function ($message) use ($email, $name, $request) {
            $message->to($email, $name)
                    ->subject($request->sujet);
        });
        
        return back()->with('success', "Email envoyé avec succès à {$name} ({$email})");
    }

    // Envoi groupé aux prospects
    public function sendProspectGroupEmail(Request $request)
    {
        $request->validate([
            'sujet' => 'required|string|max:255',
            'contenu' => 'required|string',
        ]);
        
        // Récupérer tous les prospects (utilisateurs sans colis)
        $prospects = User::whereDoesntHave('colis')->get();
        $count = 0;
        
        foreach ($prospects as $prospect) {
            Mail::send('emails.prospect_email', [
                'contenu' => $request->contenu,
                'user' => $prospect,
                'sujet' => $request->sujet
            ], function ($message) use ($prospect, $request) {
                $message->to($prospect->email, $prospect->name)
                        ->subject($request->sujet);
            });
            
            $count++;
        }
        
        return back()->with('success', "Email de prospection envoyé avec succès à {$count} prospect(s).");
    }

    // Envoi individuel aux prospects
    public function sendProspectIndividualEmail(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'sujet' => 'required|string|max:255',
            'contenu' => 'required|string',
        ]);
        
        $user = User::findOrFail($request->user_id);
        
        // Vérifier que c'est bien un prospect (pas de colis)
        if ($user->colis()->exists()) {
            return back()->with('error', 'Cet utilisateur a déjà envoyé des colis et n\'est pas un prospect.');
        }
        
        Mail::send('emails.prospect_email', [
            'contenu' => $request->contenu,
            'user' => $user,
            'sujet' => $request->sujet
        ], function ($message) use ($user, $request) {
            $message->to($user->email, $user->name)
                    ->subject($request->sujet);
        });
        
        return back()->with('success', "Email de prospection envoyé avec succès à {$user->name} ({$user->email})");
    }
}
