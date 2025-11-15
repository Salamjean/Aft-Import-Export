<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeRecuperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'agence_id',
        'type_recuperation',
        'quantite',
        'nature_objet',
        'nom_concerne',
        'prenom_concerne',
        'contact',
        'email',
        'adresse_recuperation',
        'date_recuperation',
        'statut',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($demande) {
            $demande->reference = $demande->generateReference();
        });
    }

    public function generateReference()
    {
        $initiales = '';
        
        if (!empty($this->nom_concerne)) {
            $initiales .= strtoupper(substr($this->nom_concerne, 0, 2));
        }
        
        if (!empty($this->prenom_concerne)) {
            $initiales .= strtoupper(substr($this->prenom_concerne, 0, 1));
        }
        
        if (empty($initiales) && !empty($this->prenom_concerne)) {
            $initiales = strtoupper(substr($this->prenom_concerne, 0, 2));
        }
        
        if (empty($initiales)) {
            $initiales = 'CL';
        }

        $nombreAleatoire = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $reference = 'REC' . $initiales . $nombreAleatoire;

        // Vérifier l'unicité
        $count = DemandeRecuperation::where('reference', $reference)->count();
        if ($count > 0) {
            return $this->generateReference();
        }

        return $reference;
    }

    protected $casts = [
        'type_recuperation' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }
}