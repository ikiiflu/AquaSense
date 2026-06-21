<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\Sensor;
use App\Models\SensorReading;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Simula inserção contínua de leituras dos sensores.
 *
 * Lote único:          php artisan sensor:simulate
 * Loop (intervalo DB): php artisan sensor:simulate --loop=auto
 * Loop a cada 30s:     php artisan sensor:simulate --loop=30
 * Histórico 24h:       php artisan sensor:simulate --backfill=24
 */
class SimulateSensorReadings extends Command
{
    protected $signature = 'sensor:simulate
                            {--loop=0     : Segundos entre lotes, ou "auto" para usar o DB}
                            {--backfill=0 : Inserir N horas de histórico em intervalos de 1 min}';

    protected $description = 'Insere leituras simuladas dos sensores no banco de dados';

    private array $estado = [];

    public function handle(): int
    {
        $loopOpt  = $this->option('loop');
        $loop     = $loopOpt === 'auto'
            ? (int) Setting::get('intervalo_leitura_seg', 60)
            : (int) $loopOpt;
        $backfill = (int) $this->option('backfill');

        $sensores = Sensor::where('ativo', true)->get();

        if ($sensores->isEmpty()) {
            $this->error('Nenhum sensor ativo encontrado. Cadastre sensores pelo Mapa Operacional.');
            return self::FAILURE;
        }

        foreach ($sensores as $sensor) {
            $ultima = $sensor->ultimaLeitura;
            $this->estado[$sensor->id] = [
                'obstrucao' => $ultima?->obstrucao_pct    ?? random_int(5, 30),
                'vazao_base' => $ultima?->vazao_lps       ?? random_int(200, 500),
            ];
        }

        if ($backfill > 0) {
            $this->backfill($sensores, $backfill);
            return self::SUCCESS;
        }

        do {
            $this->inserirLote($sensores);

            if ($loop > 0) {
                $this->line("  Próximo lote em {$loop}s… (Ctrl+C para parar)");
                sleep($loop);
            }
        } while ($loop > 0);

        return self::SUCCESS;
    }

    private function inserirLote(\Illuminate\Support\Collection $sensores): void
    {
        $agora = Carbon::now();
        $rows  = [];

        foreach ($sensores as $sensor) {
            $leitura = $this->gerarLeitura($sensor->id, $agora);
            $rows[]  = $leitura;
            $this->avaliarAlertas($sensor, $leitura);
        }

        SensorReading::insert($rows);

        $this->info("[{$agora->format('H:i:s')}] Inseridas " . count($rows) . " leituras");
    }

    private function backfill(\Illuminate\Support\Collection $sensores, int $horas): void
    {
        $inicio = Carbon::now()->subHours($horas);
        $fim    = Carbon::now();
        $cursor = $inicio->copy();
        $total  = 0;

        $this->info("Preenchendo {$horas}h de histórico a partir de {$inicio->format('Y-m-d H:i')} …");
        $barra = $this->output->createProgressBar((int) ($horas * 60));

        while ($cursor->lte($fim)) {
            $rows = [];
            foreach ($sensores as $sensor) {
                $rows[] = $this->gerarLeitura($sensor->id, $cursor->copy());
            }
            SensorReading::insert($rows);
            $total  += count($rows);
            $cursor->addMinute();
            $barra->advance();
        }

        $barra->finish();
        $this->newLine();
        $this->info("Concluído. {$total} leituras inseridas.");
    }

    private function gerarLeitura(int $sensorId, Carbon $em): array
    {
        $hora = (int) $em->format('H');

        $tempestade      = $hora >= 14 && $hora <= 18;
        $base            = $tempestade ? mt_rand(12, 20) : mt_rand(1, 4);
        $precipitacao    = round($base + (mt_rand(-100, 100) / 100), 3);

        $prev       = $this->estado[$sensorId]['obstrucao'];
        $delta      = (mt_rand(0, 100) / 100 - 0.45) * 5;
        $obstrucao  = max(0.0, min(100.0, $prev + $delta + ($tempestade ? 1.5 : 0)));
        $this->estado[$sensorId]['obstrucao'] = $obstrucao;

        $vazaoMax    = $this->estado[$sensorId]['vazao_base'];
        $fatorVazao  = max(0.02, 1.0 - ($obstrucao / 100) * 0.95);
        $vazao       = round($vazaoMax * $fatorVazao + (mt_rand(-50, 50) / 10), 3);

        return [
            'sensor_id'       => $sensorId,
            'obstrucao_pct'   => round($obstrucao, 2),
            'precipitacao_mm' => $precipitacao,
            'vazao_lps'       => max(0.0, $vazao),
            'registrado_em'   => $em->format('Y-m-d H:i:s'),
        ];
    }

    private function avaliarAlertas(Sensor $sensor, array $leitura): void
    {
        $obs = $leitura['obstrucao_pct'];

        $lCritico = (float) Setting::get('limite_critico', 70);
        $lRisco   = (float) Setting::get('limite_risco',   40);
        $lAtencao = (float) Setting::get('limite_atencao', 10);

        $severidade = match (true) {
            $obs >= $lCritico => 'critico',
            $obs >= $lRisco   => 'risco',
            $obs >= $lAtencao => 'atencao',
            default           => null,
        };

        if ($severidade === null) {
            Alert::where('sensor_id', $sensor->id)
                 ->whereNull('resolvido_em')
                 ->update(['resolvido_em' => now()]);
            return;
        }

        $mensagens = [
            'critico' => "Obstrução de {$obs}%. Risco iminente de transbordamento. Intervenção urgente necessária.",
            'risco'   => "Obstrução de {$obs}%. Inspeção recomendada em até 2 horas.",
            'atencao' => "Obstrução de {$obs}%. Monitoramento contínuo recomendado.",
        ];

        Alert::where('sensor_id', $sensor->id)
             ->whereNull('resolvido_em')
             ->where('severidade', '!=', $severidade)
             ->update(['resolvido_em' => now()]);

        $existe = Alert::where('sensor_id', $sensor->id)
                       ->where('severidade', $severidade)
                       ->whereNull('resolvido_em')
                       ->exists();

        if (! $existe) {
            Alert::create([
                'sensor_id'  => $sensor->id,
                'severidade' => $severidade,
                'mensagem'   => $mensagens[$severidade],
            ]);
        }
    }
}
