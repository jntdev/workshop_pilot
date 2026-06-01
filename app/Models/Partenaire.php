<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Partenaire extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'nom',
        'email',
        'password',
        'actif',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'actif' => 'boolean',
        ];
    }

    public function scopeActif($query): void
    {
        $query->where('actif', true);
    }

    public function demandes(): HasMany
    {
        return $this->hasMany(DemandePartenaire::class);
    }
}
