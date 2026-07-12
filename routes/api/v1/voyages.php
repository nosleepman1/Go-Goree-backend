<?php

use App\Http\Controllers\Api\V1\Voyages\ChaloupeController;
use App\Http\Controllers\Api\V1\Voyages\TarifController;
use App\Http\Controllers\Api\V1\Voyages\TrajetController;
use App\Http\Controllers\Api\V1\Voyages\VoyageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('voyages', VoyageController::class);
    Route::apiResource('trajets', TrajetController::class);
    Route::apiResource('chaloupes', ChaloupeController::class);
    Route::apiResource('tarifs', TarifController::class);
});
