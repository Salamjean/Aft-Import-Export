<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colis extends Model
{
    use HasFactory;

    protected $fillable = [
        'conteneur_id',
        'reference_colis',
        'mode_transit',
        'agence_expedition_id',
        'agence_destination_id',
        'agence_destination',
        'agence_expedition',
        'devise',

        // Informations expéditeur
        'name_expediteur',
        'prenom_expediteur',
        'email_expediteur',
        'contact_expediteur',
        'adresse_expediteur',

        // Informations destinataire
        'name_destinataire',
        'prenom_destinataire',
        'email_destinataire',
        'indicatif',
        'contact_destinataire',
        'adresse_destinataire',

        // Colis
        'colis',
        'montant_colis',
        'montant_paye_colis',
        'user_id',
        'statut',

        // Services
        'service_id',
        'prix_service',
        'montant_total',

        // Paiement
        'methode_paiement',
        'nom_banque',
        'numero_compte',
        'operateur_mobile_money',
        'numero_mobile_money',
        'montant_espece',
        'montant_virement',
        'montant_cheque',
        'montant_mobile_money',
        'montant_livraison',
        'montant_paye',
        'reste_a_payer',
        'statut_paiement',
        'notes_paiement',
        'qr_codes',
        'code_colis',
        'statuts_individuels',
        'agent_encaisseur_id',
        'agent_encaisseur_type',
        'agent_encaisseur_name',
        'date_paiement',
    ];

    protected $casts = [
        'colis' => 'array',
        'montant_colis' => 'decimal:2',
        'montant_paye_colis' => 'decimal:2',
        'prix_service' => 'decimal:2',
        'montant_total' => 'decimal:2',
        'montant_espece' => 'decimal:2',
        'montant_virement' => 'decimal:2',
        'montant_cheque' => 'decimal:2',
        'montant_mobile_money' => 'decimal:2',
        'montant_livraison' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'reste_a_payer' => 'decimal:2',
    ];

    // Relations
    public function conteneur()
    {
        return $this->belongsTo(Conteneur::class);
    }

    public function agenceExpedition()
    {
        return $this->belongsTo(Agence::class, 'agence_expedition_id');
    }

    public function agenceDestination()
    {
        return $this->belongsTo(Agence::class, 'agence_destination_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    // Méthodes pour le paiement
    public function getMontantPaiementAttribute()
    {
        switch ($this->methode_paiement) {
            case 'espece':
                return $this->montant_espece;
            case 'virement_bancaire':
                return $this->montant_virement;
            case 'cheque':
                return $this->montant_cheque;
            case 'mobile_money':
                return $this->montant_mobile_money;
            case 'livraison':
                return $this->montant_livraison;
            default:
                return 0;
        }
    }

    public function getDetailsPaiementAttribute()
    {
        switch ($this->methode_paiement) {
            case 'virement_bancaire':
                return "Banque: {$this->nom_banque}, Compte: {$this->numero_compte}";
            case 'mobile_money':
                return "Opérateur: {$this->operateur_mobile_money}, Numéro: {$this->numero_mobile_money}";
            case 'espece':
                return "Paiement en espèces";
            case 'cheque':
                return "Paiement par chèque";
            case 'livraison':
                return "Paiement à la livraison";
            default:
                return "Non spécifié";
        }
    }

    public function scopeAvecStatutIndividuel($query, $statut)
    {
        return $query->where('statuts_individuels', 'LIKE', '%"statut":"' . $statut . '"%');
    }
}