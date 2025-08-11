@extends('components.templates.template')

@section('title', 'CryptoWallet - Dashboard')

@push('head')
    @vite('resources/css/dashboard.css')
@endpush

@push('body')
    <div class="container">
        @include('components.ui.header')

        <div class="main-content">
            @stack('content')
        </div>
    </div>
@endpush