<?php

namespace App\Console\Commands;

use App\Models\Sensor;
use App\Models\Setting;
use App\Services\SimulationService;
use Illuminate\Console\Command;

/**
 * Gera leituras simuladas para todos os sensores ativos.
 *
 * Uso via cron (scheduler, a cada minuto):
 *   php artisan schedule:run
 *
 * Uso manual:
 *   php artisan sensor:simulate               → um lote respeitando intervalo
 *   php artisan sensor:simulate --force       → força geração ignorando intervalo
 *   php artisan sensor:simulate --backfill=24 → 24h de histórico (1 lote/min)
 */
class SimulateSensorReadings extends Command
{
    protected $signature = 'sensor:simulate
                            {--force    : Gera independente do intervalo configurado}
                            {--backfill=0 : Inserir N horas de histórico retroativo}';

    protected $description = 'Gera leituras simuladas dos sensores ativos';

    public function handle(SimulationService $sim): int
    {
        $sensores = Sensor::with('ultimaLeitura')->where('ativo', true)->get();

        if ($sensores->isEmpty()) {
            $this->warn('Nenhum sensor ativo encontrado.');
            return self::FAILURE;
        }

        // Modo backfill: gera histórico retroativo
        $backfill = (int) $this->option('backfill');
        if ($backfill > 0) {
            return $this->runBackfill($sim, $sensores, $backfill);
        }

        // Modo normal: respeita intervalo configurado (ou força se --force)
        $force     = $this->option('force');
        $intervalo = (int) Setting::get('intervalo_leitura_seg', 60);
        $inseridas = 0;

        foreach ($sensores as $sensor) {
            if ($force) {
                $sim->gerarForcar($sensor);
                $inseridas++;
            } elseif ($sim->gerarSeNecessario($sensor, $intervalo)) {
                $inseridas++;
            }
        }

        $this->line('[' . now()->format('H:i:s') . "] {$inseridas}/{$sensores->count()} leituras inseridas");
        return self::SUCCESS;
    }

    private function runBackfill(SimulationService $sim, $sensores, int $horas): int
    {
        $inicio = now()->subHours($horas);
        $cursor = $inicio->copy();
        $fim    = now();
        $total  = 0;

        $this->info("Preenchendo {$horas}h de histórico a partir de {$inicio->format('Y-m-d H:i')}…");
        $barra = $this->output->createProgressBar((int) ($horas * 60));

        while ($cursor->lte($fim)) {
            foreach ($sensores as $sensor) {
                $dados = $sim->gerar($sensor, $cursor->copy());
                \App\Models\SensorReading::create($dados);
                $sim->avaliarAlertas($sensor, $dados['obstrucao_pct']);
                $total++;
            }
            $cursor->addMinute();
            $barra->advance();
        }

        $barra->finish();
        $this->newLine();
        $this->info("Concluído. {$total} leituras inseridas.");
        return self::SUCCESS;
    }
}
