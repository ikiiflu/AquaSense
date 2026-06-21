<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sensor extends Model
{
    protected $table = 'sensores';

    protected $fillable = [
        'codigo', 'nome', 'endereco', 'bairro_id', 'latitude', 'longitude', 'ativo',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
        'ativo'     => 'boolean',
    ];

    public function bairro(): BelongsTo
    {
        return $this->belongsTo(Bairro::class, 'bairro_id');
    }

    public function getBairroNomeAttribute(): string
    {
        return $this->bairro?->nome ?? '—';
    }

    public function leituras(): HasMany
    {
        return $this->hasMany(SensorReading::class)->orderByDesc('registrado_em');
    }

    /** @deprecated use leituras() */
    public function readings(): HasMany
    {
        return $this->leituras();
    }

    public function ultimaLeitura(): HasOne
    {
        return $this->hasOne(SensorReading::class)->latestOfMany('registrado_em');
    }

    /** @deprecated use ultimaLeitura() */
    public function latestReading(): HasOne
    {
        return $this->ultimaLeitura();
    }

    public function alertas(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function alertasAtivos(): HasMany
    {
        return $this->hasMany(Alert::class)->whereNull('resolvido_em');
    }

    public function manutencoes(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    private static ?array $limiares = null;

    private static function limiares(): array
    {
        if (static::$limiares !== null) {
            return static::$limiares;
        }
        try {
            static::$limiares = [
                'critico' => (float) Setting::get('limite_critico', 70),
                'risco'   => (float) Setting::get('limite_risco',   40),
                'atencao' => (float) Setting::get('limite_atencao', 10),
            ];
        } catch (\Throwable) {
            static::$limiares = ['critico' => 70.0, 'risco' => 40.0, 'atencao' => 10.0];
        }
        return static::$limiares;
    }

    public function getStatusAttribute(): string
    {
        $obs = $this->ultimaLeitura?->obstrucao_pct ?? 0;
        $t   = static::limiares();

        return match (true) {
            $obs >= $t['critico'] => 'critico',
            $obs >= $t['risco']   => 'risco',
            $obs >= $t['atencao'] => 'atencao',
            default               => 'ok',
        };
    }
}
