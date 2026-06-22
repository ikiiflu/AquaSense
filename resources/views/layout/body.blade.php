<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Monitoramento inteligente de drenagem urbana em tempo real">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="reading-interval" content="{{ (int) (\App\Models\Setting::get('intervalo_leitura_seg', 60)) }}">
    <meta name="refresh-interval" content="{{ (int) (\App\Models\Setting::get('intervalo_atualizacao_seg', 60)) }}">
    <title>@yield('title', 'AquaSense')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
    @stack('styles')
</head>

<body>
    <div class="app-shell">
        @include('layout.sidebar')
        <main class="app-main" id="app-main">
            <div class="mobile-topbar">
                <div class="mobile-topbar-row">
                    <button class="mobile-menu-btn" id="mobile-menu-btn" aria-label="Abrir menu">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                            <path d="M2 4h14M2 9h14M2 14h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <span class="mobile-topbar-brand">Aqua<span>Sense</span></span>
                </div>
                <div class="mobile-topbar-sync">
                    <span>Última sinc.:&nbsp;<strong style="color:var(--ink)">{{ isset($footerLastSync) && $footerLastSync ? $footerLastSync->diffForHumans() : '--' }}</strong></span>
                    <span>Próx.:&nbsp;<strong style="color:var(--flow)" id="mobile-refresh-countdown">--</strong></span>
                </div>
            </div>
            <div class="app-content">
                @yield('content')
            </div>
            @include('layout.footer')
        </main>
        <div class="sidebar-overlay" id="sidebar-overlay"></div>
    </div>
    <script src="{{ asset('js/app.js') }}?v={{ time() }}" defer></script>
    @stack('scripts')
</body>

</html>
