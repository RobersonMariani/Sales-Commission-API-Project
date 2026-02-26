<?php

use App\Api\Modules\Auth\Controllers\AuthController;
use App\Api\Modules\Report\Controllers\ReportController;
use App\Api\Modules\Sale\Controllers\SaleController;
use App\Api\Modules\Seller\Controllers\SellerController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['status' => 'ok']));

Route::prefix('auth')->group(function (): void {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function (): void {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::prefix('sellers')->middleware('auth:api')->group(function (): void {
    Route::post('/', [SellerController::class, 'store']);
    Route::get('/', [SellerController::class, 'index']);
    Route::get('/{seller}', [SellerController::class, 'show']);
    Route::post('/{seller}/resend-commission', [SellerController::class, 'resendCommission']);
});

Route::prefix('sales')->middleware('auth:api')->group(function (): void {
    Route::post('/', [SaleController::class, 'store']);
    Route::get('/', [SaleController::class, 'index']);
    Route::get('/{sale}', [SaleController::class, 'show']);
});

Route::prefix('reports')->middleware('auth:api')->group(function (): void {
    Route::get('/sales', [ReportController::class, 'salesSummary']);
    Route::get('/sales/by-seller', [ReportController::class, 'salesBySeller']);
    Route::get('/sales/daily', [ReportController::class, 'dailySales']);
});
