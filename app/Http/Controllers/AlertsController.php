<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Bairro;

class AlertsController extends Controller
{
    public function index()
    {
        $activeAlerts = Alert::with(['sensor:id,codigo,nome,bairro_id', 'sensor.bairro:id,nome'])
            ->whereNull('resolvido_em')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn ($a) => $a->sensor?->bairro?->nome ?? 'Sem bairro');

        $resolvedAlerts = Alert::with(['sensor:id,codigo,nome'])
            ->whereNotNull('resolvido_em')
            ->orderByDesc('resolvido_em')
            ->limit(50)
            ->get();

        $totalActive = Alert::whereNull('resolvido_em')->count();

        return view('alerts.index', compact('activeAlerts', 'resolvedAlerts', 'totalActive'));
    }

    public function resolve(Alert $alert)
    {
        $alert->update(['resolvido_em' => now()]);
        return redirect()->route('alerts.index')->with('success', 'Alerta marcado como resolvido.');
    }

    public function destroy(Alert $alert)
    {
        $alert->delete();
        return redirect()->route('alerts.index')->with('success', 'Alerta excluido.');
    }

    public function clearResolved()
    {
        $count = Alert::whereNotNull('resolvido_em')->count();
        Alert::whereNotNull('resolvido_em')->delete();
        return redirect()->route('alerts.index')
            ->with('success', "{$count} alerta(s) resolvido(s) removido(s).");
    }
}
