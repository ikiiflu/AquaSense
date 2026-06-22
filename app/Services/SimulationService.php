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

    private function modoSimulacao(): string
    {
        try {
            return Setting::get('modo_simulacao', 'normal');
        } catch (\Throwable) {
            return 'normal';
        }
    }

    public function gerar(Sensor $sensor, ?Carbon $em = null): array
    {
        $em   = $em ?? now();
        $hora = (int) $em->format('H');
        $modo = $this->modoSimulacao();

        // Determina intensidade da chuva pelo modo
        $tempestade = match ($modo) {
            'tempestade'  => true,
            'chuva_forte' => false,
            'chuva_fraca' => false,
            default       => ($hora >= 14 && $hora <= 18), // normal: simula período de chuva à tarde
        };

        $chuvaForte = match ($modo) {
            'tempestade'  => true,
            'chuva_forte' => true,
            default       => false,
        };

        $semChuva = ($modo === 'sem_chuva');

        // Obstrução: random walk sobre a última leitura
        $ultima        = $sensor->ultimaLeitura;
        $obstrucaoPrev = $ultima?->obstrucao_pct ?? mt_rand(5, 30);

        // Obstrução: caminhada aleatória — sobe mais em modo chuva
        $tendencia = match (true) {
            $tempestade  => 2.5,
            $chuvaForte  => 1.5,
            $semChuva    => -0.8,
            default      => 0.0,
        };
        $delta     = (mt_rand(0, 100) / 100 - 0.45) * 5 + $tendencia;
        $obstrucao = max(0.0, min(100.0, (float) $obstrucaoPrev + $delta));

        // Precipitação conforme modo
        $precipitacao = match (true) {
            $semChuva    => round(mt_rand(0, 30) / 100, 3),
            $tempestade  => round(mt_rand(15, 25) + (mt_rand(-100, 100) / 100), 3),
            $chuvaForte  => round(mt_rand(8, 16) + (mt_rand(-100, 100) / 100), 3),
            default      => round(mt_rand(1, 4) + (mt_rand(-100, 100) / 100), 3),
        };

        // Vazão: a chuva empurra água pelo bueiro; a obstrução limita o que passa.
        // Sem chuva há fluxo residual mínimo (~10 L/s). Com 25 mm a entrada
        // atinge a capacidade nominal (300 L/s). A obstrução reduz o teto máximo.
        $fluxoEntrada      = 10.0 + $precipitacao * 11.6; // 0 mm→10, ~25 mm→300
        $fatorObstrucao    = max(0.02, 1.0 - ($obstrucao / 100) * 0.98);
        $capacidadeReal    = 300.0 * $fatorObstrucao;
        $vazao             = round(
            min($fluxoEntrada, $capacidadeReal) + (mt_rand(-15, 15) / 10),
            3
        );

        return [
            'sensor_id'       => $sensor->id,
            'obstrucao_pct'   => round($obstrucao, 2),
            'precipitacao_mm' => max(0.0, $precipitacao),
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

        // Busca todos os alertas ativos do sensor (pode haver duplicatas antigas)
        $ativos = Alert::where('sensor_id', $sensor->id)
            ->whereNull('resolvido_em')
            ->orderByDesc('created_at')
            ->get();

        if ($severidade === null) {
            // Sensor voltou ao normal: resolve todos os alertas ativos
            $ativos->each(fn ($a) => $a->update(['resolvido_em' => now()]));
            return;
        }

        $mensagem = "Obstrução de {$obstrucao}%. " . match ($severidade) {
            'critico' => 'Risco iminente de transbordamento. Intervenção urgente necessária.',
            'risco'   => 'Inspeção recomendada em até 2 horas.',
            default   => 'Monitoramento contínuo recomendado.',
        };

        if ($ativos->isNotEmpty()) {
            $principal = $ativos->first();

            // Atualiza severidade/mensagem se houve mudança de nível
            if ($principal->severidade !== $severidade) {
                $principal->update(['severidade' => $severidade, 'mensagem' => $mensagem]);
            }

            // Resolve eventuais duplicatas geradas pelo comportamento anterior
            if ($ativos->count() > 1) {
                Alert::whereIn('id', $ativos->slice(1)->pluck('id'))
                    ->update(['resolvido_em' => now()]);
            }
        } else {
            Alert::create([
                'sensor_id'  => $sensor->id,
                'severidade' => $severidade,
                'mensagem'   => $mensagem,
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

    /** Gera e persiste incondicionalmente (ignora intervalo configurado). */
    public function gerarForcar(Sensor $sensor): void
    {
        $dados = $this->gerar($sensor);
        SensorReading::create($dados);
        $this->avaliarAlertas($sensor, $dados['obstrucao_pct']);
    }
}
