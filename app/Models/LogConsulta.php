<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogConsulta extends Model
{
    protected $table = 'log_consultas';

    public $timestamps = false;

    protected $fillable = ['sql_query', 'bindings', 'tempo_ms', 'executado_em'];

    protected $casts = [
        'bindings'     => 'array',
        'executado_em' => 'datetime',
    ];
}
