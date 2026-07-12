<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voyage extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'date_voyage',
        'places',
        'places_restantes',
        'trajet_id',
        'chaloupe_id',
    ];

    protected function casts(): array
    {
        return [
            'date_voyage' => 'date',
        ];
    }

    public function trajet()
    {
        return $this->belongsTo(Trajet::class);
    }

    public function chaloupe()
    {
        return $this->belongsTo(Chaloupe::class);
    }

    public function billets()
    {
        return $this->hasMany(Billet::class);
    }
}
