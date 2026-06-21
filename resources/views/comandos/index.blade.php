@extends('layout.body')

@section('title', 'AquaSense — Últimos Comandos')

@section('content')
<div class="dash-header">
    <div>
        <h1 class="dash-header-title">Últimos Comandos SQL</h1>
        <div class="dash-header-meta">
            <span>{{ $logs->count() }} registros</span>
            <span aria-hidden="true">·</span>
            <span>INSERT / UPDATE / DELETE</span>
        </div>
    </div>
    <form method="POST" action="{{ route('comandos.clear') }}"
          onsubmit="return confirm('Limpar o log de comandos?')">
        @csrf
        <button type="submit" style="padding:0.4rem 0.9rem;background:transparent;color:var(--ink-dim);
                                     font-size:0.8rem;border:1px solid var(--line);border-radius:var(--radius-md);
                                     cursor:pointer;font-family:var(--font-body)">
            Limpar log
        </button>
    </form>
</div>

<section style="padding:0 1.5rem 2rem;overflow-x:auto">
    @if($logs->isEmpty())
        <div class="empty-state" style="margin-top:3rem">
            <div class="empty-state-icon">○</div>
            <div class="empty-state-title">Nenhum comando registrado</div>
            <div class="empty-state-desc">Os comandos INSERT, UPDATE e DELETE executados pelo sistema aparecerão aqui.</div>
        </div>
    @else
        <table style="width:100%;border-collapse:collapse;font-size:0.78rem;font-family:var(--font-mono)">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid var(--line);color:var(--ink-dim)">
                    <th style="padding:0.5rem 0.75rem;white-space:nowrap">Hora</th>
                    <th style="padding:0.5rem 0.75rem;white-space:nowrap">ms</th>
                    <th style="padding:0.5rem 0.75rem">SQL</th>
                    <th style="padding:0.5rem 0.75rem">Bindings</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    @php
                        $sql = $log->sql_query;
                        $op  = strtoupper(substr(ltrim($sql), 0, 6));
                        $opColor = match($op) {
                            'INSERT' => 'var(--status-ok)',
                            'UPDATE' => 'var(--status-atencao)',
                            'DELETE' => 'var(--status-critico)',
                            default  => 'var(--ink-dim)',
                        };
                    @endphp
                    <tr style="border-bottom:1px solid color-mix(in srgb,var(--line) 40%,transparent);vertical-align:top">
                        <td style="padding:0.45rem 0.75rem;color:var(--ink-dim);white-space:nowrap">
                            {{ $log->executado_em->format('H:i:s') }}<br>
                            <span style="font-size:0.7rem;opacity:0.6">{{ $log->executado_em->format('d/m') }}</span>
                        </td>
                        <td style="padding:0.45rem 0.75rem;color:var(--ink-dim);white-space:nowrap">
                            {{ number_format($log->tempo_ms, 1) }}
                        </td>
                        <td style="padding:0.45rem 0.75rem;max-width:520px">
                            <span style="font-weight:700;color:{{ $opColor }}">{{ $op }}</span><span style="color:var(--ink-dim)">{{ substr($sql, 6) }}</span>
                        </td>
                        <td style="padding:0.45rem 0.75rem;color:var(--ink-dim);max-width:200px;word-break:break-all">
                            @if($log->bindings)
                                {{ implode(', ', array_map(fn($b) => is_null($b) ? 'NULL' : $b, $log->bindings)) }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</section>
@stop
