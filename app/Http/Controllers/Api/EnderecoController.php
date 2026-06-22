<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Endereco;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnderecoController extends Controller
{
    public function index(): JsonResponse
    {
        $enderecos = Endereco::orderBy('logradouro')->get()->map(fn ($e) => $this->format($e));
        return response()->json(['data' => $enderecos]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'logradouro' => 'required|string|max:150',
        ]);

        $endereco = Endereco::create($validated);
        return response()->json(['data' => $this->format($endereco)], 201);
    }

    public function update(Request $request, Endereco $endereco): JsonResponse
    {
        $validated = $request->validate([
            'logradouro' => 'required|string|max:150',
        ]);

        $endereco->update($validated);
        return response()->json(['data' => $this->format($endereco)]);
    }

    public function destroy(Endereco $endereco): JsonResponse
    {
        $endereco->delete();
        return response()->json(null, 204);
    }

    private function format(Endereco $e): array
    {
        return [
            'id'         => $e->id,
            'logradouro' => $e->logradouro,
        ];
    }
}
