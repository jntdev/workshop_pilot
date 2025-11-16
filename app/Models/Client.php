<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'prenom',
        'nom',
        'telephone',
        'email',
        'adresse',
        'origine_contact',
        'commentaires',
        'avantage_type',
        'avantage_valeur',
        'avantage_expiration',
        'avantage_applique',
        'avantage_applique_le',
    ];

    protected function casts(): array
    {
        return [
            'avantage_valeur' => 'decimal:2',
            'avantage_applique' => 'boolean',
            'avantage_expiration' => 'datetime',
            'avantage_applique_le' => 'datetime',
        ];
    }
}
