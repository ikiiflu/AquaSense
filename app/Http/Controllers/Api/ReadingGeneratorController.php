<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Models\Setting;
use App\Services\SimulationService;
use Illuminate\Http\JsonResponse;

class ReadingGeneratorController extends Controller
{
    public function gerar(SimulationService $sim): JsonResponse
    {
        $intervaloSeg = (int) Setting::get('intervalo_leitura_seg', 60);

        $sensores = Sensor::with('ultimaLeitura')->where('ativo', true)->get();

        $inseridas = 0;
        foreach ($sensores as $sensor) {
            if ($sim->gerarSeNecessario($sensor, $intervaloSeg)) {
                $inseridas++;
            }
        }

        return response()->json([
            'inseridas' => $inseridas,
            'sensores'  => $sensores->count(),
            'intervalo' => $intervaloSeg,
        ]);
    }
}
