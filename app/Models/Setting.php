<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'configuracoes';

    protected $fillable = ['chave', 'valor', 'rotulo'];

    public static function get(string $chave, mixed $default = null): mixed
    {
        return static::where('chave', $chave)->value('valor') ?? $default;
    }

    public static function set(string $chave, mixed $valor): void
    {
        static::updateOrCreate(['chave' => $chave], ['valor' => $valor]);
    }
}
