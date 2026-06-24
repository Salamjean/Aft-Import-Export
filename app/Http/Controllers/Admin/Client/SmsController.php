<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use App\Models\Conteneur;
use App\Models\User;
use App\Services\GroupSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
    protected $smsService;

    public function __construct(GroupSmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function index()
    {
        // Récupérer les conteneurs distincts qui ont des colis
        $conteneurs = Conteneur::whereHas('colis')->get();
        // Récupérer les utilisateurs qui ont au moins un colis (en tant qu'expéditeur)
        $clients = User::whereHas('colis')->withCount('colis')->get();
        // Récupérer les utilisateurs qui n'ont JAMAIS envoyé de colis (prospects)
        $prospects = User::whereDoesntHave('colis')->get();

        return view('admin.client.sms', compact('conteneurs', 'clients', 'prospects'));
    }

    public function previewRecipients(Request $request)
    {
        $request->validate([
            'conteneur_id' => 'nullable|exists:conteneurs,id',
            'type_destinataire' => 'required|in:tous,expediteurs,destinataires'
        ]);

        $contacts = $this->smsService->getFilteredContactsQuery($request->all());
        $count = count($contacts);

        // Limiter l'affichage des contacts pour ne pas surcharger l'interface
        $contactsPreview = $count > 10 ? array_slice($contacts, 0, 10) : $contacts;
        $moreCount = $count > 10 ? $count - 10 : 0;

        return response()->json([
            'count' => $count,
            'contacts' => $contactsPreview,
            'moreCount' => $moreCount,
        ]);
    }

    public function sendGroupSms(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'conteneur_id' => 'nullable|exists:conteneurs,id',
            'type_destinataire' => 'nullable|in:tous,expediteurs,destinataires'
        ]);

        $filtres = [
            'conteneur_id' => $request->conteneur_id,
            'type_destinataire' => $request->type_destinataire
        ];

        $nombreDestinataires = $this->smsService->sendGroupSms(
            $request->message,
            $filtres
        );

        return back()->with('success', "SMS envoyé avec succès à {$nombreDestinataires} destinataire(s).");
    }

    public function sendIndividualSms(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
            'type_destinataire' => 'required|in:expediteur,destinataire'
        ]);

        $user = User::findOrFail($request->user_id);

        if ($request->type_destinataire === 'destinataire') {
            $lastColis = $user->colis()->latest()->first();
            if (!$lastColis) {
                return back()->with('error', 'Aucun colis trouvé pour cet utilisateur.');
            }
            $contact = $lastColis->contact_destinataire;
            $name = $lastColis->name_destinataire . ' ' . $lastColis->prenom_destinataire;
        } else {
            $contact = $user->contact;
            $name = $user->name . ' ' . $user->prenom;
        }

        if (empty($contact)) {
            return back()->with('error', "Numéro de téléphone non renseigné pour ce destinataire.");
        }

        $sent = $this->smsService->sendSms($contact, $request->message);

        if ($sent) {
            return back()->with('success', "SMS envoyé avec succès à {$name} ({$contact})");
        } else {
            return back()->with('error', "Erreur lors de l'envoi du SMS à {$name}.");
        }
    }

    public function sendProspectGroupSms(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        // Récupérer tous les prospects (utilisateurs sans colis)
        $prospects = User::whereDoesntHave('colis')->get();
        $count = 0;

        foreach ($prospects as $prospect) {
            if ($prospect->contact) {
                if ($this->smsService->sendSms($prospect->contact, $request->message)) {
                    $count++;
                }
            }
        }

        return back()->with('success', "SMS de prospection envoyé avec succès à {$count} prospect(s).");
    }

    public function sendProspectIndividualSms(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $user = User::findOrFail($request->user_id);

        if (empty($user->contact)) {
            return back()->with('error', "Numéro de téléphone non renseigné pour ce prospect.");
        }

        $sent = $this->smsService->sendSms($user->contact, $request->message);

        if ($sent) {
            return back()->with('success', "SMS de prospection envoyé avec succès à {$user->name} ({$user->contact})");
        } else {
            return back()->with('error', "Erreur lors de l'envoi du SMS à {$user->name}.");
        }
    }
}
