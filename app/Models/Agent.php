<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Agent extends Authenticatable
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

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'archived_at' => 'datetime'
    ];

    // Relation avec l'agence
    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    // Scope pour les agents non archivés
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    // Scope pour les agents archivés
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    // Méthode pour archiver un agent
    public function archive()
    {
        $this->update(['archived_at' => now()]);
    }

    // Méthode pour restaurer un agent
    public function restoreAgent()
    {
        $this->update(['archived_at' => null]);
    }

    // Méthode pour vérifier si l'agent est archivé
    public function isArchived()
    {
        return !is_null($this->archived_at);
    }
}