@extends('components.templates.auth')

@section('title', 'CryptoWallet - Registro')

@push('content')
<p class="toggle-link">Novo aqui? <a href="{{ route('register') }}">Registre-se</a></p>
<form id="loginForm" class="auth-form" action="{{ route('login') }}" method="POST">
    @csrf
    <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-input @error('email') error @enderror" placeholder="seu@email.com" required value="{{ old('email') }}">
        @error('email')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Senha</label>
        <input type="password" name="password" class="form-input @error('email') error @enderror" placeholder="••••••••" required>
    </div>

    <div class="checkbox-group">
        <label class="custom-checkbox">
            <input type="checkbox" name="remember" value="1">
            <span class="checkmark"></span>
            Lembrar de mim
        </label>
    </div>

    <button type="submit" class="submit-btn">Entrar na Carteira</button>

    <div class="toggle-link">
        Esqueceu sua senha? <a href="#">Recuperar</a>
    </div>
</form>
@endpush