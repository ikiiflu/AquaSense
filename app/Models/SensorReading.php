<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorReading extends Model
{
    protected $table = 'leituras';

    public $timestamps = false;

    protected $fillable = [
        'sensor_id', 'obstrucao_pct', 'precipitacao_mm', 'vazao_lps', 'registrado_em',
    ];

    protected $casts = [
        'obstrucao_pct'   => 'float',
        'precipitacao_mm' => 'float',
        'vazao_lps'       => 'float',
        'registrado_em'   => 'datetime',
    ];

    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class);
    }
}
