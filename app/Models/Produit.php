<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation',
        'prix_unitaire',
        'agence_destination_id'
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2'
    ];

    public function agenceDestination()
    {
        return $this->belongsTo(Agence::class, 'agence_destination_id');
    }

    // Accessor pour formater le prix
    public function getPrixUnitaireFormateAttribute()
    {
        return number_format($this->prix_unitaire, 2, ',', ' ') . ' XOF';
    }

    // Scope pour les produits par agence
    public function scopeParAgence($query, $agenceId)
    {
        return $query->where('agence_destination_id', $agenceId);
    }
}