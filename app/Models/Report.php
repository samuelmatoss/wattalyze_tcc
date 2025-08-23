<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'period_type',
        'period_start',
        'period_end',
        'filters',
        'data',
        'format',
        'file_path',
        'is_scheduled',
        'schedule_frequency',
        'next_generation',
        'status'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'filters' => 'array',
        'data' => 'array',
        'is_scheduled' => 'boolean',
        'next_generation' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}