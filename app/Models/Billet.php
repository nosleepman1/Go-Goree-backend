<?php

namespace App\Models;

use App\Enums\StatutBilletEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Billet extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'qr_token',
        'montant',
        'statut',
        'voyage_id',
        'tarif_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'statut' => StatutBilletEnum::class,
            'montant' => 'decimal:2',
        ];
    }

    public function voyage()
    {
        return $this->belongsTo(Voyage::class);
    }

    public function tarif()
    {
        return $this->belongsTo(Tarif::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scans()
    {
        return $this->hasMany(Scan::class);
    }

    public function payements()
    {
        return $this->hasMany(Payement::class);
    }
}
