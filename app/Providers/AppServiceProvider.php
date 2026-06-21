<?php

namespace App\Providers;

use App\Models\Alert;
use App\Models\Sensor;
use App\Models\SensorReading;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private static bool $loggingQuery = false;

    public function register(): void {}

    public function boot(): void
    {
        // ── View composers ──────────────────────────────────────────────────────
        View::composer('layout.sidebar', function ($view) {
            $sensors    = Sensor::with(['ultimaLeitura', 'bairro'])->where('ativo', true)->orderBy('codigo')->get();
            $alertCount = Alert::whereNull('resolvido_em')->count();

            $view->with('navSensors', $sensors)
                 ->with('navAlertCount', $alertCount);
        });

        View::composer('layout.footer', function ($view) {
            $ativos   = Sensor::where('ativo', true)->count();
            $lastSync = SensorReading::max('registrado_em');

            $view->with('footerActiveSensors', $ativos)
                 ->with('footerTotalSensors',  $ativos)
                 ->with('footerLastSync',       $lastSync);
        });

        // ── SQL query logger (INSERT / UPDATE / DELETE apenas) ──────────────────
        DB::listen(function ($query) {
            if (self::$loggingQuery) return;

            $sql = ltrim($query->sql);
            $op  = strtoupper(substr($sql, 0, 6));
            if (!in_array($op, ['INSERT', 'UPDATE', 'DELETE'], true)) return;
            if (stripos($sql, 'log_consultas') !== false) return;

            self::$loggingQuery = true;
            try {
                DB::table('log_consultas')->insert([
                    'sql_query'    => $query->sql,
                    'bindings'     => json_encode($query->bindings),
                    'tempo_ms'     => $query->time,
                    'executado_em' => now()->format('Y-m-d H:i:s'),
                ]);

                // Mantém apenas os últimos 200 registros
                $max = DB::table('log_consultas')->max('id');
                if ($max > 200) {
                    DB::table('log_consultas')
                        ->where('id', '<=', $max - 200)
                        ->delete();
                }
            } catch (\Throwable) {
                // Tabela ainda não existe (durante migrate)
            } finally {
                self::$loggingQuery = false;
            }
        });
    }
}
