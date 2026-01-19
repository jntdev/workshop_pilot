<?php

namespace App\Models;

use App\Enums\QuoteStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'reference',
        'status',
        'invoiced_at',
        'valid_until',
        'discount_type',
        'discount_value',
        'total_ht',
        'total_tva',
        'total_ttc',
        'margin_total_ht',
    ];

    protected function casts(): array
    {
        return [
            'status' => QuoteStatus::class,
            'invoiced_at' => 'datetime',
            'valid_until' => 'date',
            'discount_value' => 'decimal:2',
            'total_ht' => 'decimal:2',
            'total_tva' => 'decimal:2',
            'total_ttc' => 'decimal:2',
            'margin_total_ht' => 'decimal:2',
        ];
    }

    // Méthodes pour le nouveau workflow simplifié (7.1)

    public function isInvoice(): bool
    {
        return $this->invoiced_at !== null;
    }

    public function isQuote(): bool
    {
        return $this->invoiced_at === null;
    }

    public function canEdit(): bool
    {
        return $this->isQuote();
    }

    public function canDelete(): bool
    {
        return $this->isQuote();
    }

    public function convertToInvoice(): void
    {
        if ($this->isInvoice()) {
            throw new \DomainException('Ce document est déjà une facture.');
        }

        $this->update([
            'invoiced_at' => now(),
            'status' => QuoteStatus::Invoiced,
        ]);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(QuoteLine::class)->orderBy('position');
    }

    // Méthodes de validation

    public function canBeInvoiced(): bool
    {
        return ! $this->hasIncompleteLines();
    }

    public function hasIncompleteLines(): bool
    {
        return $this->lines()->whereNull('purchase_price_ht')->exists();
    }

    public function getIncompleteLinesCount(): int
    {
        return $this->lines()->whereNull('purchase_price_ht')->count();
    }
}
