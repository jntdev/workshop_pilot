<?php

namespace App\Models;

use App\Enums\Metier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyKpi extends Model
{
    /** @use HasFactory<\Database\Factories\MonthlyKpiFactory> */
    use HasFactory;

    protected $fillable = [
        'metier',
        'year',
        'month',
        'invoice_count',
        'revenue_ht',
        'margin_ht',
    ];

    protected function casts(): array
    {
        return [
            'metier' => Metier::class,
            'year' => 'integer',
            'month' => 'integer',
            'invoice_count' => 'integer',
            'revenue_ht' => 'decimal:2',
            'margin_ht' => 'decimal:2',
        ];
    }
}
