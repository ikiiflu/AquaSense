<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaSense — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            overflow: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: var(--void);
        }

        .login-wrapper {
            width: 100%;
            max-width: 400px;
            padding: 2rem 1rem;
        }

        .login-brand {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-brand-logo {
            width: 56px;
            height: 56px;
            object-fit: contain;
            margin-bottom: 0.75rem;
        }

        .login-brand-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: -0.02em;
        }

        .login-brand-name span {
            color: var(--flow);
        }

        .login-brand-tagline {
            font-size: 0.75rem;
            color: var(--ink-dim);
            margin-top: 0.2rem;
            letter-spacing: 0.04em;
        }

        .login-card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 2rem;
        }

        .login-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 1.5rem;
        }

        .login-field {
            margin-bottom: 1.25rem;
        }

        .login-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--ink-dim);
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .login-input {
            width: 100%;
            background: var(--void);
            border: 1px solid var(--line);
            color: var(--ink);
            padding: 0.65rem 0.875rem;
            border-radius: var(--radius-md);
            font-size: 0.9rem;
            font-family: var(--font-body);
            transition: border-color var(--transition-fast);
        }

        .login-input:focus {
            outline: none;
            border-color: var(--flow);
        }

        .login-input.is-error {
            border-color: var(--status-critico);
        }

        .login-error {
            font-size: 0.75rem;
            color: var(--status-critico);
            margin-top: 0.35rem;
        }

        .login-remember {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.82rem;
            color: var(--ink-dim);
            cursor: pointer;
        }

        .login-remember input[type="checkbox"] {
            accent-color: var(--flow);
            width: 15px;
            height: 15px;
        }

        .login-btn {
            width: 100%;
            padding: 0.7rem 1rem;
            background: var(--flow);
            color: var(--void);
            font-weight: 700;
            font-size: 0.9rem;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: opacity var(--transition-fast);
            font-family: var(--font-body);
        }

        .login-btn:hover {
            opacity: 0.88;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.72rem;
            color: var(--ink-muted);
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-brand">
            <img src="{{ asset('img/logo_transparente.png') }}" alt="AquaSense" class="login-brand-logo">
            <div class="login-brand-name">Aqua<span>Sense</span></div>
            <div class="login-brand-tagline">Monitorar. Antecipar. Agir.</div>
        </div>

        <div class="login-card">
            <div class="login-title">Acesso ao sistema</div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="login-field">
                    <label for="email" class="login-label">E-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        autofocus
                        class="login-input {{ $errors->has('email') ? 'is-error' : '' }}"
                        placeholder="seu@email.com">
                    @error('email')
                        <div class="login-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="login-field">
                    <label for="password" class="login-label">Senha</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        class="login-input {{ $errors->has('password') ? 'is-error' : '' }}"
                        placeholder="••••••••">
                    @error('password')
                        <div class="login-error">{{ $message }}</div>
                    @enderror
                </div>

                <label class="login-remember">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Manter conectado
                </label>

                <button type="submit" class="login-btn">Entrar</button>
            </form>
        </div>

        <div class="login-footer">
            AquaSense · Sistema de Monitoramento Urbano
        </div>
    </div>
</body>
</html>
