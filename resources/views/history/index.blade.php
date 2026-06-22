@extends('layout.body')

@section('title', 'AquaSense - Histórico')

@section('content')
<div class="dash-header">
    <div>
        <h1 class="dash-header-title">Histórico de leituras</h1>
        <div class="dash-header-meta">
            <span>{{ number_format($readings->total()) }} registros</span>
            <span aria-hidden="true">·</span>
            <span>Página {{ $readings->currentPage() }} de {{ $readings->lastPage() }}</span>
        </div>
    </div>
</div>

<section style="padding:0 1.5rem 1rem">
    <form method="GET" action="{{ route('history.index') }}"
          style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap">
        <label for="sensor_id" style="font-size:0.8rem;color:var(--ink-dim)">Sensor:</label>
        <select id="sensor_id" name="sensor_id" onchange="this.form.submit()"
                style="background:var(--panel);border:1px solid var(--line);color:var(--ink);
                       padding:0.4rem 0.75rem;border-radius:6px;font-size:0.85rem">
            @foreach($sensors as $s)
                <option value="{{ $s->id }}" {{ $s->id == $selected ? 'selected' : '' }}>
                    {{ $s->codigo }} - {{ $s->nome }}
                </option>
            @endforeach
        </select>
    </form>
</section>

<section style="padding:0 1.5rem;overflow-x:auto">
    @if($readings->isEmpty())
        <div class="empty-state" style="margin-top:3rem">
            <div class="empty-state-icon">○</div>
            <div class="empty-state-title">Sem leituras</div>
            <div class="empty-state-desc">As leituras são geradas automaticamente enquanto você navega pelo sistema.</div>
        </div>
    @else
        <table style="width:100%;border-collapse:collapse;font-size:0.82rem;font-family:var(--font-mono)">
            <thead>
                <tr style="text-align:left;border-bottom:2px solid var(--line);color:var(--ink-dim)">
                    <th style="padding:0.5rem 0.75rem;font-weight:600">#</th>
                    <th style="padding:0.5rem 0.75rem;font-weight:600">Data/Hora</th>
                    <th style="padding:0.5rem 0.75rem;font-weight:600">Obstrução (%)</th>
                    <th style="padding:0.5rem 0.75rem;font-weight:600">Precipitação (mm)</th>
                    <th style="padding:0.5rem 0.75rem;font-weight:600">Vazão (L/s)</th>
                    <th style="padding:0.5rem 0.75rem;font-weight:600">Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $stColors = ['ok'=>'#10B981','atencao'=>'#F59E0B','risco'=>'#F97316','critico'=>'#EF4444'];
                    $stLabels = ['ok'=>'Normal','atencao'=>'Atenção','risco'=>'Risco','critico'=>'Crítico'];
                @endphp
                @foreach($readings as $i => $r)
                    @php
                        $obs = $r->obstrucao_pct;
                        $st  = $obs >= 70 ? 'critico' : ($obs >= 40 ? 'risco' : ($obs >= 10 ? 'atencao' : 'ok'));
                        $cor = $stColors[$st];
                    @endphp
                    <tr class="history-row">
                        <td style="padding:0.45rem 0.75rem;color:var(--ink-muted);font-size:0.75rem">{{ $r->id }}</td>
                        <td style="padding:0.45rem 0.75rem;color:var(--ink-dim)">{{ $r->registrado_em->format('d/m/Y H:i:s') }}</td>
                        <td style="padding:0.45rem 0.75rem">{{ number_format($r->obstrucao_pct, 2) }}</td>
                        <td style="padding:0.45rem 0.75rem">{{ number_format($r->precipitacao_mm, 3) }}</td>
                        <td style="padding:0.45rem 0.75rem">{{ number_format($r->vazao_lps, 3) }}</td>
                        <td style="padding:0.45rem 0.75rem">
                            <span style="color:{{ $cor }};font-weight:600">{{ $stLabels[$st] ?? $st }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</section>

{{-- Paginação --}}
@if($readings->lastPage() > 1)
<nav style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.5rem 2rem;gap:1rem;flex-wrap:wrap" aria-label="Paginação">

    {{-- Info --}}
    <span style="font-size:0.78rem;color:var(--ink-dim);font-family:var(--font-data)">
        Exibindo {{ $readings->firstItem() }}–{{ $readings->lastItem() }}
        de {{ number_format($readings->total()) }}
    </span>

    {{-- Links --}}
    <div style="display:flex;gap:0.25rem;align-items:center;flex-wrap:wrap">

        {{-- Anterior --}}
        @if($readings->onFirstPage())
            <span style="{{ 'padding:0.35rem 0.65rem;border-radius:6px;font-size:0.8rem;border:1px solid var(--line);color:var(--ink-muted);cursor:default' }}">←</span>
        @else
            <a href="{{ $readings->previousPageUrl() }}" style="padding:0.35rem 0.65rem;border-radius:6px;font-size:0.8rem;border:1px solid var(--line);color:var(--ink-dim);text-decoration:none;transition:border-color 0.15s,color 0.15s" onmouseover="this.style.borderColor='var(--flow)';this.style.color='var(--flow)'" onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--ink-dim)'">←</a>
        @endif

        {{-- Janela de páginas --}}
        @php
            $cur   = $readings->currentPage();
            $last  = $readings->lastPage();
            $start = max(1, $cur - 2);
            $end   = min($last, $cur + 2);
        @endphp

        @if($start > 1)
            <a href="{{ $readings->url(1) }}" style="padding:0.35rem 0.65rem;border-radius:6px;font-size:0.8rem;border:1px solid var(--line);color:var(--ink-dim);text-decoration:none" onmouseover="this.style.borderColor='var(--flow)';this.style.color='var(--flow)'" onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--ink-dim)'">1</a>
            @if($start > 2)
                <span style="color:var(--ink-muted);font-size:0.8rem;padding:0 0.15rem">…</span>
            @endif
        @endif

        @for($p = $start; $p <= $end; $p++)
            @if($p === $cur)
                <span style="padding:0.35rem 0.65rem;border-radius:6px;font-size:0.8rem;border:1px solid var(--flow);color:var(--flow);background:var(--flow-dim);font-weight:700">{{ $p }}</span>
            @else
                <a href="{{ $readings->url($p) }}" style="padding:0.35rem 0.65rem;border-radius:6px;font-size:0.8rem;border:1px solid var(--line);color:var(--ink-dim);text-decoration:none" onmouseover="this.style.borderColor='var(--flow)';this.style.color='var(--flow)'" onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--ink-dim)'">{{ $p }}</a>
            @endif
        @endfor

        @if($end < $last)
            @if($end < $last - 1)
                <span style="color:var(--ink-muted);font-size:0.8rem;padding:0 0.15rem">…</span>
            @endif
            <a href="{{ $readings->url($last) }}" style="padding:0.35rem 0.65rem;border-radius:6px;font-size:0.8rem;border:1px solid var(--line);color:var(--ink-dim);text-decoration:none" onmouseover="this.style.borderColor='var(--flow)';this.style.color='var(--flow)'" onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--ink-dim)'">{{ $last }}</a>
        @endif

        {{-- Próximo --}}
        @if($readings->hasMorePages())
            <a href="{{ $readings->nextPageUrl() }}" style="padding:0.35rem 0.65rem;border-radius:6px;font-size:0.8rem;border:1px solid var(--line);color:var(--ink-dim);text-decoration:none;transition:border-color 0.15s,color 0.15s" onmouseover="this.style.borderColor='var(--flow)';this.style.color='var(--flow)'" onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--ink-dim)'">→</a>
        @else
            <span style="padding:0.35rem 0.65rem;border-radius:6px;font-size:0.8rem;border:1px solid var(--line);color:var(--ink-muted);cursor:default">→</span>
        @endif

    </div>
</nav>
@endif
@stop

@push('styles')
<style>
.history-row { border-bottom: 1px solid var(--line); transition: background 0.1s; }
.history-row:hover { background: var(--panel-raised); }
</style>
@endpush
