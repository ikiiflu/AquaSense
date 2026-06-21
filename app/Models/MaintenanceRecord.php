<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRecord extends Model
{
    protected $table = 'manutencoes';

    protected $fillable = [
        'sensor_id', 'operador', 'descricao', 'observacoes', 'realizado_em',
    ];

    protected $casts = [
        'realizado_em' => 'datetime',
    ];

    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class);
    }
}
