<?php

namespace App\Http\Requests\Api\V1\Voyages;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Requête de validation pour la mise à jour d'un trajet.
 */
class UpdateTrajetRequest extends FormRequest
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
            'depart' => ['sometimes', 'string', 'max:255'],
            'arrivee' => ['sometimes', 'string', 'max:255'],
            'heure_depart' => ['sometimes', 'date_format:H:i'],
            'duree' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
