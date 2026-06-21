@extends('layout.body')

@section('title', 'AquaSense — Alertas')

@section('content')
<div class="dash-header">
    <div>
        <h1 class="dash-header-title">Central de alertas</h1>
        <div class="dash-header-meta">
            <span>Ativos: {{ $activeAlerts->count() }}</span>
            <span aria-hidden="true">·</span>
            <span>Resolvidos recentes: {{ $resolvedAlerts->count() }}</span>
        </div>
    </div>
</div>

<section class="alert-section" aria-label="Alertas ativos">
    <div class="alert-header">
        <h2 class="alert-header-title">Alertas ativos</h2>
        @if($activeAlerts->count() > 0)
            <span class="alert-badge">{{ $activeAlerts->count() }}</span>
        @endif
    </div>

    <ul class="alert-list" role="log">
        @forelse($activeAlerts as $alert)
            <li class="alert-item">
                <div class="alert-item-bar {{ $alert->severidade }}" aria-hidden="true"></div>
                <div class="alert-item-body">
                    <div class="alert-item-location">
                        {{ $alert->sensor->nome ?? '—' }}
                        <span style="font-size:0.7rem;opacity:0.6;margin-left:0.5rem">{{ $alert->sensor->codigo ?? '' }}</span>
                    </div>
                    <div class="alert-item-detail">{{ $alert->mensagem }}</div>
                    <div class="alert-item-time">{{ $alert->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <span class="alert-item-action" style="font-size:0.7rem;font-weight:600;text-transform:uppercase;color:var(--status-{{ $alert->severidade }})">
                    {{ ucfirst($alert->severidade) }}
                </span>
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

@if($resolvedAlerts->count() > 0)
<section class="alert-section" aria-label="Alertas resolvidos" style="margin-top:2rem">
    <div class="alert-header">
        <h2 class="alert-header-title" style="opacity:0.6">Resolvidos recentemente</h2>
    </div>

    <ul class="alert-list" role="log">
        @foreach($resolvedAlerts as $alert)
            <li class="alert-item" style="opacity:0.55">
                <div class="alert-item-bar ok" aria-hidden="true"></div>
                <div class="alert-item-body">
                    <div class="alert-item-location">{{ $alert->sensor->nome ?? '—' }}</div>
                    <div class="alert-item-detail">{{ $alert->mensagem }}</div>
                    <div class="alert-item-time">
                        Resolvido em {{ $alert->resolvido_em->format('d/m/Y H:i') }}
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</section>
@endif
@stop
