<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'categorie',
        'prix',
    ];

    protected function casts(): array
    {
        return [
            'categorie' => \App\Enums\CategorieEnum::class,
            'prix' => 'decimal:2',
        ];
    }

    public function billets()
    {
        return $this->hasMany(Billet::class);
    }
}
