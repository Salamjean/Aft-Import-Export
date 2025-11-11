<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    use HasFactory;

    protected $fillable = [
        'mode_transit',
        'agence_expedition_id', 
        'agence_destination_id',
        'agence_expedition',
        'agence_destination',
        'pays_expedition',
        'name_client',
        'prenom_client',
        'email_client',
        'contact_client',
        'adresse_client',
        'devise',
        'colis', // Si vous stockez en JSON
        'user_id',
        'statut',
        'montant_devis',
        'reference_devis',
    ];

    protected $casts = [
        'colis' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agenceExpedition()
    {
        return $this->belongsTo(Agence::class, 'agence_expedition_id');
    }

    public function agenceDestination()
    {
        return $this->belongsTo(Agence::class, 'agence_destination_id');
    }
}