<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarif extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

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
