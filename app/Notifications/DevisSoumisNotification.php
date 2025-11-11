<?php

namespace App\Notifications;

use App\Models\Devis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DevisSoumisNotification extends Notification
{
    use Queueable;

    public $devis;

    public function __construct(Devis $devis)
    {
        $this->devis = $devis;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouvelle demande de devis - AFT-IMPORT-EXPORT')
            ->view('emails.devis_soumis', ['devis' => $this->devis]);
    }

    public function toArray($notifiable)
    {
        return [
            'devis_id' => $this->devis->id,
            'client' => $this->devis->prenom_client . ' ' . $this->devis->name_client,
            'mode_transit' => $this->devis->mode_transit,
        ];
    }
}