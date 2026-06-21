<?php

namespace App\Http\Controllers;

use App\Models\Sensor;

class ChartsController extends Controller
{
    public function index()
    {
        $sensors = Sensor::with('ultimaLeitura')
            ->where('ativo', true)
            ->orderBy('codigo')
            ->get();

        return view('charts.index', compact('sensors'));
    }
}
