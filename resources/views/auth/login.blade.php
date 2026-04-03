@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container-sm flex-center" style="min-height:70vh">
    <div class="card">
        <h1 class="card-title">Acessar Sistema</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif

        @if ($errors->has('email'))
            <div class="alert alert-danger">
                {{ $errors->first('email') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" autocomplete="off">
            @csrf

            <div class="form-group">
                <label for="email">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                    required
                    maxlength="254"
                    autocomplete="username"
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                {{--
                    autocomplete="current-password": instrui gerenciadores de
                    senhas a preencherem o campo de senha salva.
                    NUNCA repopular o campo senha com old().
                --}}
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                >
            </div>

            <button type="submit" class="btn btn-primary btn-full mt-2">
                Entrar
            </button>
        </form>

        <p class="text-center mt-2 text-muted">
            Não tem conta? <a href="{{ route('register') }}">Cadastre-se</a>
        </p>
    </div>
</div>
@endsection
