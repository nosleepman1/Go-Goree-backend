<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trajet extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'jour',
        'heure_depart',
        'duree',
    ];

    protected function casts(): array
    {
        return [
            'jour' => \App\Enums\JourEnum::class,
            'duree' => 'decimal:2',
        ];
    }

    public function voyages()
    {
        return $this->hasMany(Voyage::class);
    }
}
