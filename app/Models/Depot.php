<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Depot extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'reference',
        'quantite',
        'nature_objet',
        'nom_concerne',
        'prenom_concerne',
        'contact',
        'email',
        'adresse_depot',
        'code_nature',
        'path_qrcode',
        'chauffeur_id',
        'date_depot',
        'statut',
    ];

    // Relation avec l'agence
    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }
}
