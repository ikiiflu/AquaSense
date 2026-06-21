<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\JsonResponse;

class AlertController extends Controller
{
    public function active(): JsonResponse
    {
        $ordemSeveridade = ['critico' => 3, 'risco' => 2, 'atencao' => 1];

        $alerts = Alert::with('sensor:id,codigo,nome')
            ->whereNull('resolvido_em')
            ->orderByDesc('created_at')
            ->get()
            ->sortByDesc(fn ($a) => $ordemSeveridade[$a->severidade] ?? 0)
            ->values()
            ->map(fn ($a) => [
                'id'         => $a->id,
                'severity'   => $a->severidade,
                'severidade' => $a->severidade,
                'message'    => $a->mensagem,
                'mensagem'   => $a->mensagem,
                'created_at' => $a->created_at->toIso8601String(),
                'sensor'     => $a->sensor,
            ]);

        return response()->json(['data' => $alerts, 'count' => $alerts->count()]);
    }

    public function index(): JsonResponse
    {
        $alerts = Alert::with('sensor:id,codigo,nome')
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        return response()->json(['data' => $alerts]);
    }
}
