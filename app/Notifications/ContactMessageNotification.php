<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactMessageNotification extends Notification
{
    use Queueable;

    public $contactData;

    public function __construct($contactData)
    {
        $this->contactData = $contactData;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouveau message de contact - AFT-IMPORT-EXPORT')
            ->view('emails.contact_message', ['contact' => $this->contactData]);
    }

    public function toArray($notifiable)
    {
        return [
            'name' => $this->contactData['name'],
            'email' => $this->contactData['email'],
            'subject' => $this->contactData['subject'],
        ];
    }
}