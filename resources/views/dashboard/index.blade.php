@extends('layout.body')

@section('title', 'AquaSense - Dashboard')

@section('content')
@php
    $statusColors = ['ok'=>'#10B981','atencao'=>'#F59E0B','risco'=>'#F97316','critico'=>'#EF4444'];
    $statusGlows  = ['ok'=>'rgba(16,185,129,0.18)','atencao'=>'rgba(245,158,11,0.18)','risco'=>'rgba(249,115,22,0.18)','critico'=>'rgba(239,68,68,0.18)'];
    $statusLabels = ['ok'=>'Normal','atencao'=>'Atenção','risco'=>'Risco','critico'=>'Crítico'];
    $statusCounts = ['ok'=>0,'atencao'=>0,'risco'=>0,'critico'=>0];
    foreach ($sensors as $s) { $statusCounts[$s->status] = ($statusCounts[$s->status] ?? 0) + 1; }

    $obs     = $metrics['avg_obstruction'];
    $rain    = $metrics['avg_rainfall'];
    $flow    = $metrics['avg_flow'];

    $obsSt   = is_null($obs)  ? 'ok' : ($obs  >= 70 ? 'critico' : ($obs  >= 40 ? 'risco' : ($obs  >= 10 ? 'atencao' : 'ok')));
    $rainSt  = is_null($rain) ? 'ok' : ($rain > 10  ? 'critico' : ($rain > 4   ? 'risco' : ($rain > 0   ? 'atencao' : 'ok')));
    $flowSt  = 'ok';

    $obsLbl  = is_null($obs)  ? 'Sem dados'  : ['ok'=>'Dentro do limite','atencao'=>'Atenção','risco'=>'Risco elevado','critico'=>'Crítico'][$obsSt];
    $rainLbl = is_null($rain) ? 'Sem dados'  : ($rain > 10 ? 'Chuva intensa' : ($rain > 4 ? 'Chuva moderada' : ($rain > 0 ? 'Chuva leve' : 'Sem chuva')));
    $flowLbl = is_null($flow) ? 'Sem dados'  : 'Fluxo normal';
@endphp

{{-- ── Cabeçalho ────────────────────────────────────────────────── --}}
<div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap">
    <div>
        <h1 class="dash-header-title" style="font-size:1.5rem">Visão operacional</h1>
        <div style="display:flex;gap:1rem;margin-top:0.3rem;font-size:0.75rem;color:var(--ink-dim);font-family:var(--font-data)">
            <span>{{ $metrics['sensors_count'] }} sensor{{ $metrics['sensors_count'] !== 1 ? 'es' : '' }}</span>
            <span style="color:var(--line)">·</span>
            @if($metrics['alerts_count'] > 0)
                <span style="color:var(--status-critico)">{{ $metrics['alerts_count'] }} alerta{{ $metrics['alerts_count'] !== 1 ? 's' : '' }} ativo{{ $metrics['alerts_count'] !== 1 ? 's' : '' }}</span>
            @else
                <span style="color:var(--status-ok)">Sem alertas ativos</span>
            @endif
            <span style="color:var(--line)">·</span>
            <span>Caratinga, MG</span>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:0.4rem;font-size:0.72rem;font-family:var(--font-data);color:var(--flow);background:var(--flow-dim);padding:0.3rem 0.75rem;border-radius:99px;border:1px solid rgba(0,212,170,0.2)">
        <span style="width:6px;height:6px;border-radius:50%;background:var(--flow);display:inline-block;animation:live-dot 2s ease-in-out infinite"></span>
        AO VIVO
    </div>
</div>

{{-- ── Banda de status por sensor ──────────────────────────────────── --}}
@if($sensors->isNotEmpty())
<div style="background:var(--panel);border:1px solid var(--line);border-radius:8px;padding:0.6rem 1rem;display:flex;align-items:center;gap:0.5rem;overflow-x:auto">
    <span style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-muted);font-weight:600;white-space:nowrap;margin-right:0.25rem">Rede</span>
    @foreach($sensors as $s)
        @php
            $sCor = $statusColors[$s->status] ?? '#10B981';
            $sObs = $s->ultimaLeitura?->obstrucao_pct ?? 0;
        @endphp
        <div title="{{ $s->nome }} — {{ number_format($sObs,1) }}% obstrução" style="display:flex;flex-direction:column;align-items:center;gap:0.25rem;min-width:44px">
            <div style="width:36px;height:6px;background:var(--line);border-radius:3px;overflow:hidden">
                <div style="width:{{ max(4,(int)$sObs) }}%;height:100%;background:{{ $sCor }};border-radius:3px;transition:width 0.4s"></div>
            </div>
            <span style="font-size:0.6rem;font-family:var(--font-data);color:var(--ink-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:44px;text-align:center">{{ $s->codigo }}</span>
        </div>
    @endforeach
    <div style="margin-left:auto;display:flex;gap:0.75rem;flex-shrink:0">
        @foreach(['ok'=>'Normal','atencao'=>'Atenção','risco'=>'Risco','critico'=>'Crítico'] as $st => $lb)
            @if($statusCounts[$st] > 0)
                @php $c = $statusColors[$st]; @endphp
                <span style="font-size:0.65rem;color:{{ $c }};display:flex;align-items:center;gap:0.3rem;white-space:nowrap">
                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $c }};display:inline-block"></span>
                    {{ $statusCounts[$st] }} {{ $lb }}
                </span>
            @endif
        @endforeach
    </div>
</div>
@endif

{{-- ── Três cards de métrica ────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem">

    {{-- Obstrução --}}
    @php $c = $statusColors[$obsSt]; $g = $statusGlows[$obsSt]; @endphp
    <div style="background:var(--panel);border:1px solid var(--line);border-radius:12px;padding:1.25rem 1.5rem;position:relative;overflow:hidden;box-shadow:0 0 0 0 {{ $c }}">
        <div style="position:absolute;top:0;right:0;width:120px;height:120px;border-radius:50%;background:{{ $g }};transform:translate(30%,-30%);pointer-events:none"></div>
        <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-muted);font-weight:600;margin-bottom:0.75rem">Obstrução média</div>
        <div style="font-size:2.8rem;font-weight:700;font-family:var(--font-data);color:{{ $c }};line-height:1;margin-bottom:0.5rem" id="metric-obstruction">
            {{ is_null($obs) ? '-' : number_format($obs, 1) }}@if(!is_null($obs))<span style="font-size:1.4rem;opacity:0.7">%</span>@endif
        </div>
        <div style="height:4px;background:var(--line);border-radius:2px;margin-bottom:0.75rem;overflow:hidden">
            <div style="height:100%;width:{{ min(100, $obs ?? 0) }}%;background:{{ $c }};border-radius:2px;transition:width 0.5s ease"></div>
        </div>
        <div style="display:flex;align-items:center;gap:0.4rem;font-size:0.75rem">
            <span style="width:7px;height:7px;border-radius:50%;background:{{ $c }};display:inline-block;flex-shrink:0"></span>
            <span style="color:var(--ink-dim)">{{ $obsLbl }}</span>
        </div>
    </div>

    {{-- Precipitação --}}
    @php $c = $statusColors[$rainSt]; $g = $statusGlows[$rainSt]; @endphp
    <div style="background:var(--panel);border:1px solid var(--line);border-radius:12px;padding:1.25rem 1.5rem;position:relative;overflow:hidden">
        <div style="position:absolute;top:0;right:0;width:120px;height:120px;border-radius:50%;background:{{ $g }};transform:translate(30%,-30%);pointer-events:none"></div>
        <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-muted);font-weight:600;margin-bottom:0.75rem">Precipitação</div>
        <div style="font-size:2.8rem;font-weight:700;font-family:var(--font-data);color:{{ $c }};line-height:1;margin-bottom:0.5rem" id="metric-rainfall">
            {{ is_null($rain) ? '-' : number_format($rain, 1) }}@if(!is_null($rain))<span style="font-size:1.4rem;opacity:0.7"> mm</span>@endif
        </div>
        <div style="height:4px;background:var(--line);border-radius:2px;margin-bottom:0.75rem;overflow:hidden">
            <div style="height:100%;width:{{ min(100, is_null($rain) ? 0 : min(100, ($rain/25)*100)) }}%;background:{{ $c }};border-radius:2px;transition:width 0.5s ease"></div>
        </div>
        <div style="display:flex;align-items:center;gap:0.4rem;font-size:0.75rem">
            <span style="width:7px;height:7px;border-radius:50%;background:{{ $c }};display:inline-block;flex-shrink:0"></span>
            <span style="color:var(--ink-dim)">{{ $rainLbl }}</span>
        </div>
    </div>

    {{-- Vazão --}}
    @php $c = $statusColors[$flowSt]; $g = $statusGlows[$flowSt]; @endphp
    <div style="background:var(--panel);border:1px solid var(--line);border-radius:12px;padding:1.25rem 1.5rem;position:relative;overflow:hidden">
        <div style="position:absolute;top:0;right:0;width:120px;height:120px;border-radius:50%;background:{{ $g }};transform:translate(30%,-30%);pointer-events:none"></div>
        <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-muted);font-weight:600;margin-bottom:0.75rem">Vazão média</div>
        <div style="font-size:2.8rem;font-weight:700;font-family:var(--font-data);color:{{ $c }};line-height:1;margin-bottom:0.5rem" id="metric-flow">
            {{ is_null($flow) ? '-' : number_format($flow, 1) }}@if(!is_null($flow))<span style="font-size:1.4rem;opacity:0.7"> L/s</span>@endif
        </div>
        <div style="height:4px;background:var(--line);border-radius:2px;margin-bottom:0.75rem;overflow:hidden">
            <div style="height:100%;width:{{ is_null($flow) ? 0 : min(100, ($flow/300)*100) }}%;background:{{ $c }};border-radius:2px;transition:width 0.5s ease"></div>
        </div>
        <div style="display:flex;align-items:center;gap:0.4rem;font-size:0.75rem">
            <span style="width:7px;height:7px;border-radius:50%;background:{{ $c }};display:inline-block;flex-shrink:0"></span>
            <span style="color:var(--ink-dim)">{{ $flowLbl }}</span>
        </div>
    </div>

</div>

{{-- ── Dois painéis inferiores ──────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;min-height:0;flex:1">

    {{-- Alertas recentes --}}
    <div style="background:var(--panel);border:1px solid var(--line);border-radius:12px;display:flex;flex-direction:column;overflow:hidden;min-height:200px">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:0.9rem 1.25rem;border-bottom:1px solid var(--line);flex-shrink:0">
            <span style="font-size:0.8rem;font-weight:700;color:var(--ink)">Alertas recentes</span>
            @if($metrics['alerts_count'] > 0)
                <div style="display:flex;align-items:center;gap:0.6rem">
                    <span style="background:var(--status-critico);color:#fff;font-size:0.65rem;font-weight:700;border-radius:99px;padding:0.12rem 0.55rem" id="alert-badge">{{ $metrics['alerts_count'] }}</span>
                    <a href="{{ route('alerts.index') }}" style="font-size:0.72rem;color:var(--flow);text-decoration:none;font-weight:500">Ver todos</a>
                </div>
            @endif
        </div>
        <ul style="list-style:none;margin:0;padding:0;overflow-y:auto;flex:1" id="alert-list" role="log">
            @forelse($activeAlerts->take(12) as $alert)
                @php
                    $aSt    = $alert->severidade;
                    $aCor   = $statusColors[$aSt] ?? '#EF4444';
                    $aGlow  = $statusGlows[$aSt]  ?? 'rgba(239,68,68,0.1)';
                    $aCss   = 'font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:' . $aCor;
                    $aLabel = ['atencao'=>'Atenção','risco'=>'Risco','critico'=>'Crítico'][$aSt] ?? $aSt;
                @endphp
                <li style="display:flex;align-items:stretch;border-bottom:1px solid color-mix(in srgb,var(--line) 50%,transparent);padding:0.55rem 1.25rem 0.55rem 0">
                    <div style="width:3px;flex-shrink:0;border-radius:2px;margin-right:0.85rem;background:{{ $aCor }}"></div>
                    <div style="flex:1;min-width:0">
                        <div style="display:flex;align-items:baseline;gap:0.5rem">
                            <span style="font-size:0.8rem;font-weight:600;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $alert->sensor->nome ?? '-' }}</span>
                            <span style="{{ $aCss }}">{{ $aLabel }}</span>
                        </div>
                        <div style="font-size:0.73rem;color:var(--ink-dim);margin-top:0.1rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $alert->mensagem }}</div>
                    </div>
                    <span style="font-size:0.67rem;font-family:var(--font-data);color:var(--ink-muted);flex-shrink:0;margin-left:0.5rem;padding-top:0.1rem">{{ $alert->created_at->format('H:i') }}</span>
                </li>
            @empty
                <li style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2.5rem 1rem;gap:0.5rem;color:var(--ink-dim)">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--status-ok)" stroke-width="1.5" stroke-linecap="round">
                        <path d="M20 6L9 17l-5-5"/>
                    </svg>
                    <span style="font-size:0.82rem;font-weight:600;color:var(--ink)">Tudo operacional</span>
                    <span style="font-size:0.75rem;text-align:center">Todos os sensores dentro dos parâmetros normais.</span>
                </li>
            @endforelse
        </ul>
    </div>

    {{-- Rede de sensores --}}
    <div style="background:var(--panel);border:1px solid var(--line);border-radius:12px;display:flex;flex-direction:column;overflow:hidden;min-height:200px">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:0.9rem 1.25rem;border-bottom:1px solid var(--line);flex-shrink:0">
            <span style="font-size:0.8rem;font-weight:700;color:var(--ink)">Rede de sensores</span>
            <span style="font-size:0.72rem;font-family:var(--font-data);color:var(--ink-dim)">{{ $metrics['sensors_count'] }} ativo{{ $metrics['sensors_count'] !== 1 ? 's' : '' }}</span>
        </div>
        <ul style="list-style:none;margin:0;padding:0;overflow-y:auto;flex:1">
            @forelse($sensors as $s)
                @php
                    $sCor  = $statusColors[$s->status] ?? '#10B981';
                    $sObs  = $s->ultimaLeitura?->obstrucao_pct ?? 0;
                    $sFlow = $s->ultimaLeitura?->vazao_lps     ?? 0;
                    $sSt   = $s->status;
                    $sLbl  = $statusLabels[$sSt] ?? 'Normal';
                    $sObsCss = 'font-size:0.62rem;font-weight:700;color:' . $sCor;
                @endphp
                <li style="display:flex;align-items:center;gap:0.75rem;border-bottom:1px solid color-mix(in srgb,var(--line) 50%,transparent);padding:0.55rem 1.25rem">
                    <div style="width:8px;height:8px;border-radius:50%;background:{{ $sCor }};flex-shrink:0;{{ $sSt === 'critico' ? 'box-shadow:0 0 8px '.$sCor.';animation:sensor-pulse 1.2s ease-in-out infinite' : '' }}"></div>
                    <div style="flex:1;min-width:0">
                        <div style="display:flex;align-items:baseline;gap:0.4rem;margin-bottom:0.18rem">
                            <span style="font-size:0.8rem;font-weight:600;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $s->nome }}</span>
                            <span style="font-size:0.65rem;font-family:var(--font-data);color:var(--ink-muted)">{{ $s->codigo }}</span>
                        </div>
                        <div style="height:3px;background:var(--line);border-radius:2px;overflow:hidden">
                            <div style="height:100%;width:{{ max(2,(int)$sObs) }}%;background:{{ $sCor }};border-radius:2px;transition:width 0.4s"></div>
                        </div>
                    </div>
                    <div style="text-align:right;flex-shrink:0">
                        <div style="{{ $sObsCss }}">{{ number_format($sObs,1) }}%</div>
                        <div style="font-size:0.62rem;color:var(--ink-muted);font-family:var(--font-data)">{{ number_format($sFlow,0) }} L/s</div>
                    </div>
                </li>
            @empty
                <li style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2.5rem 1rem;gap:0.5rem;color:var(--ink-dim)">
                    <span style="font-size:0.82rem;font-weight:600;color:var(--ink)">Sem sensores</span>
                    <span style="font-size:0.75rem">Cadastre sensores no mapa operacional.</span>
                </li>
            @endforelse
        </ul>
    </div>

</div>
@stop
