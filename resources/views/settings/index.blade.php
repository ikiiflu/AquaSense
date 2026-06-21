@extends('layout.body')

@section('title', 'AquaSense — Configurações')

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
    <div style="margin:0 1.5rem 1rem;padding:0.75rem 1rem;background:var(--status-ok-dim);
                border:1px solid var(--status-ok);border-radius:8px;color:var(--status-ok);font-size:0.85rem">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="margin:0 1.5rem 1rem;padding:0.75rem 1rem;background:var(--status-critico-dim);
                border:1px solid var(--status-critico);border-radius:8px;color:var(--status-critico);font-size:0.85rem">
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ route('settings.update') }}" style="padding:0 1.5rem 2rem">
    @csrf

    {{-- ---- Coleta de dados ---- --}}
    <section style="margin-bottom:2rem">
        <h2 style="font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;
                   color:var(--ink-dim);margin-bottom:1rem;padding-bottom:0.5rem;
                   border-bottom:1px solid var(--line)">
            Coleta de dados
        </h2>

        <div class="settings-field">
            <label for="intervalo_leitura_seg" class="settings-label">
                Intervalo entre leituras
                <span class="settings-hint">Tempo (em segundos) entre cada coleta automática de todos os sensores.</span>
            </label>
            <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap">
                <input
                    type="number"
                    id="intervalo_leitura_seg"
                    name="intervalo_leitura_seg"
                    value="{{ old('intervalo_leitura_seg', $settings->get('intervalo_leitura_seg')?->valor ?? 60) }}"
                    min="10"
                    max="86400"
                    class="settings-input"
                    style="width:120px">
                <span style="font-size:0.8rem;color:var(--ink-dim)">segundos</span>

                <div style="display:flex;gap:0.4rem;flex-wrap:wrap">
                    @foreach([['30','30 s'],['60','1 min'],['300','5 min'],['600','10 min'],['1800','30 min'],['3600','1 h']] as [$val,$lbl])
                        <button type="button"
                                onclick="document.getElementById('intervalo_leitura_seg').value='{{ $val }}'"
                                class="settings-quick-btn">
                            {{ $lbl }}
                        </button>
                    @endforeach
                </div>
            </div>
            @error('intervalo_leitura_seg')
                <p style="color:var(--status-critico);font-size:0.75rem;margin-top:0.4rem">{{ $message }}</p>
            @enderror
        </div>
    </section>

    {{-- ---- Limites de alerta ---- --}}
    <section style="margin-bottom:2rem">
        <h2 style="font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;
                   color:var(--ink-dim);margin-bottom:1rem;padding-bottom:0.5rem;
                   border-bottom:1px solid var(--line)">
            Limites de obstrução para alertas
        </h2>

        @foreach([
            ['limite_atencao', 'Atenção',  'atencao', 'Obstrução (%) a partir da qual o sensor entra em estado de atenção.'],
            ['limite_risco',   'Risco',    'risco',   'Obstrução (%) a partir da qual o sensor entra em estado de risco.'],
            ['limite_critico', 'Crítico',  'critico', 'Obstrução (%) a partir da qual o sensor entra em estado crítico.'],
        ] as [$key, $label, $status, $hint])
            <div class="settings-field">
                <label for="{{ $key }}" class="settings-label">
                    <span style="display:inline-flex;align-items:center;gap:0.5rem">
                        <span class="threshold-dot threshold-dot--{{ $status }}"></span>
                        {{ $label }}
                    </span>
                    <span class="settings-hint">{{ $hint }}</span>
                </label>
                <div style="display:flex;align-items:center;gap:0.75rem">
                    <input
                        type="number"
                        id="{{ $key }}"
                        name="{{ $key }}"
                        value="{{ old($key, $settings->get($key)?->valor ?? '') }}"
                        min="1"
                        max="99"
                        class="settings-input"
                        style="width:90px">
                    <span style="font-size:0.8rem;color:var(--ink-dim)">%</span>
                </div>
                @error($key)
                    <p style="color:var(--status-critico);font-size:0.75rem;margin-top:0.4rem">{{ $message }}</p>
                @enderror
            </div>
        @endforeach
    </section>

    <button type="submit" class="settings-save-btn">
        Salvar configurações
    </button>
</form>

{{-- ---- Zona de perigo: limpar tabelas ---- --}}
<div style="padding:0 1.5rem 3rem">
    <h2 style="font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;
               color:var(--status-critico);margin-bottom:1rem;padding-bottom:0.5rem;
               border-bottom:1px solid color-mix(in srgb,var(--status-critico) 35%,transparent)">
        Zona de perigo
    </h2>

    <div style="background:var(--panel);border:1px solid color-mix(in srgb,var(--status-critico) 35%,transparent);
                border-radius:10px;padding:1.25rem;display:flex;align-items:center;
                justify-content:space-between;gap:1rem;flex-wrap:wrap">
        <div>
            <div style="font-size:0.9rem;font-weight:600;color:var(--ink);margin-bottom:0.25rem">
                Limpar todas as tabelas
            </div>
            <div style="font-size:0.78rem;color:var(--ink-dim)">
                Remove todas as leituras, alertas e registros de manutenção. Os sensores e configurações são mantidos.<br>
                <strong style="color:var(--status-critico)">Esta ação não pode ser desfeita.</strong>
            </div>
        </div>
        <form method="POST" action="{{ route('settings.clear') }}"
              onsubmit="return confirm('Tem certeza? Todos os registros de leituras, alertas e manutenções serão apagados permanentemente.')">
            @csrf
            <button type="submit" style="padding:0.55rem 1.25rem;background:transparent;color:var(--status-critico);
                                         font-size:0.85rem;font-weight:700;border:1.5px solid var(--status-critico);
                                         border-radius:var(--radius-md);cursor:pointer;font-family:var(--font-body);
                                         white-space:nowrap">
                Limpar tabelas
            </button>
        </form>
    </div>
</div>
@stop

@push('styles')
<style>
.settings-field {
    margin-bottom: 1.25rem;
}
.settings-label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 0.5rem;
}
.settings-hint {
    display: block;
    font-size: 0.75rem;
    font-weight: 400;
    color: var(--ink-dim);
    margin-top: 0.2rem;
}
.settings-input {
    background: var(--panel);
    border: 1px solid var(--line);
    color: var(--ink);
    padding: 0.45rem 0.75rem;
    border-radius: var(--radius-md);
    font-size: 0.9rem;
    font-family: var(--font-data);
    transition: border-color var(--transition-fast);
}
.settings-input:focus {
    outline: none;
    border-color: var(--flow);
}
.settings-quick-btn {
    padding: 0.25rem 0.6rem;
    font-size: 0.72rem;
    background: var(--panel);
    border: 1px solid var(--line);
    border-radius: 5px;
    color: var(--ink-dim);
    cursor: pointer;
    transition: border-color var(--transition-fast), color var(--transition-fast);
    font-family: var(--font-body);
}
.settings-quick-btn:hover {
    border-color: var(--flow);
    color: var(--flow);
}
.settings-save-btn {
    padding: 0.6rem 1.5rem;
    background: var(--flow);
    color: var(--void);
    font-weight: 700;
    font-size: 0.85rem;
    border: none;
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: opacity var(--transition-fast);
    font-family: var(--font-body);
    margin-bottom: 2.5rem;
}
.settings-save-btn:hover {
    opacity: 0.85;
}
.threshold-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}
.threshold-dot--atencao { background: var(--status-atencao); }
.threshold-dot--risco   { background: var(--status-risco); }
.threshold-dot--critico { background: var(--status-critico); }
</style>
@endpush
