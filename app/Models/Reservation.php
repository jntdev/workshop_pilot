<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'date_contact',
        'date_reservation',
        'date_retour',
        'livraison_necessaire',
        'adresse_livraison',
        'contact_livraison',
        'creneau_livraison',
        'recuperation_necessaire',
        'adresse_recuperation',
        'contact_recuperation',
        'creneau_recuperation',
        'prix_total_ttc',
        'acompte_demande',
        'acompte_montant',
        'acompte_paye_le',
        'paiement_final_le',
        'statut',
        'raison_annulation',
        'commentaires',
    ];

    protected function casts(): array
    {
        return [
            'date_contact' => 'datetime',
            'date_reservation' => 'date',
            'date_retour' => 'date',
            'livraison_necessaire' => 'boolean',
            'recuperation_necessaire' => 'boolean',
            'prix_total_ttc' => 'decimal:2',
            'acompte_demande' => 'boolean',
            'acompte_montant' => 'decimal:2',
            'acompte_paye_le' => 'date',
            'paiement_final_le' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReservationItem::class);
    }
}
