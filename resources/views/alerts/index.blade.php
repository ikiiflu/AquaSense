@extends('layout.body')

@section('title', 'AquaSense - Alertas')

@section('content')
<div class="dash-header">
    <div>
        <h1 class="dash-header-title">Central de alertas</h1>
        <div class="dash-header-meta">
            <span>Ativos: {{ $totalActive }}</span>
            <span aria-hidden="true">·</span>
            <span>Resolvidos recentes: {{ $resolvedAlerts->count() }}</span>
        </div>
    </div>
</div>

@if(session('success'))
    <div style="margin:0 1.5rem 1rem;padding:0.75rem 1rem;background:var(--status-ok-dim);border:1px solid var(--status-ok);border-radius:8px;color:var(--status-ok);font-size:0.85rem">
        {{ session('success') }}
    </div>
@endif

{{-- Layout de dois cards lado a lado --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;padding:0 1.5rem 2rem;height:calc(100vh - 10rem)">

    {{-- Card: Alertas ativos --}}
    <div style="background:var(--panel);border:1px solid var(--line);border-radius:12px;display:flex;flex-direction:column;overflow:hidden">

        {{-- Cabeçalho fixo --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid var(--line);flex-shrink:0">
            <h2 style="font-size:0.9rem;font-weight:700;color:var(--ink);margin:0">Alertas ativos</h2>
            @if($totalActive > 0)
                <span style="background:var(--status-critico);color:#fff;font-size:0.7rem;font-weight:700;border-radius:99px;padding:0.15rem 0.6rem;min-width:1.4rem;text-align:center">{{ $totalActive }}</span>
            @endif
        </div>

        {{-- Corpo scrollável --}}
        <div style="flex:1;overflow-y:auto;overflow-x:hidden">
            @if($activeAlerts->isEmpty())
                <div class="empty-state" style="margin-top:3rem">
                    <div class="empty-state-icon">✓</div>
                    <div class="empty-state-title">Nenhum alerta ativo</div>
                    <div class="empty-state-desc">Todos os sensores estão dentro dos parâmetros normais.</div>
                </div>
            @else
                @php $sevCores = ['atencao'=>'#F59E0B','risco'=>'#F97316','critico'=>'#EF4444']; @endphp
                @foreach($activeAlerts as $bairroNome => $alerts)
                    <div style="padding:0.6rem 1.25rem 0.25rem">
                        <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--ink-dim);padding-bottom:0.4rem;border-bottom:1px solid var(--line);margin-bottom:0.25rem">
                            {{ $bairroNome }} <span style="font-weight:400;margin-left:0.3rem">({{ $alerts->count() }})</span>
                        </div>
                    </div>
                    <ul style="list-style:none;margin:0;padding:0">
                        @foreach($alerts as $alert)
                            @php
                                $sevCor      = $sevCores[$alert->severidade] ?? '#EF4444';
                                $sevBadgeCss = 'font-size:0.65rem;font-weight:700;text-transform:uppercase;color:' . $sevCor . ';text-align:center;white-space:nowrap';
                            @endphp
                            <li style="display:flex;align-items:stretch;border-bottom:1px solid color-mix(in srgb,var(--line) 60%,transparent);padding:0.6rem 1.25rem 0.6rem 0">
                                <div style="width:3px;flex-shrink:0;border-radius:2px;margin-right:0.75rem;background:{{ $sevCor }}"></div>
                                <div style="flex:1;min-width:0">
                                    <div style="font-size:0.82rem;font-weight:600;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                        {{ $alert->sensor->nome ?? '-' }}
                                        <span style="font-size:0.7rem;opacity:0.5;font-weight:400;margin-left:0.35rem">{{ $alert->sensor->codigo ?? '' }}</span>
                                    </div>
                                    <div style="font-size:0.77rem;color:var(--ink-dim);margin-top:0.15rem;line-height:1.4">{{ $alert->mensagem }}</div>
                                    <div style="font-size:0.7rem;color:var(--ink-dim);opacity:0.6;margin-top:0.25rem">{{ $alert->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:0.3rem;flex-shrink:0;margin-left:0.75rem;padding-top:0.1rem">
                                    <span style="{{ $sevBadgeCss }}">{{ ucfirst($alert->severidade) }}</span>
                                    <form method="POST" action="{{ route('alerts.resolve', $alert) }}" style="margin:0">
                                        @csrf
                                        <button type="submit" style="padding:0.2rem 0.5rem;font-size:0.65rem;font-weight:600;border-radius:4px;border:1px solid var(--status-ok);color:var(--status-ok);cursor:pointer;font-family:var(--font-body);white-space:nowrap;background:transparent">
                                            Dar baixa
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('alerts.destroy', $alert) }}" style="margin:0" onsubmit="return confirm('Excluir este alerta permanentemente?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="padding:0.2rem 0.5rem;font-size:0.65rem;font-weight:600;border-radius:4px;border:1px solid var(--status-critico);color:var(--status-critico);cursor:pointer;font-family:var(--font-body);white-space:nowrap;background:transparent">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Card: Resolvidos --}}
    <div style="background:var(--panel);border:1px solid var(--line);border-radius:12px;display:flex;flex-direction:column;overflow:hidden">

        {{-- Cabeçalho fixo --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid var(--line);flex-shrink:0">
            <h2 style="font-size:0.9rem;font-weight:700;color:var(--ink-dim);margin:0">Resolvidos recentemente</h2>
            @if($resolvedAlerts->count() > 0)
                <span style="background:var(--panel);border:1px solid var(--line);color:var(--ink-dim);font-size:0.7rem;font-weight:600;border-radius:99px;padding:0.15rem 0.6rem">{{ $resolvedAlerts->count() }}</span>
            @endif
        </div>

        {{-- Corpo scrollável --}}
        <div style="flex:1;overflow-y:auto;overflow-x:hidden">
            @if($resolvedAlerts->isEmpty())
                <div class="empty-state" style="margin-top:3rem">
                    <div class="empty-state-icon">○</div>
                    <div class="empty-state-title">Nenhum registro</div>
                    <div class="empty-state-desc">Os alertas resolvidos aparecerão aqui.</div>
                </div>
            @else
                <ul style="list-style:none;margin:0;padding:0">
                    @foreach($resolvedAlerts as $alert)
                        <li style="display:flex;align-items:stretch;border-bottom:1px solid color-mix(in srgb,var(--line) 60%,transparent);padding:0.6rem 1.25rem 0.6rem 0;opacity:0.6">
                            <div style="width:3px;flex-shrink:0;border-radius:2px;margin-right:0.75rem;background:var(--status-ok)"></div>
                            <div style="flex:1;min-width:0">
                                <div style="font-size:0.82rem;font-weight:600;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                    {{ $alert->sensor->nome ?? '-' }}
                                </div>
                                <div style="font-size:0.77rem;color:var(--ink-dim);margin-top:0.15rem;line-height:1.4">{{ $alert->mensagem }}</div>
                                <div style="font-size:0.7rem;color:var(--status-ok);opacity:0.8;margin-top:0.25rem">Resolvido em {{ $alert->resolvido_em->format('d/m/Y H:i') }}</div>
                            </div>
                            <div style="flex-shrink:0;margin-left:0.75rem;display:flex;align-items:center">
                                <form method="POST" action="{{ route('alerts.destroy', $alert) }}" style="margin:0" onsubmit="return confirm('Excluir este alerta permanentemente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="padding:0.2rem 0.5rem;font-size:0.65rem;font-weight:600;border-radius:4px;border:1px solid var(--status-critico);color:var(--status-critico);cursor:pointer;font-family:var(--font-body);background:transparent">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

</div>
@stop
