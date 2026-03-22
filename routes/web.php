<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\MobileUploadController;
use App\Services\Agenda\AgendaVersioner;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Routes publiques pour l'upload mobile (sans auth)
Route::get('/upload/{token}', [MobileUploadController::class, 'show'])->name('upload.show');
Route::post('/api/upload/{token}', [MobileUploadController::class, 'upload'])->name('upload.upload');

Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
        ->name('auth.google.redirect');

    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
        ->name('auth.google.callback');
});

// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {

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
                'origine_contact' => $client->origine_contact,
                'commentaires' => $client->commentaires,
                'avantage_type' => $client->avantage_type,
                'avantage_valeur' => $client->avantage_valeur,
                'avantage_expiration' => $client->avantage_expiration?->format('Y-m-d'),
            ]);

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
        ]);
    })->name('clients.index');

    Route::get('/clients/nouveau', function () {
        return Inertia::render('Clients/Form');
    })->name('clients.create');

    Route::get('/clients/{id}', function ($id) {
        $client = \App\Models\Client::findOrFail($id);

        return Inertia::render('Clients/Form', [
            'client' => [
                'id' => $client->id,
                'prenom' => $client->prenom,
                'nom' => $client->nom,
                'email' => $client->email,
                'telephone' => $client->telephone,
                'adresse' => $client->adresse,
                'origine_contact' => $client->origine_contact,
                'commentaires' => $client->commentaires,
                'avantage_type' => $client->avantage_type,
                'avantage_valeur' => $client->avantage_valeur,
                'avantage_expiration' => $client->avantage_expiration?->format('Y-m-d'),
            ],
        ]);
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
        return Inertia::render('Atelier/Quotes/Form');
    })->name('atelier.quotes.create');

    Route::get('/atelier/devis/{quote}', function (\App\Models\Quote $quote) {
        $quote->load('client', 'lines');

        return Inertia::render('Atelier/Quotes/Show', [
            'quote' => [
                'id' => $quote->id,
                'reference' => $quote->reference,
                'client_id' => $quote->client_id,
                'client' => [
                    'id' => $quote->client->id,
                    'prenom' => $quote->client->prenom,
                    'nom' => $quote->client->nom,
                    'email' => $quote->client->email,
                    'telephone' => $quote->client->telephone,
                    'adresse' => $quote->client->adresse,
                    'origine_contact' => $quote->client->origine_contact,
                    'commentaires' => $quote->client->commentaires,
                    'avantage_type' => $quote->client->avantage_type,
                    'avantage_valeur' => $quote->client->avantage_valeur,
                    'avantage_expiration' => $quote->client->avantage_expiration?->format('Y-m-d'),
                ],
                'bike_description' => $quote->bike_description,
                'reception_comment' => $quote->reception_comment,
                'remarks' => $quote->remarks,
                'valid_until' => $quote->valid_until->format('Y-m-d'),
                'discount_type' => $quote->discount_type,
                'discount_value' => $quote->discount_value,
                'total_ht' => $quote->total_ht,
                'total_tva' => $quote->total_tva,
                'total_ttc' => $quote->total_ttc,
                'margin_total_ht' => $quote->margin_total_ht,
                'total_estimated_time_minutes' => $quote->total_estimated_time_minutes,
                'actual_time_minutes' => $quote->actual_time_minutes,
                'invoiced_at' => $quote->invoiced_at?->toISOString(),
                'created_at' => $quote->created_at->toISOString(),
                'is_invoice' => $quote->isInvoice(),
                'can_edit' => $quote->canEdit(),
                'can_delete' => $quote->canDelete(),
                'lines' => $quote->lines->map(fn ($line) => [
                    'id' => $line->id,
                    'title' => $line->title,
                    'reference' => $line->reference,
                    'quantity' => $line->quantity,
                    'purchase_price_ht' => $line->purchase_price_ht,
                    'sale_price_ht' => $line->sale_price_ht,
                    'sale_price_ttc' => $line->sale_price_ttc,
                    'margin_amount_ht' => $line->margin_amount_ht,
                    'margin_rate' => $line->margin_rate,
                    'tva_rate' => $line->tva_rate,
                    'line_purchase_ht' => $line->line_purchase_ht,
                    'line_margin_ht' => $line->line_margin_ht,
                    'line_total_ht' => $line->line_total_ht,
                    'line_total_ttc' => $line->line_total_ttc,
                    'position' => $line->position,
                    'estimated_time_minutes' => $line->estimated_time_minutes,
                ])->toArray(),
            ],
        ]);
    })->name('atelier.quotes.show');

    Route::get('/atelier/devis/{quote}/modifier', function (\App\Models\Quote $quote) {
        $quote->load('client', 'lines');

        return Inertia::render('Atelier/Quotes/Form', [
            'quote' => [
                'id' => $quote->id,
                'reference' => $quote->reference,
                'client_id' => $quote->client_id,
                'client' => [
                    'id' => $quote->client->id,
                    'prenom' => $quote->client->prenom,
                    'nom' => $quote->client->nom,
                    'email' => $quote->client->email,
                    'telephone' => $quote->client->telephone,
                    'adresse' => $quote->client->adresse,
                    'origine_contact' => $quote->client->origine_contact,
                    'commentaires' => $quote->client->commentaires,
                    'avantage_type' => $quote->client->avantage_type,
                    'avantage_valeur' => $quote->client->avantage_valeur,
                    'avantage_expiration' => $quote->client->avantage_expiration?->format('Y-m-d'),
                ],
                'bike_description' => $quote->bike_description,
                'reception_comment' => $quote->reception_comment,
                'remarks' => $quote->remarks,
                'valid_until' => $quote->valid_until->format('Y-m-d'),
                'discount_type' => $quote->discount_type,
                'discount_value' => $quote->discount_value,
                'total_ht' => $quote->total_ht,
                'total_tva' => $quote->total_tva,
                'total_ttc' => $quote->total_ttc,
                'margin_total_ht' => $quote->margin_total_ht,
                'total_estimated_time_minutes' => $quote->total_estimated_time_minutes,
                'actual_time_minutes' => $quote->actual_time_minutes,
                'invoiced_at' => $quote->invoiced_at?->toISOString(),
                'created_at' => $quote->created_at->toISOString(),
                'is_invoice' => $quote->isInvoice(),
                'can_edit' => $quote->canEdit(),
                'can_delete' => $quote->canDelete(),
                'lines' => $quote->lines->map(fn ($line) => [
                    'id' => $line->id,
                    'title' => $line->title,
                    'reference' => $line->reference,
                    'quantity' => $line->quantity,
                    'purchase_price_ht' => $line->purchase_price_ht,
                    'sale_price_ht' => $line->sale_price_ht,
                    'sale_price_ttc' => $line->sale_price_ttc,
                    'margin_amount_ht' => $line->margin_amount_ht,
                    'margin_rate' => $line->margin_rate,
                    'tva_rate' => $line->tva_rate,
                    'line_purchase_ht' => $line->line_purchase_ht,
                    'line_margin_ht' => $line->line_margin_ht,
                    'line_total_ht' => $line->line_total_ht,
                    'line_total_ttc' => $line->line_total_ttc,
                    'position' => $line->position,
                    'estimated_time_minutes' => $line->estimated_time_minutes,
                ])->toArray(),
            ],
        ]);
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
            return redirect()->route('atelier.index')
                ->with('error', 'Impossible de supprimer une facture.');
        }

        $quote->delete();

        return redirect()->route('atelier.index')
            ->with('message', 'Devis supprimé avec succès.');
    })->name('atelier.quotes.destroy');

    // Page d'accueil = Location avec agenda ouvert par défaut
    $locationHandler = function (bool $openAgendaDefault = false) {
        return function (\Illuminate\Http\Request $request) use ($openAgendaDefault) {
            $year = now()->year;
            $openAgenda = $request->has('openAgenda') ? $request->boolean('openAgenda') : $openAgendaDefault;

            // Charger toutes les réservations de l'année (non annulées)
            $startYear = now()->startOfYear();
            $endYear = now()->endOfYear();

            $reservations = \App\Models\Reservation::with(['client', 'items', 'payments'])
                ->where('statut', '!=', 'annule')
                ->where(function ($query) use ($startYear, $endYear) {
                    // Réservations dont la période chevauche l'année
                    $query->where('date_retour', '>=', $startYear)
                        ->where('date_reservation', '<=', $endYear);
                })
                ->orderBy('date_reservation')
                ->get()
                ->map(fn ($r) => [
                    'id' => $r->id,
                    'client_id' => $r->client_id,
                    'client_name' => $r->client ? "{$r->client->prenom} {$r->client->nom}" : 'Client inconnu',
                    'client' => $r->client ? [
                        'id' => $r->client->id,
                        'prenom' => $r->client->prenom,
                        'nom' => $r->client->nom,
                        'email' => $r->client->email,
                        'telephone' => $r->client->telephone,
                        'adresse' => $r->client->adresse,
                        'origine_contact' => $r->client->origine_contact,
                        'commentaires' => $r->client->commentaires,
                        'avantage_type' => $r->client->avantage_type,
                        'avantage_valeur' => $r->client->avantage_valeur,
                        'avantage_expiration' => $r->client->avantage_expiration,
                    ] : null,
                    'date_contact' => $r->date_contact?->format('Y-m-d\TH:i'),
                    'date_reservation' => $r->date_reservation->format('Y-m-d'),
                    'date_retour' => $r->date_retour->format('Y-m-d'),
                    'livraison_necessaire' => $r->livraison_necessaire,
                    'adresse_livraison' => $r->adresse_livraison,
                    'contact_livraison' => $r->contact_livraison,
                    'creneau_livraison' => $r->creneau_livraison,
                    'recuperation_necessaire' => $r->recuperation_necessaire,
                    'adresse_recuperation' => $r->adresse_recuperation,
                    'contact_recuperation' => $r->contact_recuperation,
                    'creneau_recuperation' => $r->creneau_recuperation,
                    'prix_total_ttc' => $r->prix_total_ttc,
                    'acompte_demande' => $r->acompte_demande,
                    'acompte_montant' => $r->acompte_montant,
                    'acompte_paye_le' => $r->acompte_paye_le?->format('Y-m-d'),
                    'paiement_final_le' => $r->paiement_final_le?->format('Y-m-d'),
                    'statut' => $r->statut,
                    'raison_annulation' => $r->raison_annulation,
                    'commentaires' => $r->commentaires,
                    'color' => $r->color ?? 0,
                    'selection' => $r->selection ?? [],
                    'items' => $r->items->map(fn ($item) => [
                        'bike_type_id' => $item->bike_type_id,
                        'quantite' => $item->quantite,
                    ])->toArray(),
                    'payments' => $r->payments->map(fn ($p) => [
                        'id' => $p->id,
                        'amount' => $p->amount,
                        'method' => $p->method,
                        'paid_at' => $p->paid_at->format('Y-m-d\TH:i'),
                        'note' => $p->note,
                    ])->toArray(),
                    'total_paid' => $r->totalPaid(),
                    'remaining' => $r->remaining(),
                ]);

            // Charger les vélos depuis la base de données avec leurs relations
            $bikes = \App\Models\Bike::with(['category', 'size'])->ordered()->get()->map(fn ($bike) => [
                'id' => $bike->id,
                'column_id' => 'bike_'.$bike->id,
                'bike_category_id' => $bike->bike_category_id,
                'bike_size_id' => $bike->bike_size_id,
                'category' => $bike->category ? [
                    'id' => $bike->category->id,
                    'name' => $bike->category->name,
                    'color' => $bike->category->color,
                    'has_battery' => $bike->category->has_battery,
                    'sort_order' => $bike->category->sort_order,
                ] : null,
                'size' => $bike->size ? [
                    'id' => $bike->size->id,
                    'name' => $bike->size->name,
                    'color' => $bike->size->color,
                    'sort_order' => $bike->size->sort_order,
                ] : null,
                'frame_type' => $bike->frame_type,
                'model' => $bike->model,
                'battery_type' => $bike->battery_type,
                'name' => $bike->name,
                'status' => $bike->status,
                'notes' => $bike->notes,
            ]);

            // Charger les référentiels catégories et tailles
            $bikeCategories = \App\Models\BikeCategory::ordered()->get()->map(fn ($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'color' => $cat->color,
                'has_battery' => $cat->has_battery,
                'sort_order' => $cat->sort_order,
            ]);

            $bikeSizes = \App\Models\BikeSize::ordered()->get()->map(fn ($size) => [
                'id' => $size->id,
                'name' => $size->name,
                'color' => $size->color,
                'sort_order' => $size->sort_order,
            ]);

            return Inertia::render('Location/Index', [
                'bikes' => $bikes,
                'bikeCategories' => $bikeCategories,
                'bikeSizes' => $bikeSizes,
                'year' => $year,
                'reservations' => $reservations,
                'openAgenda' => $openAgenda,
                'agendaVersion' => app(AgendaVersioner::class)->current(),
            ]);
        };
    };

    Route::get('/', $locationHandler(true))->name('home');
    Route::get('/location', $locationHandler(false))->name('location.index');

    Route::get('/dashboard', function () {
        $year = now()->year;
        $month = now()->month;

        // Charger les KPIs du mois courant pour les 3 métiers
        $kpis = \App\Models\MonthlyKpi::whereIn('metier', ['vente', 'atelier', 'location'])
            ->where('year', $year)
            ->where('month', $month)
            ->get()
            ->keyBy('metier');

        // Charger les KPIs N-1 pour comparaison
        $kpisLastYear = \App\Models\MonthlyKpi::whereIn('metier', ['vente', 'atelier', 'location'])
            ->where('year', $year - 1)
            ->where('month', $month)
            ->get()
            ->keyBy('metier');

        $formatKpi = function (string $metier) use ($kpis, $kpisLastYear) {
            $current = $kpis->get($metier);
            $lastYear = $kpisLastYear->get($metier);

            $revenue = $current ? (float) $current->revenue_ht : 0;
            $margin = $current ? (float) $current->margin_ht : null;
            $count = $current ? $current->invoice_count : 0;
            $averageBasket = $count > 0 ? $revenue / $count : null;

            $revenueLastYear = $lastYear ? (float) $lastYear->revenue_ht : null;
            $trend = null;
            if ($revenueLastYear && $revenueLastYear > 0) {
                $trend = (($revenue - $revenueLastYear) / $revenueLastYear) * 100;
            }

            return [
                'metier' => $metier,
                'revenue' => $revenue,
                'margin' => $margin,
                'average_basket' => $averageBasket,
                'invoice_count' => $count,
                'trend' => $trend,
                'has_data' => $current !== null,
            ];
        };

        return Inertia::render('Dashboard', [
            'kpis' => [
                'vente' => $formatKpi('vente'),
                'atelier' => $formatKpi('atelier'),
                'location' => $formatKpi('location'),
            ],
            'period' => [
                'year' => $year,
                'month' => $month,
                'label' => now()->translatedFormat('F Y'),
            ],
        ]);
    })->name('dashboard');

    Route::get('/bikes', [\App\Http\Controllers\BikeController::class, 'index'])->name('bikes.index');

    Route::get('/messages', function () {
        return Inertia::render('Messages');
    })->name('messages.index');

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

    // Route de test composants
    Route::get('/test-composant', function () {
        return Inertia::render('TestComposant');
    })->name('test.composant');
});
