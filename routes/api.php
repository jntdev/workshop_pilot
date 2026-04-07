<?php

use App\Http\Controllers\Api\AtelierController;
use App\Http\Controllers\Api\BikeCategoryController;
use App\Http\Controllers\Api\BikeController;
use App\Http\Controllers\Api\BikeSizeController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\MessageCategoryController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PhotoController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\UploadTokenController;
use Illuminate\Support\Facades\Route;

// Health check endpoint (sans auth) pour warmup serveur mutualisé
Route::get('/health', fn () => response()->json(['status' => 'ok']));

Route::middleware(['web', 'auth'])->group(function () {
    // Dashboard API routes
    Route::post('/dashboard/kpis/rebuild', [AtelierController::class, 'rebuildAllKpis']);

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
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::put('/reservations/{id}', [ReservationController::class, 'update']);
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);
    Route::post('/reservations/{id}/send-acompte-email', [ReservationController::class, 'sendAcompteEmail']);
    Route::post('/reservations/send-acompte-email', [ReservationController::class, 'sendAcompteEmailDirect']);

    // Location API routes
    Route::get('/location/version', [LocationController::class, 'version']);
    Route::get('/location/full', [LocationController::class, 'full']);
    Route::get('/location/planning', [LocationController::class, 'planning']);

    // Bikes API routes
    Route::get('/bikes', [BikeController::class, 'index']);
    Route::post('/bikes', [BikeController::class, 'store']);
    Route::put('/bikes/{id}', [BikeController::class, 'update']);
    Route::delete('/bikes/{id}', [BikeController::class, 'destroy']);
    Route::post('/bikes/reorder', [BikeController::class, 'reorder']);

    // Bike Categories API routes
    Route::get('/bike-categories', [BikeCategoryController::class, 'index']);
    Route::post('/bike-categories', [BikeCategoryController::class, 'store']);
    Route::put('/bike-categories/{id}', [BikeCategoryController::class, 'update']);
    Route::delete('/bike-categories/{id}', [BikeCategoryController::class, 'destroy']);

    // Bike Sizes API routes
    Route::get('/bike-sizes', [BikeSizeController::class, 'index']);
    Route::post('/bike-sizes', [BikeSizeController::class, 'store']);
    Route::put('/bike-sizes/{id}', [BikeSizeController::class, 'update']);
    Route::delete('/bike-sizes/{id}', [BikeSizeController::class, 'destroy']);

    // Message categories API routes
    Route::get('/message-categories', [MessageCategoryController::class, 'index']);
    Route::post('/message-categories', [MessageCategoryController::class, 'store']);
    Route::put('/message-categories/{messageCategory}', [MessageCategoryController::class, 'update']);
    Route::delete('/message-categories/{messageCategory}', [MessageCategoryController::class, 'destroy']);
    Route::post('/message-categories/reorder', [MessageCategoryController::class, 'reorder']);

    // Messages API routes
    Route::get('/messages', [MessageController::class, 'index']);
    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::get('/messages/{message}', [MessageController::class, 'show']);
    Route::patch('/messages/{message}/read', [MessageController::class, 'markAsRead']);
    Route::patch('/messages/{message}/resolve', [MessageController::class, 'markAsResolved']);
    Route::patch('/messages/{message}/reopen', [MessageController::class, 'reopen']);
    Route::patch('/messages/{message}/category', [MessageController::class, 'updateCategory']);
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);
    Route::post('/messages/{message}/replies', [MessageController::class, 'storeReply']);
    Route::patch('/replies/{reply}/read', [MessageController::class, 'markReplyAsRead']);
    Route::put('/replies/{reply}', [MessageController::class, 'updateReply']);
    Route::delete('/replies/{reply}', [MessageController::class, 'destroyReply']);

    // Upload tokens API routes
    Route::post('/upload-tokens', [UploadTokenController::class, 'store']);
    Route::get('/upload-tokens/{token}/photos', [UploadTokenController::class, 'photos']);

    // Photos API routes
    Route::delete('/photos/{id}', [PhotoController::class, 'destroy']);
});
