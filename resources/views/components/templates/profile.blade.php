@extends('components.templates.template')

@section('title', 'CryptoWallet - Perfil')

@push('head')
    @vite('resources/css/profile.css')
@endpush

@push('body')
    <div class="container">
        @include('components.ui.header')

        <div class="main-content">
            <div class="profile-layout">
                <div class="profile-sidebar">
                    @include('components.ui.navbarprofile')
                </div>
    
                <div class="profile-content">
                    @stack('content')
                </div>
            </div>
        </div>
    </div>
@endpush