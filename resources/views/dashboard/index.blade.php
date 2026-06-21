@extends('layout.body')

@section('title', 'AquaSense — Dashboard')

@section('content')
<div class="dash-header">
    <div>
        <h1 class="dash-header-title">Visão operacional</h1>
        <div class="dash-header-meta">
            <span>Sensores: {{ $metrics['sensors_count'] }}</span>
            <span aria-hidden="true">·</span>
            <span>Alertas ativos: {{ $metrics['alerts_count'] }}</span>
        </div>
    </div>
</div>

{{-- Cards de métricas --}}
<section class="metrics-grid" aria-label="Indicadores da rede de drenagem">

    <div class="metric-card" role="region" aria-label="Percentual de obstrução médio">
        <div class="metric-card-eyebrow">
            <svg class="metric-card-icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.5" />
                <circle cx="8" cy="5" r="1.5" fill="currentColor" />
            </svg>
            Obst. média
        </div>
        <div class="metric-card-value" id="metric-obstruction" aria-live="polite">
            @if(!is_null($metrics['avg_obstruction']))
                {{ $metrics['avg_obstruction'] }}%
            @else
                —
            @endif
            <span class="unit">obstrução</span>
        </div>
        <div class="metric-card-status">
            @php
                $obs = $metrics['avg_obstruction'] ?? 0;
                $obsStatus = is_null($metrics['avg_obstruction']) ? 'ok' : ($obs >= 70 ? 'critico' : ($obs >= 40 ? 'risco' : ($obs >= 10 ? 'atencao' : 'ok')));
                $obsLabel  = is_null($metrics['avg_obstruction']) ? 'Sem dados' : ['ok' => 'Dentro do limite', 'atencao' => 'Atenção', 'risco' => 'Risco elevado', 'critico' => 'Crítico'][$obsStatus];
            @endphp
            <div class="metric-card-status-dot {{ $obsStatus }}" aria-hidden="true"></div>
            {{ $obsLabel }}
        </div>
        @if(!is_null($metrics['avg_obstruction']))
        <div class="metric-card-spark" aria-hidden="true"></div>
        @endif
    </div>

    <div class="metric-card" role="region" aria-label="Índice pluviométrico">
        <div class="metric-card-eyebrow">
            <svg class="metric-card-icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M8 1v11M5 6l3-5 3 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                <path d="M4 9l4-3 4 3v4H4V9Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
            </svg>
            Precipitação
        </div>
        <div class="metric-card-value" id="metric-rainfall" aria-live="polite">
            @if(!is_null($metrics['avg_rainfall']))
                {{ $metrics['avg_rainfall'] }}
            @else
                —
            @endif
            <span class="unit">mm</span>
        </div>
        <div class="metric-card-status">
            @php
                $rain = $metrics['avg_rainfall'] ?? 0;
                $rainLabel = is_null($metrics['avg_rainfall']) ? 'Sem dados' : ($rain > 10 ? 'Chuva intensa' : ($rain > 4 ? 'Chuva moderada' : 'Chuva fraca'));
            @endphp
            <div class="metric-card-status-dot ok" aria-hidden="true"></div>
            {{ $rainLabel }}
        </div>
        @if(!is_null($metrics['avg_rainfall']))
        <div class="metric-card-spark" aria-hidden="true"></div>
        @endif
    </div>

    <div class="metric-card" role="region" aria-label="Volume de vazão das galerias">
        <div class="metric-card-eyebrow">
            <svg class="metric-card-icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M2 8c2-4 4-6 6-6s4 2 6 6-4 6-6 6-4-2-6-6Z" stroke="currentColor" stroke-width="1.5" />
                <path d="M6 8h4M8 6v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            Vazão
        </div>
        <div class="metric-card-value" id="metric-flow" aria-live="polite">
            @if(!is_null($metrics['avg_flow']))
                {{ $metrics['avg_flow'] }}
            @else
                —
            @endif
            <span class="unit">L/s</span>
        </div>
        <div class="metric-card-status">
            <div class="metric-card-status-dot ok" aria-hidden="true"></div>
            {{ is_null($metrics['avg_flow']) ? 'Sem dados' : 'Fluxo normal' }}
        </div>
        @if(!is_null($metrics['avg_flow']))
        <div class="metric-card-spark" aria-hidden="true"></div>
        @endif
    </div>

</section>

{{-- Feed de alertas --}}
<section class="alert-section" aria-label="Central de alertas">
    <div class="alert-header">
        <h2 class="alert-header-title">Alertas recentes</h2>
        @if($metrics['alerts_count'] > 0)
            <span class="alert-badge" id="alert-badge" aria-live="polite">{{ $metrics['alerts_count'] }}</span>
        @endif
    </div>

    <ul class="alert-list" id="alert-list" role="log" aria-label="Lista de alertas ativos">
        @forelse($activeAlerts as $alert)
            <li class="alert-item">
                <div class="alert-item-bar {{ $alert->severidade }}" aria-hidden="true"></div>
                <div class="alert-item-body">
                    <div class="alert-item-location">{{ $alert->sensor->nome ?? '—' }}</div>
                    <div class="alert-item-detail">{{ $alert->mensagem }}</div>
                    <div class="alert-item-time">{{ $alert->created_at->format('H:i') }}</div>
                </div>
                <a href="{{ route('alerts.index') }}" class="alert-item-action">Ver</a>
            </li>
        @empty
            <li class="empty-state">
                <div class="empty-state-icon">✓</div>
                <div class="empty-state-title">Nenhum alerta ativo</div>
                <div class="empty-state-desc">Todos os sensores operam dentro dos parâmetros normais.</div>
            </li>
        @endforelse
    </ul>
</section>
@stop
