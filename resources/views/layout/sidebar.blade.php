<aside class="app-sidebar" role="navigation" aria-label="Navegação principal">
    <div class="sidebar-brand">
        <img src="{{ asset('img/logo_transparente.png') }}" alt="AquaSense Logo" class="sidebar-brand-logo">
        <div class="sidebar-brand-text">
            <div class="sidebar-brand-name">Aqua<span>Sense</span></div>
            <div class="sidebar-brand-tagline">Monitorar. Antecipar. Agir.</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}"
           class="{{ request()->routeIs('dashboard') ? 'is-active' : '' }}"
           aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">
            <svg class="sidebar-nav-icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <rect x="1" y="1" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5" />
                <rect x="9" y="1" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5" />
                <rect x="1" y="9" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5" />
                <rect x="9" y="9" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5" />
            </svg>
            Dashboard
        </a>

        <a href="{{ route('map.operational_map') }}"
           class="{{ request()->routeIs('map.*') ? 'is-active' : '' }}"
           aria-current="{{ request()->routeIs('map.*') ? 'page' : 'false' }}">
            <svg class="sidebar-nav-icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M8 1v14M4 5l4-4 4 4M3 15h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            Mapa operacional
        </a>

        <a href="{{ route('alerts.index') }}"
           class="{{ request()->routeIs('alerts.*') ? 'is-active' : '' }}"
           aria-current="{{ request()->routeIs('alerts.*') ? 'page' : 'false' }}">
            <svg class="sidebar-nav-icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5" />
                <path d="M8 4v5M8 12v.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            Alertas
            @if(!empty($navAlertCount) && $navAlertCount > 0)
                <span class="sidebar-nav-badge">{{ $navAlertCount }}</span>
            @endif
        </a>

        <a href="{{ route('history.index') }}"
           class="{{ request()->routeIs('history.*') ? 'is-active' : '' }}"
           aria-current="{{ request()->routeIs('history.*') ? 'page' : 'false' }}">
            <svg class="sidebar-nav-icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M2 4h12M2 8h8M2 12h12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            Histórico
        </a>

        <a href="{{ route('charts.index') }}"
           class="{{ request()->routeIs('charts.*') ? 'is-active' : '' }}"
           aria-current="{{ request()->routeIs('charts.*') ? 'page' : 'false' }}">
            <svg class="sidebar-nav-icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M2 4v8l4-4 4 4 4-4v-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
            Gráficos
        </a>

        <a href="{{ route('comandos.index') }}"
           class="{{ request()->routeIs('comandos.*') ? 'is-active' : '' }}"
           aria-current="{{ request()->routeIs('comandos.*') ? 'page' : 'false' }}">
            <svg class="sidebar-nav-icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <rect x="1" y="3" width="14" height="10" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                <path d="M4 7l2 2-2 2M8 11h4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Últimos Comandos
        </a>

        <a href="{{ route('settings.index') }}"
           class="{{ request()->routeIs('settings.*') ? 'is-active' : '' }}"
           aria-current="{{ request()->routeIs('settings.*') ? 'page' : 'false' }}">
            <svg class="sidebar-nav-icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <circle cx="8" cy="8" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                <path d="M8 1.5v2M8 12.5v2M1.5 8h2M12.5 8h2M3.4 3.4l1.4 1.4M11.2 11.2l1.4 1.4M3.4 12.6l1.4-1.4M11.2 4.8l1.4-1.4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            Configurações
        </a>
    </nav>

    @if (!request()->routeIs('map.*'))
        <div class="sidebar-sensors">
            <div class="sidebar-sensors-header">
                <span class="sidebar-sensors-title">Sensores ativos</span>
                <span class="sidebar-sensors-count">{{ ($navSensors ?? collect())->count() }}</span>
            </div>
            <ul class="sensor-list" id="sensor-list" role="listbox" aria-label="Lista de sensores">
                @forelse($navSensors ?? [] as $sensor)
                    <li class="sensor-list-item js-sensor-item"
                        tabindex="0"
                        role="option"
                        aria-label="{{ $sensor->nome }} - {{ $sensor->endereco instanceof \App\Models\Endereco ? $sensor->endereco->logradouro : '' }}, status {{ $sensor->status }}"
                        data-lat="{{ $sensor->latitude }}"
                        data-lng="{{ $sensor->longitude }}"
                        data-sensor-id="{{ $sensor->id }}">
                        <div class="sensor-dot status-{{ $sensor->status }}" aria-hidden="true"></div>
                        <div class="sensor-info">
                            <div class="sensor-name">{{ $sensor->nome }}</div>
                            <div class="sensor-address">{{ $sensor->endereco instanceof \App\Models\Endereco ? $sensor->endereco->logradouro : '' }}</div>
                        </div>
                    </li>
                @empty
                    <li class="sensor-list-empty" style="padding:0.75rem 1rem;font-size:0.75rem;color:var(--ink-dim)">
                        Sem sensores cadastrados
                    </li>
                @endforelse
            </ul>
        </div>
    @endif

    <div class="sidebar-operator">
        @auth
        @php
            $authUser = auth()->user();
            $initials = collect(explode(' ', $authUser->name))
                ->take(2)
                ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                ->implode('');
        @endphp
        <div class="operator-avatar" aria-hidden="true">{{ $initials }}</div>
        <div style="flex:1;min-width:0">
            <div class="operator-name" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $authUser->name }}</div>
            <div class="operator-role" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $authUser->email }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="flex-shrink:0">
            @csrf
            <button type="submit" title="Sair"
                    style="background:none;border:none;cursor:pointer;padding:4px;color:var(--ink-muted);
                           display:flex;align-items:center;transition:color var(--transition-fast)"
                    onmouseover="this.style.color='var(--status-critico)'"
                    onmouseout="this.style.color='var(--ink-muted)'">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M6 2H3a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h3M11 11l3-3-3-3M14 8H6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </form>
        @endauth
    </div>
</aside>
