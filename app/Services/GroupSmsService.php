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

    public function __construct()
    {
        $this->apiUrl = config('services.yellika.api_url', 'https://app.1smsafrica.com/api/v3');
        $this->apiKey = config('services.yellika.api_key');
        $this->senderId = config('services.yellika.sender_id', 'Plateau app');
    }

    public function sendSms($recipient, $message)
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

        // Si le numéro commence par + ou 00, on le traite comme international. Sinon, s'il fait 10 chiffres (Côte d'Ivoire), on ajoute 225
        // En Côte d'Ivoire, les numéros ont 10 chiffres. Ex: 0707XXXXXX -> 2250707XXXXXX
        if (strlen($cleaned) == 10 && (str_starts_with($cleaned, '01') || str_starts_with($cleaned, '05') || str_starts_with($cleaned, '07') || str_starts_with($cleaned, '08') || str_starts_with($cleaned, '09'))) {
            $cleaned = '225' . $cleaned;
        }

        return $cleaned;
    }
}
