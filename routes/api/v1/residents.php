<?php

use App\Http\Controllers\Api\V1\Residents\AbonnementController;
use App\Http\Controllers\Api\V1\Residents\DemandeResidenceController;
use App\Http\Controllers\Api\V1\Residents\ResidentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('residents', ResidentController::class);

    Route::post('demandes-residence/{id}/valider', [DemandeResidenceController::class, 'valider']);
    Route::post('demandes-residence/{id}/refuser', [DemandeResidenceController::class, 'refuser']);
    Route::apiResource('demandes-residence', DemandeResidenceController::class);

    Route::apiResource('abonnements', AbonnementController::class);
});
