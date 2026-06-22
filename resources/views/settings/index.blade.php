@extends('layout.body')

@section('title', 'AquaSense - Configurações')

@section('content')
<div class="dash-header">
    <div>
        <h1 class="dash-header-title">Configurações</h1>
        <div class="dash-header-meta">
            <span>Parâmetros do sistema de monitoramento</span>
        </div>
    </div>
</div>

@if(session('success'))
    <div style="margin:0 1.5rem 1rem;padding:0.75rem 1rem;background:var(--status-ok-dim);border:1px solid var(--status-ok);border-radius:8px;color:var(--status-ok);font-size:0.85rem">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="margin:0 1.5rem 1rem;padding:0.75rem 1rem;background:var(--status-critico-dim);border:1px solid var(--status-critico);border-radius:8px;color:var(--status-critico);font-size:0.85rem">
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ route('settings.update') }}" style="padding:0 1.5rem 2rem">
    @csrf

    {{-- Modo de simulação --}}
    <section class="settings-section">
        <h2 class="settings-section-title">Modo de simulação de chuva</h2>
        @php $modoSim = old('modo_simulacao', $settings->get('modo_simulacao')?->valor ?? 'normal'); @endphp

        <div class="settings-field">
            <label class="settings-label">
                Intensidade da chuva simulada
                <span class="settings-hint">Controla o comportamento dos dados gerados automaticamente pelos sensores.</span>
            </label>
            <div style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-top:0.25rem">
                @foreach([
                    // [$val, $lbl, $desc, $chuva, $tSubida (10→100%), $tDescida (100→10%)]
                    ['sem_chuva',   'Sem chuva',   'Drenagem natural, sem precipitação significativa',   '0–0,30 mm', null,      '~80 min'],
                    ['chuva_fraca', 'Chuva fraca', 'Precipitação leve, obstrução sobe lentamente',       '2–6 mm',    '~65 min', null],
                    ['normal',      'Moderada',    'Precipitação moderada, obstrução sobe gradualmente', '4–10 mm',   '~45 min', null],
                    ['chuva_forte', 'Chuva forte', 'Precipitação alta, obstrução sobe rápido',           '8–16 mm',   '~26 min', null],
                    ['tempestade',  'Tempestade',  'Precipitação máxima, risco crítico acelerado',       '15–25 mm',  '~17 min', null],
                ] as [$val, $lbl, $desc, $chuva, $tSubida, $tDescida])
                    <label style="display:flex;align-items:flex-start;gap:0.5rem;padding:0.6rem 0.85rem;border-radius:8px;border:1.5px solid {{ $modoSim === $val ? 'var(--flow)' : 'var(--line)' }};cursor:pointer;background:{{ $modoSim === $val ? 'var(--panel)' : 'transparent' }};transition:border-color 0.15s">
                        <input type="radio" name="modo_simulacao" value="{{ $val }}" {{ $modoSim === $val ? 'checked' : '' }} style="margin-top:0.15rem;accent-color:var(--flow)">
                        <span>
                            <span style="display:block;font-size:0.82rem;font-weight:600;color:var(--ink)">{{ $lbl }}</span>
                            <span style="display:block;font-size:0.72rem;color:var(--ink-dim);margin-top:0.1rem">{{ $desc }}</span>
                            <span style="display:flex;gap:1rem;margin-top:0.35rem;flex-wrap:wrap">
                                <span style="font-size:0.68rem;color:var(--ink-dim)">Chuva: <strong style="color:var(--ink);font-weight:600">{{ $chuva }}</strong></span>
                                @if($tSubida)
                                <span style="font-size:0.68rem;color:var(--ink-dim)">10%→100%: <strong style="color:var(--ink);font-weight:600">{{ $tSubida }}</strong></span>
                                @endif
                                @if($tDescida)
                                <span style="font-size:0.68rem;color:var(--ink-dim)">100%→10%: <strong style="color:var(--ink);font-weight:600">{{ $tDescida }}</strong></span>
                                @endif
                            </span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Atualização da página --}}
    <section class="settings-section">
        <h2 class="settings-section-title">Atualização da página</h2>

        <div class="settings-field">
            <label class="settings-label">
                Intervalo de leitura e atualização
                <span class="settings-hint">Tempo entre cada coleta dos sensores e recarga automática da página.</span>
            </label>
            <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap">
                <input type="number" id="intervalo_atualizacao_seg" name="intervalo_atualizacao_seg"
                    value="{{ old('intervalo_atualizacao_seg', $settings->get('intervalo_atualizacao_seg')?->valor ?? 60) }}"
                    min="30" max="3600" class="settings-input" style="width:120px">
                <span style="font-size:0.8rem;color:var(--ink-dim)">segundos</span>
                <div style="display:flex;gap:0.4rem;flex-wrap:wrap">
                    @foreach([['30','30 s'],['60','1 min'],['300','5 min'],['600','10 min'],['1800','30 min'],['3600','1 h']] as [$val,$lbl])
                        <button type="button" onclick="document.getElementById('intervalo_atualizacao_seg').value='{{ $val }}'" class="settings-quick-btn">{{ $lbl }}</button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Limites de alerta --}}
    <section class="settings-section">
        <h2 class="settings-section-title">Limites de obstrução para alertas</h2>

        @foreach([
            ['limite_atencao', 'Atenção',  '#F59E0B', 'Obstrução (%) a partir da qual o sensor entra em estado de atenção.'],
            ['limite_risco',   'Risco',    '#F97316', 'Obstrução (%) a partir da qual o sensor entra em estado de risco.'],
            ['limite_critico', 'Crítico',  '#EF4444', 'Obstrução (%) a partir da qual o sensor entra em estado crítico.'],
        ] as [$key, $label, $cor, $hint])
            <div class="settings-field">
                <label for="{{ $key }}" class="settings-label">
                    <span style="display:inline-flex;align-items:center;gap:0.5rem">
                        <span style="width:10px;height:10px;border-radius:50%;background:{{ $cor }};flex-shrink:0;display:inline-block"></span>
                        {{ $label }}
                    </span>
                    <span class="settings-hint">{{ $hint }}</span>
                </label>
                <div style="display:flex;align-items:center;gap:0.75rem">
                    <input type="number" id="{{ $key }}" name="{{ $key }}"
                        value="{{ old($key, $settings->get($key)?->valor ?? '') }}"
                        min="1" max="99" class="settings-input" style="width:90px">
                    <span style="font-size:0.8rem;color:var(--ink-dim)">%</span>
                </div>
                @error($key)
                    <p style="color:var(--status-critico);font-size:0.75rem;margin-top:0.4rem">{{ $message }}</p>
                @enderror
            </div>
        @endforeach
    </section>

    <button type="submit" class="settings-save-btn">Salvar configurações</button>
</form>

{{-- Zona de perigo --}}
<div style="padding:0 1.5rem 3rem">
    <h2 style="font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--status-critico);margin-bottom:1rem;padding-bottom:0.5rem;border-bottom:1px solid var(--line)">
        Zona de perigo
    </h2>
    <div style="background:var(--panel);border:1px solid var(--line);border-radius:10px;padding:1.25rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap">
        <div>
            <div style="font-size:0.9rem;font-weight:600;color:var(--ink);margin-bottom:0.25rem">Limpar todas as tabelas</div>
            <div style="font-size:0.78rem;color:var(--ink-dim)">
                Remove todas as leituras, alertas e registros de manutenção. Sensores e configurações são mantidos.<br>
                <strong style="color:var(--status-critico)">Esta ação não pode ser desfeita.</strong>
            </div>
        </div>
        <form method="POST" action="{{ route('settings.clear') }}" onsubmit="return confirm('Tem certeza? Todos os registros de leituras, alertas e manutenções serão apagados permanentemente.')">
            @csrf
            <button type="submit" style="padding:0.55rem 1.25rem;background:transparent;color:var(--status-critico);font-size:0.85rem;font-weight:700;border:1.5px solid var(--status-critico);border-radius:var(--radius-md);cursor:pointer;font-family:var(--font-body);white-space:nowrap">
                Limpar tabelas
            </button>
        </form>
    </div>
</div>
@stop

@push('styles')
<style>
.settings-section { margin-bottom: 2rem; }
.settings-section-title {
    font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--ink-dim); margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--line);
}
.settings-field { margin-bottom: 1.25rem; }
.settings-label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--ink); margin-bottom: 0.5rem; }
.settings-hint { display: block; font-size: 0.75rem; font-weight: 400; color: var(--ink-dim); margin-top: 0.2rem; }
.settings-input {
    background: var(--panel); border: 1px solid var(--line); color: var(--ink);
    padding: 0.45rem 0.75rem; border-radius: var(--radius-md); font-size: 0.9rem;
    font-family: var(--font-data); transition: border-color var(--transition-fast);
}
.settings-input:focus { outline: none; border-color: var(--flow); }
.settings-quick-btn {
    padding: 0.25rem 0.6rem; font-size: 0.72rem; background: var(--panel);
    border: 1px solid var(--line); border-radius: 5px; color: var(--ink-dim); cursor: pointer;
    transition: border-color var(--transition-fast), color var(--transition-fast); font-family: var(--font-body);
}
.settings-quick-btn:hover { border-color: var(--flow); color: var(--flow); }
.settings-save-btn {
    padding: 0.6rem 1.5rem; background: var(--flow); color: var(--void); font-weight: 700;
    font-size: 0.85rem; border: none; border-radius: var(--radius-md); cursor: pointer;
    transition: opacity var(--transition-fast); font-family: var(--font-body); margin-bottom: 2.5rem;
}
.settings-save-btn:hover { opacity: 0.85; }
</style>
@endpush
