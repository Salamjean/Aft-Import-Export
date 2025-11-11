<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conteneur extends Model
{
    protected $fillable = [
        'name_conteneur',
        'type_conteneur', 
        'numero_conteneur',
        'agence_id'
    ];

    public function colis()
    {
        return $this->hasMany(Colis::class);
    }
    public function bateau()
    {
        return $this->hasMany(Bateau::class);
    }

    public function agence()
    {
        return $this->belongsTo(Agence::class, 'agence_id');
    }
}
