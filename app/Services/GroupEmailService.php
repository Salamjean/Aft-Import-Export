<?php

namespace App\Services;

use App\Models\User;
use App\Models\Conteneur;
use App\Models\Colis;
use App\Notifications\GroupEmailNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class GroupEmailService
{
    public function sendGroupEmail($sujet, $contenu, $filtres = [])
    {
        $emails = $this->getFilteredEmailsQuery($filtres);
        $count = 0;
        
        foreach ($emails as $email) {
            // Trouver l'utilisateur ou créer un objet utilisateur virtuel
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                // Si l'utilisateur n'existe pas, créer un objet virtuel pour l'email
                $user = new \stdClass();
                $user->email = $email;
                $user->name = $this->extractNameFromEmail($email);
            }
            
            // Envoyer l'email directement sans passer par les notifications
            Mail::send('emails.group_email', [
                'contenu' => $contenu,
                'user' => $user,
                'sujet' => $sujet
            ], function ($message) use ($user, $sujet) {
                $message->to($user->email, $user->name ?? '')
                        ->subject($sujet);
            });
            
            $count++;
        }
        
        return $count;
    }
    
    private function extractNameFromEmail($email)
    {
        $name = strstr($email, '@', true);
        return ucfirst($name);
    }
    
    public function getFilteredEmailsQuery($filtres)
    {
        // Initialiser les requêtes
        $expediteursQuery = Colis::select('email_expediteur as email');
        $destinatairesQuery = Colis::select('email_destinataire as email');
        
        // Appliquer le filtre conteneur aux deux requêtes
        if (!empty($filtres['conteneur_id'])) {
            $expediteursQuery->where('conteneur_id', $filtres['conteneur_id']);
            $destinatairesQuery->where('conteneur_id', $filtres['conteneur_id']);
        }
        
        // Construire la requête selon le type de destinataire
        if (empty($filtres['type_destinataire']) || $filtres['type_destinataire'] === 'tous') {
            // Union des deux requêtes
            $emails = $expediteursQuery->union($destinatairesQuery)->distinct()->pluck('email')->toArray();
        } elseif ($filtres['type_destinataire'] === 'expediteurs') {
            $emails = $expediteursQuery->distinct()->pluck('email')->toArray();
        } elseif ($filtres['type_destinataire'] === 'destinataires') {
            $emails = $destinatairesQuery->distinct()->pluck('email')->toArray();
        } else {
            $emails = [];
        }
        
        return $emails;
    }
    
    // Nouvelle méthode pour debug
    public function debugEmails($filtres)
    {
        $expediteurs = Colis::select('email_expediteur as email');
        $destinataires = Colis::select('email_destinataire as email');
        
        if (!empty($filtres['conteneur_id'])) {
            $expediteurs->where('conteneur_id', $filtres['conteneur_id']);
            $destinataires->where('conteneur_id', $filtres['conteneur_id']);
        }
        
        return [
            'expediteurs' => $expediteurs->distinct()->pluck('email')->toArray(),
            'destinataires' => $destinataires->distinct()->pluck('email')->toArray(),
            'type_demandé' => $filtres['type_destinataire'] ?? 'tous'
        ];
    }
}