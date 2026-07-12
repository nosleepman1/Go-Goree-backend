<?php

namespace App\Http\Controllers\Api\V1\Voyages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Voyages\StoreVoyageRequest;
use App\Http\Requests\Api\V1\Voyages\UpdateVoyageRequest;
use App\Http\Resources\Api\V1\VoyageResource;
use App\Models\Voyage;
use Illuminate\Http\Response;

/**
 * Contrôleur pour gérer les instances de voyage d'une chaloupe (CRUD administratif).
 */
class VoyageController extends Controller
{
    /**
     * Liste des voyages programmés.
     */
    public function index()
    {
        return VoyageResource::collection(Voyage::with(['trajet', 'chaloupe'])->paginate());
    }

    /**
     * Enregistrer un nouveau voyage.
     */
    public function store(StoreVoyageRequest $request)
    {
        $record = Voyage::create($request->validated());

        return (new VoyageResource($record->load(['trajet', 'chaloupe'])))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Afficher les détails d'un voyage spécifique.
     */
    public function show($id)
    {
        $record = Voyage::with(['trajet', 'chaloupe'])->findOrFail($id);

        return new VoyageResource($record);
    }

    /**
     * Mettre à jour un voyage.
     */
    public function update(UpdateVoyageRequest $request, $id)
    {
        $record = Voyage::findOrFail($id);
        $record->update($request->validated());

        return new VoyageResource($record->load(['trajet', 'chaloupe']));
    }

    /**
     * Supprimer un voyage.
     */
    public function destroy($id)
    {
        $record = Voyage::findOrFail($id);
        $record->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
