<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abonnement extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'date_debut',
        'date_fin',
        'montant',
        'resident_id',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'datetime',
            'date_fin' => 'datetime',
            'montant' => 'decimal:2',
        ];
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    /**
     * There is deliberately no `statut` column on this table — whether an
     * abonnement is active is derived from the date_fin column.
     */
    public function estActif(): bool
    {
        return $this->date_fin !== null && $this->date_fin->isFuture();
    }
}
