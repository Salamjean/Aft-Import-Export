<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Chauffeur extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'prenom',
        'email',
        'contact',
        'password',
        'agence_id',
        'email_verified_at',
        'archived_at'
    ];

    // Relation avec l'agence
    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }
}
