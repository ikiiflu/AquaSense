<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Models\SensorReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function summary(): JsonResponse
    {
        $latest = SensorReading::select('sensor_id', DB::raw('MAX(registrado_em) as max_ts'))
            ->groupBy('sensor_id');

        $rows = DB::table('leituras as r')
            ->joinSub($latest, 'l', function ($join) {
                $join->on('r.sensor_id', '=', 'l.sensor_id')
                     ->on('r.registrado_em', '=', 'l.max_ts');
            })
            ->select([
                DB::raw('ROUND(AVG(r.obstrucao_pct), 2)    as avg_obstruction'),
                DB::raw('ROUND(AVG(r.precipitacao_mm), 3)  as avg_rainfall'),
                DB::raw('ROUND(AVG(r.vazao_lps), 3)        as avg_flow'),
                DB::raw('ROUND(MAX(r.obstrucao_pct), 2)    as max_obstruction'),
                DB::raw('ROUND(MAX(r.precipitacao_mm), 3)  as max_rainfall'),
                DB::raw('COUNT(*) as sensors_reporting'),
            ])
            ->first();

        return response()->json(['data' => $rows]);
    }

    public function byRegion(): JsonResponse
    {
        $latest = SensorReading::select('sensor_id', DB::raw('MAX(registrado_em) as max_ts'))
            ->groupBy('sensor_id');

        $rows = DB::table('leituras as r')
            ->joinSub($latest, 'l', function ($join) {
                $join->on('r.sensor_id', '=', 'l.sensor_id')
                     ->on('r.registrado_em', '=', 'l.max_ts');
            })
            ->join('sensores as s', 'r.sensor_id', '=', 's.id')
            ->select([
                's.bairro',
                DB::raw('ROUND(AVG(r.obstrucao_pct), 2)   as avg_obstruction'),
                DB::raw('ROUND(AVG(r.precipitacao_mm), 3) as avg_rainfall'),
                DB::raw('ROUND(AVG(r.vazao_lps), 3)       as avg_flow'),
                DB::raw('COUNT(*) as sensor_count'),
            ])
            ->groupBy('s.bairro')
            ->orderBy('s.bairro')
            ->get();

        return response()->json(['data' => $rows]);
    }

    public function timeseries(Sensor $sensor, \Illuminate\Http\Request $request): JsonResponse
    {
        $hours = min((int) $request->query('hours', 6), 72);

        $rows = SensorReading::where('sensor_id', $sensor->id)
            ->where('registrado_em', '>=', now()->subHours($hours))
            ->orderBy('registrado_em')
            ->get(['registrado_em', 'obstrucao_pct', 'precipitacao_mm', 'vazao_lps']);

        return response()->json([
            'sensor' => ['id' => $sensor->id, 'codigo' => $sensor->codigo, 'nome' => $sensor->nome],
            'hours'  => $hours,
            'data'   => $rows,
        ]);
    }
}
