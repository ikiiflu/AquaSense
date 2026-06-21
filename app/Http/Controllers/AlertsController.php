<?php

namespace App\Http\Controllers;

use App\Models\Alert;

class AlertsController extends Controller
{
    public function index()
    {
        $activeAlerts = Alert::with('sensor:id,codigo,nome')
            ->whereNull('resolvido_em')
            ->orderByDesc('created_at')
            ->get();

        $resolvedAlerts = Alert::with('sensor:id,codigo,nome')
            ->whereNotNull('resolvido_em')
            ->orderByDesc('resolvido_em')
            ->limit(50)
            ->get();

        return view('alerts.index', compact('activeAlerts', 'resolvedAlerts'));
    }
}
