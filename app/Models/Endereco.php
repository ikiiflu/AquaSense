<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Endereco extends Model
{
    protected $table = 'enderecos';

    protected $fillable = ['logradouro'];

    public function sensores(): HasMany
    {
        return $this->hasMany(Sensor::class, 'endereco_id');
    }
}
