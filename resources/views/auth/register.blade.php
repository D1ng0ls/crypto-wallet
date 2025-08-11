@extends('components.templates.auth')

@section('title', 'CryptoWallet - Registro')

@push('content')
<p class="toggle-link">Já tem uma conta? <a href="{{ route('login') }}">Entrar</a></p>
<form id="registerForm" class="auth-form" action="{{ route('register') }}" method="POST">
    @csrf
    <div class="form-group">
        <label class="form-label">Nome Completo</label>
        <input type="text" class="form-input" name="name" placeholder="João Silva" required>
        @error('name')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" class="form-input" name="email" placeholder="seu@email.com" required>
        @error('email')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Senha</label>
        <input type="password" class="form-input" name="password" placeholder="Mínimo 8 caracteres" required minlength="8">
        @error('password')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Confirmar Senha</label>
        <input type="password" class="form-input" name="password_confirmation" placeholder="Digite a senha novamente" required>
        @error('password_confirmation')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit" class="submit-btn">Criar Minha Carteira</button>
</form>
@endpush

