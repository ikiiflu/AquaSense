@extends('layout.body')

@section('title', 'AquaSense - Manutenção')

@section('content')
<div class="dash-header">
    <div>
        <h1 class="dash-header-title">Manutenção</h1>
        <div class="dash-header-meta">
            <span>{{ $records->total() }} registros</span>
        </div>
    </div>
</div>

<section style="padding:0 1.5rem 2rem;overflow-x:auto">
    @if($records->isEmpty())
        <div class="empty-state" style="margin-top:3rem">
            <div class="empty-state-icon">○</div>
            <div class="empty-state-title">Sem registros de manutenção</div>
            <div class="empty-state-desc">Os registros serão exibidos aqui após a primeira manutenção cadastrada.</div>
        </div>
    @else
        <table style="width:100%;border-collapse:collapse;font-size:0.82rem">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid var(--line);color:var(--ink-dim)">
                    <th style="padding:0.5rem 0.75rem">Data</th>
                    <th style="padding:0.5rem 0.75rem">Sensor</th>
                    <th style="padding:0.5rem 0.75rem">Operador</th>
                    <th style="padding:0.5rem 0.75rem">Descrição</th>
                    <th style="padding:0.5rem 0.75rem">Observações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $r)
                    <tr style="border-bottom:1px solid color-mix(in srgb,var(--line) 50%,transparent)">
                        <td style="padding:0.5rem 0.75rem;font-family:var(--font-mono);color:var(--ink-dim)">
                            {{ $r->realizado_em->format('d/m/Y H:i') }}
                        </td>
                        <td style="padding:0.5rem 0.75rem">
                            <span style="font-size:0.7rem;color:var(--flow);font-family:var(--font-mono)">
                                {{ $r->sensor->codigo ?? '-' }}
                            </span><br>
                            <span>{{ $r->sensor->nome ?? '-' }}</span>
                        </td>
                        <td style="padding:0.5rem 0.75rem">{{ $r->operador }}</td>
                        <td style="padding:0.5rem 0.75rem">{{ $r->descricao }}</td>
                        <td style="padding:0.5rem 0.75rem;color:var(--ink-dim)">{{ $r->observacoes ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:1.5rem">
            {{ $records->links() }}
        </div>
    @endif
</section>
@stop
