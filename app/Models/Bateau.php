<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bateau extends Model
{
    protected $fillable = [
        'type_transport',
        'conteneur_id',
        'reference',
        'date_arrive',
        'compagnie',
        'nom',
        'numero',
        'agence_id',
        'statut',
    ];

    protected $casts = [
        'date_depart' => 'datetime',
        'date_arrivee_prevue' => 'datetime',
    ];

    /**
     * Relation avec l'agence
     */
    public function agence()
    {
        return $this->belongsTo(Agence::class, 'agence_id');
    }

    /**
     * Relation avec les conteneurs (au pluriel)
     */
    public function conteneur()
    {
        return $this->belongsTo(Conteneur::class, 'conteneur_id');
    }

    /**
     * Scope pour filtrer par agence
     */
    public function scopeByAgence($query, $agenceId)
    {
        return $query->where('agence_id', $agenceId);
    }

    /**
     * Scope pour les bateaux actifs
     */
    public function scopeActif($query)
    {
        return $query->where('statut', 'actif');
    }

    /**
     * Accessor pour le nom complet du bateau
     */
    public function getNomCompletAttribute()
    {
        return $this->nom . ' (' . $this->numero . ')';
    }

    /**
     * Vérifier si le bateau a des conteneurs
     */
    public function hasConteneurs()
    {
        return $this->conteneurs()->exists();
    }

    /**
     * Nombre de conteneurs associés
     */
    public function getNombreConteneursAttribute()
    {
        return $this->conteneurs()->count();
    }
}
