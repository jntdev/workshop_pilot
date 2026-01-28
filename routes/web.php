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
        return view('home.dashboard');
    })->name('home');

    Route::get('/dashboard', function () {
        return view('home.dashboard');
    })->name('dashboard');

    Route::get('/clients', function () {
        return view('clients.index');
    })->name('clients.index');

    Route::get('/clients/nouveau', function () {
        return view('clients.create');
    })->name('clients.create');

    Route::get('/clients/{id}', function ($id) {
        return view('clients.show', ['clientId' => $id]);
    })->name('clients.show');

    Route::get('/atelier', function () {
        return view('atelier.index');
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
        return view('location.index');
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
