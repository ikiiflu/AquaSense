<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Models\Setting;
use App\Services\SimulationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReadingGeneratorController extends Controller
{
    public function gerar(Request $request, SimulationService $sim): JsonResponse
    {
        $force        = $request->boolean('force', false);
        $intervaloSeg = (int) Setting::get('intervalo_leitura_seg', 60);
        $sensores     = Sensor::with('ultimaLeitura')->where('ativo', true)->get();
        $inseridas    = 0;

        foreach ($sensores as $sensor) {
            if ($force) {
                $sim->gerarForcar($sensor);
                $inseridas++;
            } elseif ($sim->gerarSeNecessario($sensor, $intervaloSeg)) {
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
