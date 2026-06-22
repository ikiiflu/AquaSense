<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRecord;
use App\Models\Sensor;

class MaintenanceController extends Controller
{
    public function index()
    {
        $records = MaintenanceRecord::with('sensor:id,codigo,nome')
            ->orderByDesc('realizado_em')
            ->paginate(20);

        $sensors = Sensor::where('ativo', true)->orderBy('id')->get(['id', 'codigo', 'nome']);

        return view('maintenance.index', compact('records', 'sensors'));
    }
}
