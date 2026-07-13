<?php

namespace App\Http\Requests\Api\V1\Voyages;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Requête de validation pour la création d'une chaloupe.
 */
class StoreChaloupeRequest extends FormRequest
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
            'nom' => ['required', 'string', 'max:255', 'unique:chaloupes,nom'],
            'capacite' => ['required', 'integer', 'min:1'],
        ];
    }
}
