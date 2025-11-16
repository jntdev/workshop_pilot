<?php

use App\Http\Controllers\Api\ClientController;
use Illuminate\Support\Facades\Route;

Route::post('/clients', [ClientController::class, 'store']);
