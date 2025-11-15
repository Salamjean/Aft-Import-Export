<?php

namespace App\Notifications;

use App\Models\DemandeRecuperation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeRecuperationSoumisNotification extends Notification
{
    use Queueable;

    public $demande;

    public function __construct(DemandeRecuperation $demande)
    {
        $this->demande = $demande;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouvelle demande de récupération - AFT-IMPORT-EXPORT')
            ->view('emails.demande_recuperation_soumis', ['demande' => $this->demande]);
    }

    public function toArray($notifiable)
    {
        return [
            'demande_id' => $this->demande->id,
            'reference' => $this->demande->reference,
            'client' => $this->demande->prenom_concerne . ' ' . $this->demande->nom_concerne,
            'nature_objet' => $this->demande->nature_objet,
            'quantite' => $this->demande->quantite,
        ];
    }
}