<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
        ->name('auth.google.redirect');

    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
        ->name('auth.google.callback');
});

// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('home');

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/clients', function () {
        $clients = \App\Models\Client::query()
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get()
            ->map(fn ($client) => [
                'id' => $client->id,
                'prenom' => $client->prenom,
                'nom' => $client->nom,
                'email' => $client->email,
                'telephone' => $client->telephone,
                'adresse' => $client->adresse,
            ]);

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
        ]);
    })->name('clients.index');

    Route::get('/clients/nouveau', function () {
        return view('clients.create');
    })->name('clients.create');

    Route::get('/clients/{id}', function ($id) {
        return view('clients.show', ['clientId' => $id]);
    })->name('clients.show');

    Route::get('/atelier', function () {
        $year = now()->year;
        $month = now()->month;

        $availableYears = \App\Models\MonthlyKpi::where('metier', \App\Enums\Metier::Atelier)
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [$year];
        }

        $getStats = function (int $y, int $m) {
            $kpi = \App\Models\MonthlyKpi::where('metier', \App\Enums\Metier::Atelier)
                ->where('year', $y)
                ->where('month', $m)
                ->first();

            if (! $kpi) {
                return ['revenue' => 0, 'margin' => 0, 'count' => 0, 'margin_rate' => 0];
            }

            $revenue = (float) $kpi->revenue_ht;
            $margin = (float) $kpi->margin_ht;

            return [
                'revenue' => $revenue,
                'margin' => $margin,
                'count' => $kpi->invoice_count,
                'margin_rate' => $revenue > 0 ? ($margin / $revenue) * 100 : 0,
            ];
        };

        $quotes = \App\Models\Quote::with('client')
            ->whereNull('invoiced_at')
            ->latest()
            ->get()
            ->map(fn ($q) => [
                'id' => $q->id,
                'reference' => $q->reference,
                'client_id' => $q->client_id,
                'client' => [
                    'id' => $q->client->id,
                    'prenom' => $q->client->prenom,
                    'nom' => $q->client->nom,
                    'email' => $q->client->email,
                    'telephone' => $q->client->telephone,
                    'adresse' => $q->client->adresse,
                ],
                'bike_description' => $q->bike_description,
                'total_ht' => $q->total_ht,
                'total_tva' => $q->total_tva,
                'total_ttc' => $q->total_ttc,
                'margin_total_ht' => $q->margin_total_ht,
                'invoiced_at' => $q->invoiced_at?->toISOString(),
                'created_at' => $q->created_at->toISOString(),
                'can_delete' => $q->canDelete(),
                'is_invoice' => $q->isInvoice(),
            ]);

        return Inertia::render('Atelier/Index', [
            'stats' => $getStats($year, $month),
            'comparisonStats' => $getStats($year - 1, $month),
            'selectedYear' => $year,
            'selectedMonth' => $month,
            'availableYears' => $availableYears,
            'quotes' => $quotes,
            'invoices' => [],
        ]);
    })->name('atelier.index');

    Route::get('/atelier/devis/nouveau', function () {
        return view('atelier.quotes.create');
    })->name('atelier.quotes.create');

    Route::get('/atelier/devis/{quote}', function (\App\Models\Quote $quote) {
        $quote->load('client', 'lines');

        return view('atelier.quotes.show', ['quote' => $quote]);
    })->name('atelier.quotes.show');

    Route::get('/atelier/devis/{quote}/modifier', function (\App\Models\Quote $quote) {
        return view('atelier.quotes.edit', ['quote' => $quote]);
    })->name('atelier.quotes.edit');

    Route::get('/atelier/devis/{quote}/pdf', function (\App\Models\Quote $quote, \App\Services\PdfService $pdfService) {
        $quote->load('client', 'lines');

        if ($quote->isInvoice()) {
            return $pdfService->generateInvoicePdf($quote);
        }

        return $pdfService->generateQuotePdf($quote);
    })->name('atelier.quotes.pdf');

    Route::delete('/atelier/devis/{quote}', function (\App\Models\Quote $quote) {
        if (! $quote->canDelete()) {
            return redirect()->route('atelier.quotes.index')
                ->with('error', 'Impossible de supprimer une facture.');
        }

        $quote->delete();

        return redirect()->route('atelier.quotes.index')
            ->with('message', 'Devis supprimé avec succès.');
    })->name('atelier.quotes.destroy');

    Route::get('/location', function () {
        return Inertia::render('Location/Index');
    })->name('location.index');

    Route::get('/counter', function () {
        return view('counter-demo');
    })->name('counter');

    // Route de test React
    Route::get('/test-react', function () {
        return Inertia::render('Test', [
            'message' => 'Hello depuis Laravel!',
            'timestamp' => now()->format('d/m/Y H:i:s'),
        ]);
    })->name('test.react');
});
