<?php

namespace App\Models;

use App\Enums\Metier;
use App\Enums\QuoteStatus;
use App\Services\Kpis\MonthlyKpiUpdater;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected $attributes = [
        'metier' => 'atelier',
        'status' => 'reception',
    ];

    protected $fillable = [
        'client_id',
        'bike_description',
        'reception_comment',
        'remarks',
        'client_notified',
        'client_notified_at',
        'work_completed_notified',
        'work_completed_notified_at',
        'metier',
        'reference',
        'status',
        'invoiced_at',
        'paid_at',
        'valid_until',
        'discount_type',
        'discount_value',
        'total_ht',
        'total_tva',
        'total_ttc',
        'margin_total_ht',
        'total_estimated_time_minutes',
        'actual_time_minutes',
        'is_archived',
        'email_note',
        'comments_resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'metier' => Metier::class,
            'status' => QuoteStatus::class,
            'invoiced_at' => 'datetime',
            'paid_at' => 'datetime',
            'client_notified' => 'boolean',
            'client_notified_at' => 'date',
            'work_completed_notified' => 'boolean',
            'work_completed_notified_at' => 'date',
            'valid_until' => 'date',
            'discount_value' => 'decimal:2',
            'total_ht' => 'decimal:2',
            'total_tva' => 'decimal:2',
            'total_ttc' => 'decimal:2',
            'margin_total_ht' => 'decimal:2',
            'total_estimated_time_minutes' => 'integer',
            'actual_time_minutes' => 'integer',
            'is_archived' => 'boolean',
            'comments_resolved_at' => 'datetime',
        ];
    }

    public function scopeArchived(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_archived', true);
    }

    public function scopeNotArchived(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_archived', false);
    }

    public function archive(): void
    {
        $this->update(['is_archived' => true]);
    }

    public function unarchive(): void
    {
        $this->update(['is_archived' => false]);
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

        // Générer une nouvelle référence pour la facture
        $today = now();
        $datePrefix = $today->format('Ymd');

        // Compter les factures créées aujourd'hui
        $countToday = self::whereNotNull('invoiced_at')
            ->whereDate('invoiced_at', $today->toDateString())
            ->count();

        $number = $countToday + 1;
        $newReference = sprintf('%s-%d', $datePrefix, $number);

        $this->update([
            'reference' => $newReference,
            'invoiced_at' => now(),
            'status' => QuoteStatus::Invoiced,
        ]);

        // Mettre à jour les KPIs mensuels
        app(MonthlyKpiUpdater::class)->applyInvoice($this);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(QuoteLine::class)->orderBy('position');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(QuoteComment::class)->orderBy('created_at')->orderBy('id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(QuotePayment::class)->orderBy('paid_at');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(QuoteAppointment::class)->orderBy('starts_at');
    }

    public function totalPaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    /**
     * Recalcule paid_at : date du paiement dont le cumul (dans l'ordre chronologique)
     * atteint ou dépasse le total TTC du devis. Null si le total n'est jamais atteint.
     */
    public function recalculatePaidAt(): void
    {
        $cumulative = 0.0;
        $paidAt = null;
        $total = (float) $this->total_ttc;

        foreach ($this->payments as $payment) {
            $cumulative += (float) $payment->amount;
            if ($total > 0 && $cumulative >= $total) {
                $paidAt = $payment->paid_at;
                break;
            }
        }

        $this->update(['paid_at' => $paidAt]);
    }

    public function hasOpenComments(): bool
    {
        return $this->comments_resolved_at === null && $this->comments()->exists();
    }

    public function markCommentsAsResolved(): void
    {
        $this->update(['comments_resolved_at' => now()]);
    }

    public function reopenComments(): void
    {
        $this->update(['comments_resolved_at' => null]);
    }

    /**
     * @return array<int, list<string>> quote_id => destinataires concernés, pour les devis ayant un fil de commentaires ouvert
     */
    public static function openCommentRecipientsByQuote(): array
    {
        return self::query()
            ->whereNull('comments_resolved_at')
            ->whereHas('comments')
            ->with('comments:id,quote_id,recipient_label')
            ->get()
            ->mapWithKeys(fn (Quote $quote) => [
                $quote->id => $quote->comments->pluck('recipient_label')->unique()->map(fn ($r) => $r->value)->values()->toArray(),
            ])
            ->toArray();
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
