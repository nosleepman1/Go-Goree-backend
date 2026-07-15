<?php

use App\Http\Controllers\Api\V1\Settings\ParametreController;
use App\Http\Controllers\Api\V1\Users\ControleurController;
use App\Http\Controllers\Api\V1\Users\UserController;
use Illuminate\Support\Facades\Route;

// Administration des comptes : réservée aux administrateurs.
Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    // Comptes contrôleurs (agents) créés par un administrateur.
    Route::get('controleurs', [ControleurController::class, 'index']);
    Route::post('controleurs', [ControleurController::class, 'store']);

    Route::apiResource('users', UserController::class);

    // Paramètres généraux
    Route::get('settings', [ParametreController::class, 'index']);
    Route::put('settings', [ParametreController::class, 'update']);
});
