<?php

namespace App\Http\Requests\Api\V1\Residents;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Requête de validation pour la soumission d'une demande de résidence.
 */
class StoreDemandeResidenceRequest extends FormRequest
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
            'carte_identite' => ['required', 'string'],
            'residence' => ['required', 'string'],
            'photo' => ['required', 'string'],
        ];
    }
}
