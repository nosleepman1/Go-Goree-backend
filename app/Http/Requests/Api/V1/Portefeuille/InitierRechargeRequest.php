<?php

namespace App\Http\Requests\Api\V1\Portefeuille;

use App\Enums\ModePayementEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * Requête de validation pour initier la recharge d'un portefeuille.
 */
class InitierRechargeRequest extends FormRequest
{
    /**
     * Déterminer si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtenir les règles de validation qui s'appliquent à la requête.
     */
    public function rules(): array
    {
        return [
            'montant' => ['required', 'numeric', 'min:100'],
            // Canal de paiement PayDunya. On interdit PORTEFEUILLE : on ne recharge
            // pas un portefeuille depuis lui-même.
            'payment_mode' => [
                'nullable',
                new Enum(ModePayementEnum::class),
                Rule::notIn([ModePayementEnum::PORTEFEUILLE->value]),
            ],
        ];
    }
}
