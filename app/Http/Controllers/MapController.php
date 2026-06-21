<?php

namespace App\Http\Controllers;

use App\Models\Bairro;
use App\Models\Sensor;

class MapController extends Controller
{
    public function index()
    {
        $sensors = Sensor::with(['ultimaLeitura', 'bairro'])
            ->where('ativo', true)
            ->orderBy('codigo')
            ->get();

        $bairros     = Bairro::orderBy('nome')->get();
        $bairrosJson = $bairros->toJson();

        $sensorsJson = $sensors->map(function ($s) {
            return [
                'id'        => $s->id,
                'codigo'    => $s->codigo,
                'nome'      => $s->nome,
                'endereco'  => $s->endereco,
                'bairro_id' => $s->bairro_id,
                'bairro'    => $s->bairro?->nome,
                'lat'     => (float) $s->latitude,
                'lng'     => (float) $s->longitude,
                'status'  => $s->status,
                'leitura' => $s->ultimaLeitura ? [
                    'obstrucao_pct'   => (float) $s->ultimaLeitura->obstrucao_pct,
                    'precipitacao_mm' => (float) $s->ultimaLeitura->precipitacao_mm,
                    'vazao_lps'       => (float) $s->ultimaLeitura->vazao_lps,
                ] : null,
            ];
        })->toJson();

        return view('map.operational_map', compact('sensors', 'sensorsJson', 'bairros', 'bairrosJson'));
    }
}
