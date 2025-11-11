<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recuperation extends Model
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
        'adresse_recuperation',
        'code_nature',
        'path_qrcode',
        'chauffeur_id',
        'date_recuperation',
        'statut'
    ];

    protected $casts = [
        'date_recuperation' => 'datetime',
    ];

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }
}