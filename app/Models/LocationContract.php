<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'token',
        'accessories',
        'caution_amount',
        'return_time_text',
        'operator_name',
        'contract_data',
        'signed_at',
        'signature_image',
        'signer_name',
        'pdf_path',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'accessories' => 'array',
            'contract_data' => 'array',
            'signed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function isPending(): bool
    {
        return is_null($this->signed_at) && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return is_null($this->signed_at) && $this->expires_at->isPast();
    }

    public function isSigned(): bool
    {
        return ! is_null($this->signed_at);
    }

    public function scopePending(Builder $query): void
    {
        $query->whereNull('signed_at')->where('expires_at', '>', now());
    }
}
