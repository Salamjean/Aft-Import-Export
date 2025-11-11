<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livraison extends Model
{
     use HasFactory;

    protected $fillable = [
        'reference',
        'quantite',
        'nature_objet',
        'nom_concerne',
        'prenom_concerne',
        'contact',
        'email',
        'adresse_livraison',
        'chauffeur_id',
        'date_livraison',
        'statut',
        'quantite_scannee'
    ];

    protected $casts = [
        'date_livraison' => 'datetime',
    ];

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }
}
