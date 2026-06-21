<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function index(): JsonResponse
    {
        $sensors = Sensor::with(['ultimaLeitura', 'bairro'])
            ->where('ativo', true)
            ->orderBy('codigo')
            ->get()
            ->map(fn ($s) => $this->format($s));

        return response()->json(['data' => $sensors]);
    }

    public function show(Sensor $sensor): JsonResponse
    {
        $sensor->load(['ultimaLeitura', 'bairro']);
        return response()->json(['data' => $this->format($sensor)]);
    }

    public function readings(Request $request, Sensor $sensor): JsonResponse
    {
        $limit    = min((int) $request->query('limit', 50), 500);
        $readings = $sensor->leituras()->limit($limit)->get();

        return response()->json([
            'sensor' => ['id' => $sensor->id, 'codigo' => $sensor->codigo, 'nome' => $sensor->nome],
            'data'   => $readings,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo'    => 'required|string|max:20|unique:sensores,codigo',
            'nome'      => 'required|string|max:100',
            'endereco'  => 'required|string|max:200',
            'bairro_id' => 'required|integer|exists:bairros,id',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $sensor = Sensor::create(array_merge($validated, ['ativo' => true]));
        $sensor->load(['ultimaLeitura', 'bairro']);

        return response()->json(['data' => $this->format($sensor)], 201);
    }

    public function update(Request $request, Sensor $sensor): JsonResponse
    {
        $validated = $request->validate([
            'codigo'    => 'sometimes|string|max:20|unique:sensores,codigo,' . $sensor->id,
            'nome'      => 'sometimes|string|max:100',
            'endereco'  => 'sometimes|string|max:200',
            'bairro_id' => 'sometimes|integer|exists:bairros,id',
            'latitude'  => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'ativo'     => 'sometimes|boolean',
        ]);

        $sensor->update($validated);
        $sensor->load(['ultimaLeitura', 'bairro']);

        return response()->json(['data' => $this->format($sensor)]);
    }

    public function destroy(Sensor $sensor): JsonResponse
    {
        $sensor->delete();
        return response()->json(null, 204);
    }

    private function format(Sensor $sensor): array
    {
        $r = $sensor->ultimaLeitura;

        return [
            'id'        => $sensor->id,
            'codigo'    => $sensor->codigo,
            'nome'      => $sensor->nome,
            'endereco'  => $sensor->endereco,
            'bairro_id' => $sensor->bairro_id,
            'bairro'    => $sensor->bairro?->nome,
            'latitude'  => $sensor->latitude,
            'longitude' => $sensor->longitude,
            'ativo'     => $sensor->ativo,
            'status'    => $sensor->status,
            'lat'       => $sensor->latitude,
            'lng'       => $sensor->longitude,
            'reading'   => $r ? [
                'obstrucao_pct'   => $r->obstrucao_pct,
                'precipitacao_mm' => $r->precipitacao_mm,
                'vazao_lps'       => $r->vazao_lps,
                'registrado_em'   => $r->registrado_em?->toIso8601String(),
            ] : null,
        ];
    }
}
