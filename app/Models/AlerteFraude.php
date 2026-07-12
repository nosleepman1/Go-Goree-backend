<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlerteFraude extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'payement_id',
        'niveau',
        'regle_declenchee',
        'payload_suspect',
        'traite_par',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'niveau' => \App\Enums\NiveauAlerteFraudeEnum::class,
            'statut' => \App\Enums\StatutAlerteFraudeEnum::class,
            'payload_suspect' => 'array',
        ];
    }

    public function payement()
    {
        return $this->belongsTo(Payement::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'traite_par');
    }

    
}
