<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $table = 'alertas';

    protected $fillable = [
        'sensor_id', 'severidade', 'mensagem', 'resolvido_em',
    ];

    protected $casts = [
        'resolvido_em' => 'datetime',
    ];

    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class);
    }

    public function isActive(): bool
    {
        return $this->resolvido_em === null;
    }
}
