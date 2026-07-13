<?php

namespace App\Models;

use App\Enums\MouvementPortefeuilleEnum;
use App\Enums\StatutMouvementEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MouvementPortefeuille extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'montant',
        'type',
        'payement_id',
        'statut',
        'portefeuille_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => MouvementPortefeuilleEnum::class,
            'statut' => StatutMouvementEnum::class,
            'montant' => 'decimal:2',
        ];
    }

    public function portefeuille()
    {
        return $this->belongsTo(Portefeuille::class);
    }

    public function payement()
    {
        return $this->belongsTo(Payement::class);
    }
}
