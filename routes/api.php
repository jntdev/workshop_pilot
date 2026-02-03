<?php

use App\Http\Controllers\Api\AtelierController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\QuoteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    // Atelier API routes
    Route::get('/atelier/stats', [AtelierController::class, 'stats']);
    Route::get('/atelier/invoices', [AtelierController::class, 'invoices']);
    Route::get('/atelier/clients/search', [AtelierController::class, 'searchClients']);

    // Clients API routes
    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/clients/{id}', [ClientController::class, 'show']);
    Route::post('/clients', [ClientController::class, 'store']);
    Route::put('/clients/{id}', [ClientController::class, 'update']);
    Route::delete('/clients/{id}', [ClientController::class, 'destroy']);

    // Quotes API routes
    Route::get('/quotes/{quote}', [QuoteController::class, 'show']);
    Route::post('/quotes', [QuoteController::class, 'store']);
    Route::put('/quotes/{quote}', [QuoteController::class, 'update']);
    Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy']);
    Route::post('/quotes/{quote}/convert-to-invoice', [QuoteController::class, 'convertToInvoice']);
    Route::post('/quotes/calculate-line', [QuoteController::class, 'calculateLine']);
    Route::post('/quotes/calculate-totals', [QuoteController::class, 'calculateTotals']);
});
