<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    use HasFactory;

    protected $attributes = [
        'status' => 'draft',
    ];

    protected $fillable = [
        'reference',
        'client_id',
        'user_id',
        'status',
        'payment_method',
        'total_ht',
        'total_tva',
        'total_ttc',
        'completed_at',
        'cancelled_at',
    ];

    public function casts(): array
    {
        return [
            'status' => SaleStatus::class,
            'payment_method' => PaymentMethod::class,
            'total_ht' => 'integer',
            'total_tva' => 'integer',
            'total_ttc' => 'integer',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(SaleLine::class)->orderBy('position');
    }

    public function isDraft(): bool
    {
        return $this->status === SaleStatus::Draft;
    }

    public function isCompleted(): bool
    {
        return $this->status === SaleStatus::Completed;
    }

    public function isCancelled(): bool
    {
        return $this->status === SaleStatus::Cancelled;
    }

    public function recalculateTotals(): void
    {
        $lines = $this->lines()->get();

        $totalTtc = $lines->sum('line_total_ttc');
        $totalHt = 0;

        foreach ($lines as $line) {
            $totalHt += (int) round($line->line_total_ttc / (1 + $line->tva_rate / 100));
        }

        $this->update([
            'total_ttc' => $totalTtc,
            'total_ht' => $totalHt,
            'total_tva' => $totalTtc - $totalHt,
        ]);
    }

    public function marginHt(): int
    {
        $costHt = $this->lines->sum(
            fn (SaleLine $line) => (int) round((float) $line->quantity * $line->purchase_price_ht)
        );

        return $this->total_ht - $costHt;
    }

    public function complete(PaymentMethod $paymentMethod): void
    {
        DB::transaction(function () use ($paymentMethod): void {
            /** @var self $sale */
            $sale = self::query()->lockForUpdate()->findOrFail($this->id);

            if (! $sale->isDraft()) {
                throw new \DomainException('Cette vente ne peut plus être finalisée.');
            }

            if ($sale->lines()->count() === 0) {
                throw new \DomainException('Impossible de finaliser une vente sans article.');
            }

            foreach ($sale->lines as $line) {
                if ($line->article_id === null) {
                    continue;
                }

                $line->article->stockMovements()->create([
                    'quantity' => -abs((float) $line->quantity),
                    'type' => StockMovementType::SaleConsumption,
                    'source_type' => self::class,
                    'source_id' => $sale->id,
                    'unit_price_ht' => $line->purchase_price_ht,
                ]);
            }

            $sale->update([
                'reference' => 'VC-'.now()->format('Ymd').'-'.$sale->id,
                'status' => SaleStatus::Completed,
                'payment_method' => $paymentMethod,
                'completed_at' => now(),
            ]);

            $this->setRawAttributes($sale->getAttributes());
        });
    }

    public function cancel(): void
    {
        DB::transaction(function (): void {
            /** @var self $sale */
            $sale = self::query()->lockForUpdate()->findOrFail($this->id);

            if ($sale->isCancelled()) {
                return;
            }

            if (! $sale->isCompleted()) {
                throw new \DomainException('Seule une vente finalisée peut être annulée.');
            }

            foreach ($sale->lines as $line) {
                if ($line->article_id === null) {
                    continue;
                }

                $line->article->stockMovements()->create([
                    'quantity' => abs((float) $line->quantity),
                    'type' => StockMovementType::SaleReturn,
                    'source_type' => self::class,
                    'source_id' => $sale->id,
                    'unit_price_ht' => $line->purchase_price_ht,
                ]);
            }

            $sale->update([
                'status' => SaleStatus::Cancelled,
                'cancelled_at' => now(),
            ]);

            $this->setRawAttributes($sale->getAttributes());
        });
    }
}
