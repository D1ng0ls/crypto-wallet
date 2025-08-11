<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CryptoWallet')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" />
    @vite('resources/css/default.css')
    @stack('head')
</head>
<body>
    <div class="floating-elements">
        <div class="floating-circle circle-1"></div>
        <div class="floating-circle circle-2"></div>
        <div class="floating-circle circle-3"></div>
    </div>

    @stack('body')
</body>
@if (session('success') || session('error') || $errors->any())
    <div class="alert alert-{{ session('success') ? 'success' : 'error' }}">
        @if (session('success'))
            <i class="fa-solid fa-check"></i>
            {{ session('success') }}
        @elseif (session('error'))
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ session('error') }}
        @elseif ($errors->any())
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $errors->first() }}
        @endif
    </div>
@endif

<script>
    const alertElement = document.querySelector('.alert');

    if (alertElement) {
        const visibleTime = 3000;

        const fadeOutTime = 500;

        setTimeout(() => {
            alertElement.classList.add('alert-fade-out');

            setTimeout(() => {
                alertElement.remove();
            }, fadeOutTime);

        }, visibleTime);
    }
</script>
</html>