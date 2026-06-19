<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Monitoramento inteligente de drenagem urbana em tempo real">
    <title>@yield('title', 'Drena Aí')</title>
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
            <div class="app-content">
                @yield('content')
            </div>
            @include('layout.footer')
        </main>
    </div>
    <script src="{{ asset('js/app.js') }}?v={{ time() }}" defer></script>
    @stack('scripts')
</body>

</html>