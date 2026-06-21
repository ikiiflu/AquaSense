<?php

namespace App\Http\Controllers;

use App\Models\LogConsulta;

class LogConsultaController extends Controller
{
    public function index()
    {
        $logs = LogConsulta::orderByDesc('executado_em')->limit(100)->get();

        return view('comandos.index', compact('logs'));
    }

    public function clear()
    {
        LogConsulta::truncate();

        return redirect()->route('comandos.index')
            ->with('success', 'Log de consultas limpo.');
    }
}
