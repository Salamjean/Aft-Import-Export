<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'prenom',
        'email',
        'password',
        'adresse',
        'pays',
        'contact',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relation pour les colis où l'utilisateur est l'expéditeur (propriétaire)
    public function colis()
    {
        return $this->hasMany(Colis::class, 'user_id');
    }

    // Relation pour les colis où l'utilisateur est expéditeur (par email)
    public function colisExpedies()
    {
        return $this->hasMany(Colis::class, 'email_expediteur', 'email');
    }

    // Relation pour les colis où l'utilisateur est destinataire (par email)
    public function colisDestines()
    {
        return $this->hasMany(Colis::class, 'email_destinataire', 'email');
    }

    // Scope pour les utilisateurs qui ont des colis (soit comme propriétaire, expéditeur ou destinataire)
    public function scopeAvecColis($query)
    {
        return $query->whereHas('colis')
            ->orWhereHas('colisExpedies')
            ->orWhereHas('colisDestines');
    }
}
