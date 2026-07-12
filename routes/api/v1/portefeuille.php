<?php

use App\Http\Controllers\Api\V1\Portefeuille\PortefeuilleController;
use App\Http\Controllers\Api\V1\Portefeuille\RechargeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('portefeuille', [PortefeuilleController::class, 'show']);
    Route::post('portefeuille/recharge', [RechargeController::class, 'store']);
});
