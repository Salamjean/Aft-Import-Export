<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'colis_id',
        'montant',
        'methode_paiement',
        'nom_banque',
        'numero_compte',
        'operateur_mobile_money',
        'numero_mobile_money',
        'notes',
        'agent_id',
        'agent_type',
        'agent_name',
    ];

    public function colis()
    {
        return $this->belongsTo(Colis::class);
    }
}
