<aside class="app-sidebar" role="navigation" aria-label="Navegação principal">
    <div class="sidebar-brand">
        <img src="{{ asset('img/logo_transparente.png') }}" alt="Drena Aí Logo" class="sidebar-brand-logo">
        <div class="sidebar-brand-text">
            <div class="sidebar-brand-name">Drena<span>Aí</span></div>
            <div class="sidebar-brand-tagline">Monitorar. Antecipar. Agir.</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="/" class="is-active" aria-current="page">
            <svg class="sidebar-nav-icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <rect x="1" y="1" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5" />
                <rect x="9" y="1" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5" />
                <rect x="1" y="9" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5" />
                <rect x="9" y="9" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5" />
            </svg>
            Dashboard
        </a>
        <a href="{{ route('map.operational_map') }}">
            <svg class="sidebar-nav-icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M8 1v14M4 5l4-4 4 4M3 15h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            Mapa operacional
        </a>
        <a href="#">
            <svg class="sidebar-nav-icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5" />
                <path d="M8 4v5M8 12v.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            Alertas
        </a>
        <a href="#">
            <svg class="sidebar-nav-icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M2 4h12M2 8h8M2 12h12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            Histórico
        </a>
        <a href="#">
            <svg class="sidebar-nav-icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M2 4v8l4-4 4 4 4-4v-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
            Gráficos
        </a>
        <a href="#">
            <svg class="sidebar-nav-icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <circle cx="8" cy="8" r="3" stroke="currentColor" stroke-width="1.5" />
                <path d="M8 1v3M8 12v3M1 8h3M12 8h3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            Manutenção
        </a>
    </nav>

    @if (!Route::is('map.*'))
        <div class="sidebar-sensors">
            <div class="sidebar-sensors-header">
                <span class="sidebar-sensors-title">Sensores ativos</span>
                <span class="sidebar-sensors-count">10</span>
            </div>
            <ul class="sensor-list" id="sensor-list" role="listbox" aria-label="Lista de sensores">
            </ul>
        </div>
    @endif

    <div class="sidebar-operator">
        <div class="operator-avatar" aria-hidden="true">CD</div>
        <div>
            <div class="operator-name">Carlos Drumond</div>
            <div class="operator-role">Defesa Civil — Plantão</div>
        </div>
    </div>
</aside>