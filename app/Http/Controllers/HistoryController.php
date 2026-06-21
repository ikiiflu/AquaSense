<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Models\SensorReading;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $sensors  = Sensor::where('ativo', true)->orderBy('codigo')->get(['id', 'codigo', 'nome']);
        $selected = $request->integer('sensor_id') ?: ($sensors->first()?->id);

        $readings = SensorReading::where('sensor_id', $selected)
            ->orderByDesc('registrado_em')
            ->limit(200)
            ->get();

        return view('history.index', compact('sensors', 'selected', 'readings'));
    }
}
