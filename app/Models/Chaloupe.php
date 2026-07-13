<?php

namespace App\Models;

use App\Enums\StatutChaloupeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chaloupe extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'imatriculation',
        'nom',
        'capacite',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'statut' => StatutChaloupeEnum::class,
        ];
    }

    public function voyages()
    {
        return $this->hasMany(Voyage::class);
    }
}
