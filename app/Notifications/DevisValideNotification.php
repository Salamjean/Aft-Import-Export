<?php

namespace App\Notifications;

use App\Models\Devis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DevisValideNotification extends Notification  
{
    use Queueable;

    public $devis;

    /**
     * Create a new notification instance.
     */
    public function __construct(Devis $devis)
    {
        $this->devis = $devis;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
            ->subject('🎉 Votre devis est prêt ! - ' . 'AFT-IMPORT-EXPORT')
            ->view('emails.devis_valide', ['devis' => $this->devis]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'devis_id' => $this->devis->id,
            'montant' => $this->devis->montant_devis,
            'message' => 'Votre devis a été validé avec le montant de ' . $this->devis->montant_devis . ' ' . $this->devis->devise,
        ];
    }
}