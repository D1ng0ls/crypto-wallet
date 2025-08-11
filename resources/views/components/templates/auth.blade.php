@extends('components.templates.template')

@push('head')
    @vite('resources/css/auth.css')
@endpush

@push('body')
    <div class="floating-elements">
        <div class="floating-circle circle-1"></div>
        <div class="floating-circle circle-2"></div>
        <div class="floating-circle circle-3"></div>
        <div class="floating-circle circle-4"></div>
    </div>

    <div class="auth-container">
        <div class="logo-section">
            <div class="logo">🚀 CryptoWallet</div>
        </div>

        <div class="form-container">
            @stack('content')
        </div>
    </div>
@endpush
