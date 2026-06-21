<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Sensor;
use App\Models\SensorReading;
use App\Models\Setting;
use Illuminate\Support\Carbon;

class SimulationService
{
    private static ?array $limiares = null;

    private function limiares(): array
    {
        if (static::$limiares !== null) return static::$limiares;
        try {
            static::$limiares = [
                'critico' => (float) Setting::get('limite_critico', 70),
                'risco'   => (float) Setting::get('limite_risco',   40),
                'atencao' => (float) Setting::get('limite_atencao', 10),
            ];
        } catch (\Throwable) {
            static::$limiares = ['critico' => 70.0, 'risco' => 40.0, 'atencao' => 10.0];
        }
        return static::$limiares;
    }

    public function gerar(Sensor $sensor, ?Carbon $em = null): array
    {
        $em   = $em ?? now();
        $hora = (int) $em->format('H');

        $tempestade = $hora >= 14 && $hora <= 18;

        // Estado baseado na última leitura do sensor (random walk contínuo)
        $ultima         = $sensor->ultimaLeitura;
        $obstrucaoPrev  = $ultima?->obstrucao_pct ?? mt_rand(5, 30);
        $vazaoBase      = max(50.0, $ultima?->vazao_lps ?? mt_rand(200, 500));

        // Obstrução: caminhada aleatória
        $delta     = (mt_rand(0, 100) / 100 - 0.45) * 5;
        $obstrucao = max(0.0, min(100.0, (float) $obstrucaoPrev + $delta + ($tempestade ? 1.5 : 0.0)));

        // Precipitação
        $baseRain     = $tempestade ? mt_rand(12, 20) : mt_rand(1, 4);
        $precipitacao = round($baseRain + (mt_rand(-100, 100) / 100), 3);

        // Vazão (inversamente proporcional à obstrução)
        $fatorVazao = max(0.02, 1.0 - ($obstrucao / 100) * 0.95);
        $vazao      = round($vazaoBase * $fatorVazao + (mt_rand(-50, 50) / 10), 3);

        return [
            'sensor_id'       => $sensor->id,
            'obstrucao_pct'   => round($obstrucao, 2),
            'precipitacao_mm' => $precipitacao,
            'vazao_lps'       => max(0.0, $vazao),
            'registrado_em'   => $em->format('Y-m-d H:i:s'),
        ];
    }

    public function avaliarAlertas(Sensor $sensor, float $obstrucao): void
    {
        $t = $this->limiares();

        $severidade = match (true) {
            $obstrucao >= $t['critico'] => 'critico',
            $obstrucao >= $t['risco']   => 'risco',
            $obstrucao >= $t['atencao'] => 'atencao',
            default                     => null,
        };

        if ($severidade === null) {
            Alert::where('sensor_id', $sensor->id)
                 ->whereNull('resolvido_em')
                 ->update(['resolvido_em' => now()]);
            return;
        }

        // Resolve alertas de outra severidade
        Alert::where('sensor_id', $sensor->id)
             ->whereNull('resolvido_em')
             ->where('severidade', '!=', $severidade)
             ->update(['resolvido_em' => now()]);

        // Abre alerta novo se não existir
        if (!Alert::where('sensor_id', $sensor->id)->where('severidade', $severidade)->whereNull('resolvido_em')->exists()) {
            Alert::create([
                'sensor_id'  => $sensor->id,
                'severidade' => $severidade,
                'mensagem'   => "Obstrução de {$obstrucao}%. " . match ($severidade) {
                    'critico' => 'Risco iminente de transbordamento. Intervenção urgente necessária.',
                    'risco'   => 'Inspeção recomendada em até 2 horas.',
                    default   => 'Monitoramento contínuo recomendado.',
                },
            ]);
        }
    }

    /**
     * Gera e persiste uma leitura somente se o intervalo configurado já passou.
     * Retorna true se inseriu, false se ainda era cedo demais.
     */
    public function gerarSeNecessario(Sensor $sensor, int $intervaloSeg): bool
    {
        $ultima = $sensor->ultimaLeitura;

        if ($ultima !== null) {
            $elapsed = Carbon::parse($ultima->registrado_em)->diffInSeconds(now());
            if ($elapsed < $intervaloSeg) {
                return false;
            }
        }

        $dados = $this->gerar($sensor);
        SensorReading::create($dados);
        $this->avaliarAlertas($sensor, $dados['obstrucao_pct']);
        return true;
    }
}
