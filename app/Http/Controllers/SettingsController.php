<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('id')->get()->keyBy('chave');

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'intervalo_leitura_seg' => 'required|integer|min:10|max:86400',
            'limite_atencao'        => 'required|integer|min:1|max:99',
            'limite_risco'          => 'required|integer|min:1|max:99',
            'limite_critico'        => 'required|integer|min:1|max:99',
        ]);

        foreach ($validated as $chave => $valor) {
            Setting::set($chave, $valor);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Configurações salvas com sucesso.');
    }

    public function clear()
    {
        try {
            Schema::disableForeignKeyConstraints();

            foreach (['leituras', 'alertas', 'manutencoes', 'log_consultas'] as $table) {
                DB::statement("TRUNCATE TABLE `{$table}`");
            }

            Schema::enableForeignKeyConstraints();

            return redirect()->route('settings.index')
                ->with('success', 'Tabelas limpas: leituras, alertas, manutenções e log SQL.');
        } catch (\Throwable $e) {
            Schema::enableForeignKeyConstraints();

            return redirect()->route('settings.index')
                ->with('error', 'Erro ao limpar: ' . $e->getMessage());
        }
    }
}
