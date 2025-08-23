<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnergyTariff extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'provider',
        'region',
        'tariff_type',
        'bracket1_min',
        'bracket1_max',
        'bracket1_rate',
        'bracket2_min',
        'bracket2_max',
        'bracket2_rate',
        'bracket3_min',
        'bracket3_max',
        'bracket3_rate',
        'tax_rate',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'bracket1_min' => 'float',
        'bracket1_max' => 'float',
        'bracket1_rate' => 'float',
        'bracket2_min' => 'float',
        'bracket2_max' => 'float',
        'bracket2_rate' => 'float',
        'bracket3_min' => 'float',
        'bracket3_max' => 'float',
        'bracket3_rate' => 'float',
        'tax_rate' => 'float',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    // Relação com usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
