<?php

namespace App\Services\Kpis;

use App\Enums\Metier;
use App\Models\MonthlyKpi;
use App\Models\Quote;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class MonthlyKpiUpdater
{
    public function applyInvoice(Quote $quote): void
    {
        if ($quote->invoiced_at === null) {
            throw new \InvalidArgumentException('La quote doit être une facture (invoiced_at non null).');
        }

        $year = $quote->invoiced_at->year;
        $month = $quote->invoiced_at->month;
        $metier = $quote->metier->value;

        DB::transaction(function () use ($metier, $year, $month, $quote) {
            $kpi = MonthlyKpi::lockForUpdate()
                ->firstOrCreate(
                    [
                        'metier' => $metier,
                        'year' => $year,
                        'month' => $month,
                    ],
                    [
                        'invoice_count' => 0,
                        'revenue_ht' => 0,
                        'margin_ht' => 0,
                    ]
                );

            $kpi->increment('invoice_count');
            $kpi->increment('revenue_ht', $quote->total_ht);
            $kpi->increment('margin_ht', $quote->margin_total_ht);
        });
    }

    /**
     * Recalcule les KPIs Location pour une réservation donnée.
     * Appelé lors de la création/modification des paiements.
     */
    public function syncReservationPayments(Reservation $reservation): void
    {
        $reservation->load('payments');

        // Grouper les paiements par mois
        $paymentsByMonth = [];

        foreach ($reservation->payments as $payment) {
            $key = $payment->paid_at->year.'-'.$payment->paid_at->month;
            if (! isset($paymentsByMonth[$key])) {
                $paymentsByMonth[$key] = [
                    'year' => $payment->paid_at->year,
                    'month' => $payment->paid_at->month,
                    'total' => 0,
                ];
            }
            $paymentsByMonth[$key]['total'] += (float) $payment->amount;
        }

        // Ajouter l'acompte si payé
        if ($reservation->acompte_paye_le && $reservation->acompte_montant > 0) {
            $key = $reservation->acompte_paye_le->year.'-'.$reservation->acompte_paye_le->month;
            if (! isset($paymentsByMonth[$key])) {
                $paymentsByMonth[$key] = [
                    'year' => $reservation->acompte_paye_le->year,
                    'month' => $reservation->acompte_paye_le->month,
                    'total' => 0,
                ];
            }
            $paymentsByMonth[$key]['total'] += (float) $reservation->acompte_montant;
        }

        // Mettre à jour les KPIs pour chaque mois impacté
        foreach ($paymentsByMonth as $data) {
            $this->rebuildLocationKpiForMonth($data['year'], $data['month']);
        }
    }

    /**
     * Recalcule le KPI Location pour un mois donné à partir des paiements.
     */
    public function rebuildLocationKpiForMonth(int $year, int $month): void
    {
        DB::transaction(function () use ($year, $month) {
            // Calculer le total des paiements pour ce mois
            $paymentsTotal = \App\Models\ReservationPayment::whereYear('paid_at', $year)
                ->whereMonth('paid_at', $month)
                ->sum('amount');

            // Calculer le total des acomptes payés ce mois
            $acomptesTotal = Reservation::whereYear('acompte_paye_le', $year)
                ->whereMonth('acompte_paye_le', $month)
                ->sum('acompte_montant');

            // Total TTC (les paiements et acomptes sont en TTC)
            $totalTtc = (float) $paymentsTotal + (float) $acomptesTotal;

            // Convertir TTC en HT avec le taux de TVA configuré
            $tvaRate = config('location.tva_rate', 20);
            $totalHt = $totalTtc / (1 + $tvaRate / 100);

            // Compter le nombre de réservations avec au moins un paiement ce mois
            $reservationIds = \App\Models\ReservationPayment::whereYear('paid_at', $year)
                ->whereMonth('paid_at', $month)
                ->distinct()
                ->pluck('reservation_id');

            $reservationsWithAcompte = Reservation::whereYear('acompte_paye_le', $year)
                ->whereMonth('acompte_paye_le', $month)
                ->pluck('id');

            $uniqueReservations = $reservationIds->merge($reservationsWithAcompte)->unique()->count();

            $kpi = MonthlyKpi::lockForUpdate()
                ->firstOrCreate(
                    [
                        'metier' => Metier::Location->value,
                        'year' => $year,
                        'month' => $month,
                    ],
                    [
                        'invoice_count' => 0,
                        'revenue_ht' => 0,
                        'revenue_ttc' => 0,
                        'margin_ht' => 0,
                    ]
                );

            $kpi->update([
                'revenue_ht' => round($totalHt, 2),
                'revenue_ttc' => round($totalTtc, 2),
                'invoice_count' => $uniqueReservations,
                'margin_ht' => 0, // Marge non disponible pour Location
            ]);
        });
    }
}
