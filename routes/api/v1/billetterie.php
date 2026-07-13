<?php

use App\Http\Controllers\Api\V1\Billetterie\BilletController;
use App\Http\Controllers\Api\V1\Billetterie\PayementController;
use App\Http\Controllers\Api\V1\Billetterie\ScanController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('billets', BilletController::class);
    Route::apiResource('scans', ScanController::class);
    Route::apiResource('payements', PayementController::class);
});
