<?php

use App\Http\Controllers\Api\AtelierController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\ReservationController;
use Illuminate\Support\Facades\Route;

// Health check endpoint (sans auth) pour warmup serveur mutualisé
Route::get('/health', fn () => response()->json(['status' => 'ok']));

Route::middleware(['web', 'auth'])->group(function () {
    // Atelier API routes
    Route::get('/atelier/stats', [AtelierController::class, 'stats']);
    Route::post('/atelier/stats/rebuild', [AtelierController::class, 'rebuildStats']);
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
    Route::patch('/quotes/{quote}/actual-time', [QuoteController::class, 'updateActualTime']);
    Route::post('/quotes/{quote}/send-email', [QuoteController::class, 'sendEmail']);
    Route::post('/quotes/calculate-line', [QuoteController::class, 'calculateLine']);
    Route::post('/quotes/calculate-totals', [QuoteController::class, 'calculateTotals']);

    // Reservations API routes
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/reservations/window', [ReservationController::class, 'window']);
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::put('/reservations/{id}', [ReservationController::class, 'update']);
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);

    // Location API routes
    Route::get('/location/planning', [LocationController::class, 'planning']);
});
