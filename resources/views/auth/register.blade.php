@extends('layouts.app')

@section('title', 'Criar Conta')

@section('content')
<div class="container-sm flex-center" style="min-height:70vh">
    <div class="card">
        <h1 class="card-title">Criar Conta</h1>

        {{--
            ALERTAS DE FEEDBACK
            Mensagens de sucesso/erro vindas do controller via session flash.
            {{ }} garante escape de HTML (proteção XSS).
        --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Corrija os erros abaixo:</strong>
                <ul style="margin-top:.5rem; padding-left:1.2rem">
                    @foreach ($errors->all() as $error)
                        {{-- {{ $error }} → XSS escapado --}}
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{--
            FORMULÁRIO COM PROTEÇÃO CSRF
            @csrf gera: <input type="hidden" name="_token" value="TOKEN_ÚNICO">
            O middleware VerifyCsrfToken valida este token em toda requisição POST.
            Sem ele, um site externo poderia enviar o formulário em nome do usuário.
        --}}
        <form method="POST" action="{{ route('register.store') }}" autocomplete="off" novalidate>
            @csrf

            <div class="form-group">
                <label for="name">Nome completo *</label>
                {{--
                    old('name') repopula o campo após erro de validação.
                    {{ old('name') }} escapa XSS — dados do usuário nunca são
                    inseridos no HTML sem escape.
                --}}
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                    required
                    minlength="3"
                    maxlength="100"
                    autocomplete="name"
                >
                @error('name')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">E-mail *</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                    required
                    maxlength="254"
                    autocomplete="email"
                >
                @error('email')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="password">Senha *</label>
                    {{--
                        autocomplete="new-password": instrui o navegador a NÃO
                        preencher automaticamente com senhas salvas (evita
                        preenchimento acidental em formulários de cadastro).
                        Sem value="{{ old('password') }}" — senha NUNCA é
                        repopulada no campo por segurança.
                    --}}
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                        required
                        minlength="8"
                        autocomplete="new-password"
                    >
                    @error('password')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmar senha *</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                    >
                </div>
            </div>

            <div class="alert" style="background:rgba(56,189,248,.08); border:1px solid rgba(56,189,248,.3); color:var(--color-muted); font-size:.82rem; margin-bottom:1.2rem">
                <strong style="color:var(--color-primary)">Política de senha:</strong>
                mínimo 8 caracteres, letras maiúsculas e minúsculas, números e símbolo (!@#$%...).
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="cpf">CPF (opcional)</label>
                    <input
                        type="text"
                        id="cpf"
                        name="cpf"
                        value="{{ old('cpf') }}"
                        placeholder="000.000.000-00"
                        maxlength="14"
                        autocomplete="off"
                    >
                    @error('cpf')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Telefone (opcional)</label>
                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="(00) 00000-0000"
                        maxlength="20"
                        autocomplete="tel"
                    >
                    @error('phone')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full mt-2">
                Criar conta
            </button>
        </form>

        <p class="text-center mt-2 text-muted">
            Já tem conta? <a href="{{ route('login') }}">Faça login</a>
        </p>
    </div>
</div>
@endsection
