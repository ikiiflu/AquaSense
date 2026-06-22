<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Sensor;

class DashboardController extends Controller
{
    public function index()
    {
        $sensors = Sensor::with('ultimaLeitura')
            ->where('ativo', true)
            ->orderBy('id')
            ->get();

        $activeAlerts = Alert::with('sensor:id,codigo,nome')
            ->whereNull('resolvido_em')
            ->orderByDesc('created_at')
            ->get();

        $readings = $sensors->map(fn ($s) => $s->ultimaLeitura)->filter();

        $metrics = [
            'avg_obstruction' => $readings->isEmpty() ? null : round($readings->avg('obstrucao_pct'), 1),
            'avg_rainfall'    => $readings->isEmpty() ? null : round($readings->avg('precipitacao_mm'), 1),
            'avg_flow'        => $readings->isEmpty() ? null : round($readings->avg('vazao_lps'), 1),
            'sensors_count'   => $sensors->count(),
            'alerts_count'    => $activeAlerts->count(),
        ];

        return view('dashboard.index', compact('sensors', 'activeAlerts', 'metrics'));
    }
}
