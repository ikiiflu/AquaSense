@extends('layout.body')

@section('title', 'AquaSense — Gráficos')

@section('content')
<div class="dash-header">
    <div>
        <h1 class="dash-header-title">Gráficos analíticos</h1>
        <div class="dash-header-meta">
            <span>Dados em tempo real · últimas leituras</span>
        </div>
    </div>
</div>

{{-- Obstrução por sensor (barras) --}}
<section style="padding:0 1.5rem 2rem">
    <h2 style="font-size:0.9rem;font-weight:600;margin-bottom:1rem;color:var(--ink-dim)">
        Obstrução atual por sensor
    </h2>

    @if($sensors->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">○</div>
            <div class="empty-state-title">Sem dados</div>
            <div class="empty-state-desc">Cadastre sensores e inicie a simulação de leituras.</div>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:0.6rem">
            @foreach($sensors as $s)
                @php
                    $obs   = $s->ultimaLeitura?->obstrucao_pct ?? 0;
                    $st    = $obs >= 70 ? 'critico' : ($obs >= 40 ? 'risco' : ($obs >= 10 ? 'atencao' : 'ok'));
                    $width = max(2, (int) $obs);
                @endphp
                <div style="display:flex;align-items:center;gap:0.75rem">
                    <span style="font-size:0.75rem;font-family:var(--font-mono);color:var(--ink-dim);width:5rem;flex-shrink:0">
                        {{ $s->codigo }}
                    </span>
                    <div style="flex:1;background:var(--panel);border-radius:4px;height:18px;overflow:hidden">
                        <div style="width:{{ $width }}%;height:100%;background:var(--status-{{ $st }});
                                    transition:width 0.4s ease;border-radius:4px"></div>
                    </div>
                    <span style="font-size:0.75rem;font-family:var(--font-mono);color:var(--status-{{ $st }});
                                 width:3.5rem;text-align:right">
                        {{ number_format($obs, 1) }}%
                    </span>
                </div>
            @endforeach
        </div>
    @endif
</section>

{{-- Vazão por sensor (barras) --}}
@if($sensors->isNotEmpty())
<section style="padding:0 1.5rem 2rem">
    <h2 style="font-size:0.9rem;font-weight:600;margin-bottom:1rem;color:var(--ink-dim)">
        Vazão atual por sensor (L/s)
    </h2>

    @php $maxFlow = $sensors->map(fn($s) => $s->ultimaLeitura?->vazao_lps ?? 0)->max() ?: 1; @endphp
    <div style="display:flex;flex-direction:column;gap:0.6rem">
        @foreach($sensors as $s)
            @php
                $flow = $s->ultimaLeitura?->vazao_lps ?? 0;
                $w    = max(2, (int) (($flow / $maxFlow) * 100));
            @endphp
            <div style="display:flex;align-items:center;gap:0.75rem">
                <span style="font-size:0.75rem;font-family:var(--font-mono);color:var(--ink-dim);width:5rem;flex-shrink:0">
                    {{ $s->codigo }}
                </span>
                <div style="flex:1;background:var(--panel);border-radius:4px;height:18px;overflow:hidden">
                    <div style="width:{{ $w }}%;height:100%;background:var(--flow);
                                opacity:0.8;transition:width 0.4s ease;border-radius:4px"></div>
                </div>
                <span style="font-size:0.75rem;font-family:var(--font-mono);color:var(--ink-dim);
                             width:4rem;text-align:right">
                    {{ number_format($flow, 1) }}
                </span>
            </div>
        @endforeach
    </div>
</section>

{{-- Resumo por bairro --}}
<section style="padding:0 1.5rem 2rem">
    <h2 style="font-size:0.9rem;font-weight:600;margin-bottom:1rem;color:var(--ink-dim)">
        Resumo por bairro
    </h2>

    @php
        $porBairro = $sensors->groupBy(fn($s) => $s->bairro?->nome ?? $s->bairro_nome ?? '—')->map(function($group) {
            $readings = $group->map(fn($s) => $s->ultimaLeitura)->filter();
            return [
                'count'           => $group->count(),
                'avg_obstruction' => $readings->isEmpty() ? null : round($readings->avg('obstrucao_pct'), 1),
                'avg_rainfall'    => $readings->isEmpty() ? null : round($readings->avg('precipitacao_mm'), 2),
                'avg_flow'        => $readings->isEmpty() ? null : round($readings->avg('vazao_lps'), 1),
            ];
        })->sortKeys();
    @endphp

    <table style="width:100%;border-collapse:collapse;font-size:0.82rem">
        <thead>
            <tr style="text-align:left;border-bottom:1px solid var(--line);color:var(--ink-dim)">
                <th style="padding:0.5rem 0.75rem">Bairro</th>
                <th style="padding:0.5rem 0.75rem">Sensores</th>
                <th style="padding:0.5rem 0.75rem">Obst. média (%)</th>
                <th style="padding:0.5rem 0.75rem">Precipitação (mm)</th>
                <th style="padding:0.5rem 0.75rem">Vazão (L/s)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($porBairro as $bairro => $data)
                <tr style="border-bottom:1px solid color-mix(in srgb,var(--line) 50%,transparent)">
                    <td style="padding:0.5rem 0.75rem;font-weight:600">{{ $bairro }}</td>
                    <td style="padding:0.5rem 0.75rem;color:var(--ink-dim)">{{ $data['count'] }}</td>
                    <td style="padding:0.5rem 0.75rem;font-family:var(--font-mono)">{{ $data['avg_obstruction'] ?? '—' }}</td>
                    <td style="padding:0.5rem 0.75rem;font-family:var(--font-mono)">{{ $data['avg_rainfall'] ?? '—' }}</td>
                    <td style="padding:0.5rem 0.75rem;font-family:var(--font-mono)">{{ $data['avg_flow'] ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</section>
@endif
@stop
