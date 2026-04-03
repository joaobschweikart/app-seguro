<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'App Seguro') — Sistema Seguro</title>

    <style>
        /* Reset e base */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --color-bg:       #0f172a;
            --color-surface:  #1e293b;
            --color-border:   #334155;
            --color-primary:  #38bdf8;
            --color-danger:   #f87171;
            --color-success:  #4ade80;
            --color-warning:  #fbbf24;
            --color-text:     #e2e8f0;
            --color-muted:    #94a3b8;
            --radius:         8px;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--color-bg);
            color: var(--color-text);
            min-height: 100vh;
            line-height: 1.6;
        }

        /* Navbar */
        .navbar {
            background: var(--color-surface);
            border-bottom: 1px solid var(--color-border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-primary);
            text-decoration: none;
        }

        .navbar-brand span { color: var(--color-text); }

        .navbar-user { display: flex; align-items: center; gap: 1rem; }
        .navbar-user small { color: var(--color-muted); font-size: .8rem; }

        /* Container */
        .container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .container-sm {
            max-width: 480px;
            margin: 3rem auto;
            padding: 0 1.5rem;
        }

        /* Card */
        .card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            padding: 2rem;
        }

        .card-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--color-primary);
        }

        /* Formulário */
        .form-group { margin-bottom: 1.2rem; }

        label {
            display: block;
            font-size: .875rem;
            font-weight: 500;
            margin-bottom: .4rem;
            color: var(--color-muted);
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: .65rem 1rem;
            background: var(--color-bg);
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            color: var(--color-text);
            font-size: .95rem;
            transition: border-color .2s;
        }

        input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(56,189,248,.15);
        }

        input.is-invalid { border-color: var(--color-danger); }

        .error-text {
            color: var(--color-danger);
            font-size: .8rem;
            margin-top: .3rem;
        }

        /* Botões */
        .btn {
            display: inline-block;
            padding: .65rem 1.4rem;
            border: none;
            border-radius: var(--radius);
            font-size: .95rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: opacity .2s;
        }

        .btn:hover { opacity: .85; }
        .btn-primary { background: var(--color-primary); color: #0f172a; }
        .btn-danger  { background: var(--color-danger);  color: #fff; }
        .btn-ghost   { background: transparent; border: 1px solid var(--color-border); color: var(--color-text); }
        .btn-full    { width: 100%; text-align: center; }

        /* Alertas */
        .alert {
            padding: .85rem 1rem;
            border-radius: var(--radius);
            margin-bottom: 1.2rem;
            font-size: .9rem;
        }

        .alert-success { background: rgba(74,222,128,.1); border: 1px solid var(--color-success); color: var(--color-success); }
        .alert-danger  { background: rgba(248,113,113,.1); border: 1px solid var(--color-danger);  color: var(--color-danger);  }
        .alert-warning { background: rgba(251,191,36,.1);  border: 1px solid var(--color-warning); color: var(--color-warning); }

        /* Links */
        a { color: var(--color-primary); text-decoration: none; }
        a:hover { text-decoration: underline; }

        /* Utilitários */
        .text-center { text-align: center; }
        .mt-1 { margin-top: .5rem; }
        .mt-2 { margin-top: 1rem; }
        .mt-3 { margin-top: 1.5rem; }
        .text-muted { color: var(--color-muted); font-size: .85rem; }

        /* Badge */
        .badge {
            display: inline-block;
            padding: .2rem .6rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
        }

        .badge-success { background: rgba(74,222,128,.15); color: var(--color-success); }
        .badge-danger  { background: rgba(248,113,113,.15); color: var(--color-danger);  }
        .badge-info    { background: rgba(56,189,248,.15);  color: var(--color-primary); }

        /* Tabela de logs */
        table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        th, td { padding: .6rem .8rem; text-align: left; border-bottom: 1px solid var(--color-border); }
        th { color: var(--color-muted); font-weight: 500; }
        tr:last-child td { border-bottom: none; }

        /* Grid 2 colunas */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

        @media (max-width: 600px) { .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

{{-- Navbar — só exibe quando autenticado --}}
@auth
<nav class="navbar">
    <a href="{{ route('dashboard') }}" class="navbar-brand"><span>App Seguro</span></a>
    <div class="navbar-user">

        <small>{{ Auth::user()->name }}</small>
        <a href="{{ route('profile.edit') }}" class="btn btn-ghost" style="padding:.4rem .8rem; font-size:.85rem">
            Perfil
        </a>
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button type="submit" class="btn btn-danger" style="padding:.4rem .8rem; font-size:.85rem">
                Sair
            </button>
        </form>
    </div>
</nav>
@endauth

@yield('content')

</body>
</html>
