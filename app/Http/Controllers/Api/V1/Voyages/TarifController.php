<?php

namespace App\Http\Controllers\Api\V1\Voyages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Voyages\StoreTarifRequest;
use App\Http\Requests\Api\V1\Voyages\UpdateTarifRequest;
use App\Models\Tarif;
use Illuminate\Http\Response;

/**
 * Contrôleur pour gérer la grille tarifaire (CRUD administratif).
 */
class TarifController extends Controller
{
    /**
     * Liste des tarifs.
     */
    public function index()
    {
        return response()->json(Tarif::paginate());
    }

    /**
     * Enregistrer un nouveau tarif.
     */
    public function store(StoreTarifRequest $request)
    {
        $record = Tarif::create($request->validated());

        return response()->json($record, Response::HTTP_CREATED);
    }

    /**
     * Afficher les détails d'un tarif spécifique.
     */
    public function show($id)
    {
        return response()->json(Tarif::findOrFail($id));
    }

    /**
     * Mettre à jour un tarif.
     */
    public function update(UpdateTarifRequest $request, $id)
    {
        $record = Tarif::findOrFail($id);
        $record->update($request->validated());

        return response()->json($record);
    }

    /**
     * Supprimer un tarif.
     */
    public function destroy($id)
    {
        $record = Tarif::findOrFail($id);
        $record->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
