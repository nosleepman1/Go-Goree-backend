<?php

namespace App\Http\Requests\Api\V1\Voyages;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Requête de validation pour la création d'un trajet.
 */
class StoreTrajetRequest extends FormRequest
{
    /**
     * Déterminer si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation appliquées à la requête.
     */
    public function rules(): array
    {
        return [
            'depart' => ['required', 'string', 'max:255'],
            'arrivee' => ['required', 'string', 'max:255'],
            'heure_depart' => ['required', 'date_format:H:i'],
            'duree' => ['required', 'integer', 'min:1'],
        ];
    }
}
