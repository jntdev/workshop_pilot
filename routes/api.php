<?php

use App\Http\Controllers\Api\AtelierController;
use App\Http\Controllers\Api\ClientController;
use Illuminate\Support\Facades\Route;

// Atelier API routes
Route::get('/atelier/stats', [AtelierController::class, 'stats']);
Route::get('/atelier/invoices', [AtelierController::class, 'invoices']);
Route::get('/atelier/clients/search', [AtelierController::class, 'searchClients']);

Route::get('/clients', [ClientController::class, 'index']);
Route::get('/clients/{id}', [ClientController::class, 'show']);
Route::post('/clients', [ClientController::class, 'store']);
Route::put('/clients/{id}', [ClientController::class, 'update']);
Route::delete('/clients/{id}', [ClientController::class, 'destroy']);
