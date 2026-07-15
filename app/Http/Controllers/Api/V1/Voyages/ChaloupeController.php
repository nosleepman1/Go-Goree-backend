<?php

namespace App\Http\Controllers\Api\V1\Voyages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Voyages\StoreChaloupeRequest;
use App\Http\Requests\Api\V1\Voyages\UpdateChaloupeRequest;
use App\Models\Chaloupe;
use Illuminate\Http\Response;

/**
 * Contrôleur pour gérer les chaloupes (CRUD administratif).
 */
class ChaloupeController extends Controller
{
    /**
     * Liste des chaloupes.
     */
    public function index()
    {
        return response()->json(Chaloupe::paginate());
    }

    /**
     * Enregistrer une nouvelle chaloupe.
     */
    public function store(StoreChaloupeRequest $request)
    {
        $record = Chaloupe::create($request->validated());

        app(\App\Services\Logs\ActivityLogService::class)->log(
            "Création chaloupe", 
            "Chaloupe : {$record->nom} ({$record->imatriculation})"
        );

        return response()->json($record, Response::HTTP_CREATED);
    }

    /**
     * Afficher les détails d'une chaloupe spécifique.
     */
    public function show($id)
    {
        return response()->json(Chaloupe::findOrFail($id));
    }

    /**
     * Mettre à jour une chaloupe.
     */
    public function update(UpdateChaloupeRequest $request, $id)
    {
        $record = Chaloupe::findOrFail($id);
        $record->update($request->validated());

        app(\App\Services\Logs\ActivityLogService::class)->log(
            "Modification chaloupe", 
            "Chaloupe : {$record->nom} (Statut : {$record->statut})"
        );

        return response()->json($record);
    }

    /**
     * Supprimer une chaloupe.
     */
    public function destroy($id)
    {
        $record = Chaloupe::findOrFail($id);
        
        app(\App\Services\Logs\ActivityLogService::class)->log(
            "Suppression chaloupe", 
            "Chaloupe : {$record->nom}"
        );

        $record->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
