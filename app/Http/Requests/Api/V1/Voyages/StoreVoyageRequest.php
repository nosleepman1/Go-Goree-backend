<?php

namespace App\Http\Requests\Api\V1\Voyages;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Requête de validation pour la création d'un voyage.
 */
class StoreVoyageRequest extends FormRequest
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
            'date_voyage' => ['required', 'date', 'after_or_equal:today'],
            'places' => ['required', 'integer', 'min:1'],
            'places_restantes' => ['nullable', 'integer', 'min:0'],
            'trajet_id' => ['required', 'exists:trajets,id'],
            'chaloupe_id' => ['required', 'exists:chaloupes,id'],
        ];
    }
}
