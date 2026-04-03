@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div style="margin-bottom:2rem">
        <h1 style="font-size:1.5rem; font-weight:700">
            Olá, {{ Auth::user()->name }}
        </h1>
        <p class="text-muted">
            Bem-vindo ao painel seguro. Sua sessão expira após
            {{ config('session.inactivity_timeout', 1800) / 60 }} minutos de inatividade.
        </p>
    </div>

    <div class="grid-2" style="margin-bottom:2rem">
        <div class="card">
            <div style="color:var(--color-muted); font-size:.8rem; margin-bottom:.5rem">E-MAIL</div>
            {{-- Dado sensível exibido com escape XSS --}}
            <div style="font-weight:500">{{ Auth::user()->email }}</div>
        </div>
        <div class="card">
            <div style="color:var(--color-muted); font-size:.8rem; margin-bottom:.5rem">MEMBRO DESDE</div>
            <div style="font-weight:500">
                {{ Auth::user()->created_at->format('d/m/Y') }}
            </div>
        </div>
        <div class="card">
            <div style="color:var(--color-muted); font-size:.8rem; margin-bottom:.5rem">TELEFONE</div>
            <div style="font-weight:500">

                {{ Auth::user()->phone ?? '—' }}
            </div>
        </div>
        <div class="card">
            <div style="color:var(--color-muted); font-size:.8rem; margin-bottom:.5rem">STATUS</div>
            <div>
                <span class="badge badge-success">● Ativo</span>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="card-title" style="font-size:1.1rem">📋 Atividades Recentes</h2>
        <p class="text-muted" style="margin-bottom:1rem; font-size:.85rem">
            Monitore acessos à sua conta. Se identificar atividade suspeita, altere sua senha imediatamente.
        </p>

        @if ($recentLogs->isEmpty())
            <p class="text-muted text-center" style="padding:1rem 0">Nenhuma atividade registrada.</p>
        @else
            <div style="overflow-x:auto">
                <table>
                    <thead>
                        <tr>
                            <th>Ação</th>
                            <th>IP</th>
                            <th>Data/Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentLogs as $log)
                        <tr>
                            <td>
                                @php
                                    // Mapeamento de ações para labels legíveis
                                    $labels = [
                                        'login_success'    => ['Login realizado',    'success'],
                                        'login_failed'     => ['Tentativa inválida',  'danger'],
                                        'logout'           => ['Logout',              'info'],
                                        'register'         => ['Cadastro',            'info'],
                                        'profile_updated'  => ['Perfil atualizado',   'info'],
                                        'password_changed' => ['Senha alterada',      'warning'],
                                        'session_expired'  => ['Sessão expirada',     'warning'],
                                    ];
                                    $label = $labels[$log->action] ?? [$log->action, 'info'];
                                @endphp
                                {{-- $label contém dados estáticos do array PHP — não dados do usuário, sem risco XSS --}}
                                <span class="badge badge-{{ $label[1] }}">{{ $label[0] }}</span>
                            </td>
                            <td>
                                {{-- IP do registro — escapado com {{ }} --}}
                                <code style="font-size:.8rem; color:var(--color-muted)">{{ $log->ip_address }}</code>
                            </td>
                            <td>
                                <span style="font-size:.85rem; color:var(--color-muted)">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="mt-3">
        <a href="{{ route('profile.edit') }}" class="btn btn-ghost">Editar perfil</a>
    </div>

</div>
@endsection
