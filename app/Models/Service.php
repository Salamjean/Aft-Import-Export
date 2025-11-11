<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation',
        'prix_unitaire',
        'agence_destination_id',
        'description',
        'type_service'
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

    // Scope pour les services optionnels
    public function scopeOptionnels($query)
    {
        return $query->where('type_service', 'optionnel');
    }

    // Scope pour les services obligatoires
    public function scopeObligatoires($query)
    {
        return $query->where('type_service', 'obligatoire');
    }

    // Scope pour les services par agence
    public function scopeParAgence($query, $agenceId)
    {
        return $query->where('agence_destination_id', $agenceId);
    }
}