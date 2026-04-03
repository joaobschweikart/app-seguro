@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
<div class="container-sm">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Corrija os erros:</strong>
            <ul style="margin-top:.5rem; padding-left:1.2rem">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulário de dados básicos --}}
    <div class="card">
        <h1 class="card-title">Editar Perfil</h1>

        <form method="POST" action="{{ route('profile.update') }}" novalidate>
            @csrf
            @method('PATCH')

            <div class="form-group">
                <label for="name">Nome completo</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                    required
                    minlength="3"
                    maxlength="100"
                >
                @error('name') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                    required
                    maxlength="254"
                >
                @error('email') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="phone">Telefone</label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    value="{{ old('phone', $user->phone) }}"
                    placeholder="(00) 00000-0000"
                    maxlength="20"
                >
                @error('phone') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-primary btn-full mt-2">
                Salvar dados
            </button>
        </form>
    </div>

    {{-- Seção de troca de senha --}}
    <div class="card mt-3">
        <h2 class="card-title" style="font-size:1.1rem">Alterar Senha</h2>

        <div class="alert" style="background:rgba(251,191,36,.08); border:1px solid rgba(251,191,36,.3); color:var(--color-muted); font-size:.82rem; margin-bottom:1.2rem">
            Para sua segurança, informe a senha atual antes de definir a nova.
        </div>

        {{--
            Formulário separado para troca de senha.
            Mesmo endpoint — o controller distingue pela presença de new_password.
        --}}
        <form method="POST" action="{{ route('profile.update') }}" novalidate>
            @csrf
            @method('PATCH')

            <input type="hidden" name="email" value="{{ $user->email }}">

            <div class="form-group">
                <label for="current_password">Senha atual</label>
                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    class="{{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                    autocomplete="current-password"
                >
                @error('current_password') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="new_password">Nova senha</label>
                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    class="{{ $errors->has('new_password') ? 'is-invalid' : '' }}"
                    autocomplete="new-password"
                    minlength="8"
                >
                @error('new_password') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="new_password_confirmation">Confirmar nova senha</label>
                <input
                    type="password"
                    id="new_password_confirmation"
                    name="new_password_confirmation"
                    autocomplete="new-password"
                >
            </div>

            <button type="submit" class="btn btn-danger btn-full mt-2">
                Alterar senha
            </button>
        </form>
    </div>

    <div class="mt-2">
        <a href="{{ route('dashboard') }}" class="btn btn-ghost">← Voltar ao Dashboard</a>
    </div>
</div>
@endsection
