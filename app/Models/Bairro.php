<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bairro extends Model
{
    protected $table = 'bairros';

    protected $fillable = ['nome'];

    public function sensores(): HasMany
    {
        return $this->hasMany(Sensor::class, 'bairro_id');
    }
}
