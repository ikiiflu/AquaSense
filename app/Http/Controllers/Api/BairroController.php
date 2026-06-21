<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bairro;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BairroController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Bairro::orderBy('nome')->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['nome' => 'required|string|max:100|unique:bairros,nome']);
        $b    = Bairro::create($data);

        return response()->json(['data' => $b], 201);
    }

    public function update(Request $request, Bairro $bairro): JsonResponse
    {
        $data = $request->validate([
            'nome' => 'required|string|max:100|unique:bairros,nome,' . $bairro->id,
        ]);
        $bairro->update($data);

        return response()->json(['data' => $bairro]);
    }

    public function destroy(Bairro $bairro): JsonResponse
    {
        $bairro->delete();
        return response()->json(null, 204);
    }
}
