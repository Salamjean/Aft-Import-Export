<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ColisDestinataireNotification extends Notification
{
    use Queueable;

    public $emailData;

    /**
     * Create a new notification instance.
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
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
        $colis = $this->emailData['colis'];
        
        return (new MailMessage)
            ->subject('📦 Colis en route vers vous - ' . $colis->reference_colis . ' - AFT IMPORT EXPORT')
            ->view('emails.colis_destinataire', [
                'colis' => $colis,
                'emailData' => $this->emailData,
                'destinataire' => $notifiable
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'colis_id' => $this->emailData['colis']->id,
            'reference_colis' => $this->emailData['colis']->reference_colis,
            'message' => 'Notification de colis envoyée au destinataire'
        ];
    }
}