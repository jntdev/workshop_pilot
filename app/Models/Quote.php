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
            'valid_until' => 'date',
            'discount_value' => 'decimal:2',
            'total_ht' => 'decimal:2',
            'total_tva' => 'decimal:2',
            'total_ttc' => 'decimal:2',
            'margin_total_ht' => 'decimal:2',
        ];
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

    // Méthodes de transition de statut

    public function markAsReady(): void
    {
        if (! $this->status->canTransitionTo(QuoteStatus::Ready)) {
            throw new \DomainException("Impossible de passer au statut 'prêt' depuis '{$this->status->label()}'");
        }

        $this->update(['status' => QuoteStatus::Ready]);
    }

    public function markAsModifiable(): void
    {
        if (! $this->status->canTransitionTo(QuoteStatus::Editable)) {
            throw new \DomainException("Impossible de passer au statut 'modifiable' depuis '{$this->status->label()}'");
        }

        $this->update(['status' => QuoteStatus::Editable]);
    }

    public function markAsInvoiced(): void
    {
        if (! $this->status->canTransitionTo(QuoteStatus::Invoiced)) {
            throw new \DomainException("Impossible de passer au statut 'facturé' depuis '{$this->status->label()}'");
        }

        if (! $this->canBeInvoiced()) {
            throw new \DomainException(
                "Impossible de facturer : {$this->getIncompleteLinesCount()} ligne(s) sans prix d'achat. Passez en brouillon pour les compléter."
            );
        }

        $this->update(['status' => QuoteStatus::Invoiced]);
    }
}
