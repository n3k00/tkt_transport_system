<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Parcel\ParcelSyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    Route::middleware('auth:sanctum')->prefix('parcels')->group(function (): void {
        Route::post('/sync', [ParcelSyncController::class, 'store']);
        Route::get('/tracking/{trackingId}', [ParcelSyncController::class, 'show']);
        Route::patch('/tracking/{trackingId}/status', [ParcelSyncController::class, 'updateStatus']);
    });
});
