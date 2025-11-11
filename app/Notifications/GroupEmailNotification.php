<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupEmailNotification extends Notification  
{
    use Queueable;

    public $sujet;
    public $contenu;
    public $typeDestinataire; // 'expediteur' ou 'destinataire'

    /**
     * Create a new notification instance.
     */
    public function __construct($sujet, $contenu, $typeDestinataire = null)
    {
        $this->sujet = $sujet;
        $this->contenu = $contenu;
        $this->typeDestinataire = $typeDestinataire;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->sujet)
            ->view('emails.group_email', [
                'contenu' => $this->contenu,
                'user' => $notifiable
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'sujet' => $this->sujet,
            'contenu' => $this->contenu,
        ];
    }
}