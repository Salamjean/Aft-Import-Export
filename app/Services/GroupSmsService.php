<?php

namespace App\Services;

use App\Models\User;
use App\Models\Colis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroupSmsService
{
    protected $apiUrl;
    protected $apiKey;
    protected $senderId;

    // Allmysms
    protected $allmysmsLogin;
    protected $allmysmsApiKey;
    protected $allmysmsSenderId;

    public function __construct()
    {
        $this->apiUrl = config('services.yellika.api_url', 'https://app.1smsafrica.com/api/v3');
        $this->apiKey = config('services.yellika.api_key');
        // On conserve l'ancien pour Yellika si besoin
        $this->senderId = config('services.yellika.sender_id', 'Plateau app');

        // Configuration Allmysms
        $this->allmysmsLogin = env('ALLMYSMS_LOGIN', '');
        $this->allmysmsApiKey = env('ALLMYSMS_API_KEY', '');
        // Nouveau Sender ID pour Allmysms (par défaut on reprend l'ancien s'il n'est pas défini)
        $this->allmysmsSenderId = env('ALLMYSMS_SENDER_ID', $this->senderId);
    }

    // Ancien système d'envoi Yellika (conservé comme demandé)
    public function sendSmsYellika($recipient, $message)
    {
        $recipientClean = $this->cleanPhoneNumber($recipient);

        if (empty($recipientClean)) {
            Log::warning("Numéro de téléphone vide ou invalide: {$recipient}");
            return false;
        }

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->apiUrl . '/sms/send', [
                        'recipient' => $recipientClean,
                        'sender_id' => $this->senderId,
                        'type' => 'plain',
                        'message' => $message,
                    ]);

            if ($response->successful()) {
                Log::info("SMS envoyé avec succès à {$recipientClean}");
                return true;
            } else {
                Log::error("Erreur d'envoi SMS à {$recipientClean}: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Exception lors de l'envoi du SMS à {$recipientClean}: " . $e->getMessage());
            return false;
        }
    }

    // Nouveau système d'envoi Allmysms
    public function sendSms($recipient, $message)
    {
        $recipientClean = $this->cleanPhoneNumber($recipient);

        if (empty($recipientClean)) {
            Log::warning("Numéro de téléphone vide ou invalide: {$recipient}");
            return false;
        }

        // Ajout obligatoire de la mention STOP pour conserver l'expéditeur (Free, Orange, etc.)
        if (stripos($message, 'STOP') === false && str_starts_with($recipientClean, '33')) {
            $message .= ' - STOP au 36143';
        }

        try {
            $smsData = [
                'DATA' => [
                    'MESSAGE' => $message,
                    'TPOA' => $this->allmysmsSenderId,
                    'ALERTING' => 1,
                    'SMS' => [
                        ['MOBILEPHONE' => $recipientClean]
                    ]
                ]
            ];

            $response = Http::withoutVerifying()->asForm()->post('https://api.allmysms.com/http/9.0/sendSms/', [
                'login' => $this->allmysmsLogin,
                'apiKey' => $this->allmysmsApiKey,
                'smsData' => json_encode($smsData)
            ]);

            if ($response->successful()) {
                Log::info("SMS envoyé avec succès (Allmysms) à {$recipientClean}");
                return true;
            } else {
                Log::error("Erreur d'envoi SMS (Allmysms) à {$recipientClean}: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Exception lors de l'envoi du SMS (Allmysms) à {$recipientClean}: " . $e->getMessage());
            return false;
        }
    }

    public function sendGroupSms($message, $filtres = [])
    {
        $contacts = $this->getFilteredContactsQuery($filtres);
        $count = 0;

        foreach ($contacts as $contact) {
            if ($this->sendSms($contact, $message)) {
                $count++;
            }
        }

        return $count;
    }

    public function getFilteredContactsQuery($filtres)
    {
        $expediteursQuery = \Illuminate\Support\Facades\DB::table('colis')->select('contact_expediteur as contact');
        $destinatairesQuery = \Illuminate\Support\Facades\DB::table('colis')->select('contact_destinataire as contact');

        if (!empty($filtres['conteneur_id'])) {
            $expediteursQuery->where('conteneur_id', $filtres['conteneur_id']);
            $destinatairesQuery->where('conteneur_id', $filtres['conteneur_id']);
        }

        if (empty($filtres['type_destinataire']) || $filtres['type_destinataire'] === 'tous') {
            $contacts = $expediteursQuery->union($destinatairesQuery)->distinct()->pluck('contact')->toArray();
        } elseif ($filtres['type_destinataire'] === 'expediteurs') {
            $contacts = $expediteursQuery->distinct()->pluck('contact')->toArray();
        } elseif ($filtres['type_destinataire'] === 'destinataires') {
            $contacts = $destinatairesQuery->distinct()->pluck('contact')->toArray();
        } else {
            $contacts = [];
        }

        // Nettoyer et retourner uniquement les numéros uniques non vides
        $cleanedContacts = array_map([$this, 'cleanPhoneNumber'], $contacts);
        $cleanedContacts = array_filter($cleanedContacts);
        return array_values(array_unique($cleanedContacts));
    }

    public function debugContacts($filtres)
    {
        $expediteurs = \Illuminate\Support\Facades\DB::table('colis')->select('contact_expediteur as contact');
        $destinataires = \Illuminate\Support\Facades\DB::table('colis')->select('contact_destinataire as contact');

        if (!empty($filtres['conteneur_id'])) {
            $expediteurs->where('conteneur_id', $filtres['conteneur_id']);
            $destinataires->where('conteneur_id', $filtres['conteneur_id']);
        }

        $expediteursCleaned = array_values(array_filter(array_map([$this, 'cleanPhoneNumber'], $expediteurs->distinct()->pluck('contact')->toArray())));
        $destinatairesCleaned = array_values(array_filter(array_map([$this, 'cleanPhoneNumber'], $destinataires->distinct()->pluck('contact')->toArray())));

        return [
            'expediteurs' => $expediteursCleaned,
            'destinataires' => $destinatairesCleaned,
            'type_demandé' => $filtres['type_destinataire'] ?? 'tous'
        ];
    }

    public function cleanPhoneNumber($number)
    {
        if (empty($number)) {
            return null;
        }

        // Supprimer tous les caractères non numériques
        $cleaned = preg_replace('/[^0-9]/', '', $number);

        // Si le numéro fait 10 chiffres, on applique la logique par pays
        if (strlen($cleaned) == 10) {
            if (str_starts_with($cleaned, '06')) {
                // Numéro français commençant par 06 (on enlève le premier 0 et on ajoute 33)
                $cleaned = '33' . substr($cleaned, 1);
            } elseif (str_starts_with($cleaned, '01') || str_starts_with($cleaned, '05') || str_starts_with($cleaned, '07') || str_starts_with($cleaned, '08') || str_starts_with($cleaned, '09')) {
                // Numéro ivoirien (on garde le 0 et on ajoute 225)
                $cleaned = '225' . $cleaned;
            }
        }

        return $cleaned;
    }
}
