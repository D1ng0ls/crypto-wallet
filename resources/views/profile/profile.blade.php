@extends('components.templates.profile')

@section('title', 'CryptoWallet - Perfil')

@push('head')
    @vite('resources/css/profile.css')
@endpush

@php
    $profiles = [
        'aggressive' => 'Agressivo',
        'moderate' => 'Moderado',
        'conservative' => 'Conservador',
    ];

    $types = [
        'buy' => 'Depósito',
        'withdraw' => 'Saque',
        'transfer' => 'Transferência',
    ];
@endphp

@push('content')
<div id="personal" class="content-section active">
    <h2 class="section-title">Informações Pessoais</h2>
    
    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label class="form-label">Endereço da carteira</label>
            <div style="display: flex; align-items: center; gap: 10px;">
                <input type="text" class="form-input" id="walletAddress" disabled readonly value="{{ auth()->user()->account }}">
                <button type="button" class="copy-btn" onclick="copyToClipboard()"><i id="copyIcon" class="fa-solid fa-copy"></i></button>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Perfil de investimento</label>
            <div class="profile-selector">
                @foreach ($profiles as $value => $label)
                    <input
                        type="radio"
                        name="investment_profile"
                        id="profile-{{ $value }}"
                        value="{{ $value }}"
                        class="profile-radio-input"
                        @checked(auth()->user()->investment_profile === $value)
                    >
                    <label for="profile-{{ $value }}" class="profile-radio-label">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Nome</label>
            <input type="text" name="name" class="form-input" value="{{ auth()->user()->name }}">
        </div>

        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" value="{{ auth()->user()->email }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    </form>
</div>
<div id="security" class="content-section hidden">
    <h2 class="section-title">Segurança</h2>
    
    <form action="{{ route('profile.security') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label class="form-label">Senha Atual</label>
            <input type="password" class="form-input" name="current_password" required>
        </div>

        <div class="form-group">
            <label class="form-label">Nova Senha</label>
            <input type="password" class="form-input" name="new_password" required>
        </div>

        <div class="form-group">
            <label class="form-label">Confirmar Nova Senha</label>
            <input type="password" class="form-input" name="confirm_password" required>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    </form>
</div>
<div id="notifications" class="content-section hidden">
    <h2 class="section-title">Notificações</h2>
    
    <div class="notifications-grid">
        @forelse (auth()->user()->notifications as $notification)
            <div class="notification-card">
                <div class="notification-icon">🔔</div>
                <div class="notification-content">
                    <p style="font-weight: bold;">{{ $types[$notification->data['type']] }}</p>
                    <p style="font-size: 14px;">{{ $notification->data['message'] }}</p>
                </div>
                <div class="notification-timestamp">{{ $notification->created_at->format('d/m/Y H:i') }}</div>
            </div>
        @empty
            <p style="opacity: 0.7; text-align: center;">Nenhuma notificação recente.</p>
        @endforelse
    </div>
</div>
<div id="activity" class="content-section">
    <h2 class="section-title">Estatísticas da Conta</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ auth()->user()->senderTransactions()->count() + auth()->user()->receiverTransactions()->count() }}</div>
            <div class="stat-label">Transações</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ floor(auth()->user()->created_at->diffInMonths(now())) }}</div>
            <div class="stat-label">Meses Ativo</div>
        </div>
    </div>

    <h2 class="section-title">Atividade Recente</h2>
    <div style="background: rgba(255, 255, 255, 0.03); border-radius: 12px; padding: 20px;">
        @php 
            $senderTransactions = auth()->user()->senderTransactions()->get();

            $receiverTransactions = auth()->user()->receiverTransactions()->get();

            $transactions = $senderTransactions->merge($receiverTransactions)->sortBy('created_at')
        @endphp
        @forelse ($transactions as $transaction)
            <div class="transaction-card">
                <div class="transaction-info">
                    <div class="transaction-type">{{ $transaction->type }}</div>
                    <div class="transaction-amount">{{ $transaction->amount }}</div>
                </div>
                <div class="transaction-details">
                    <div class="transaction-coin">{{ $transaction->coin->name }}</div>
                    <div class="transaction-timestamp">{{ $transaction->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        @empty
            <p style="opacity: 0.7; text-align: center;">Nenhuma transação recente.</p>
        @endforelse
    </div>
</div>
<div id="support" class="content-section">
    <h2 class="section-title">Central de Ajuda</h2>
    
    <div class="verification-grid">
        <div class="verification-card">
            <div class="verification-icon">❓</div>
            <h3>FAQ</h3>
            <p>Perguntas frequentes e respostas</p>
            <button class="btn btn-secondary">Acessar</button>
        </div>

        <div class="verification-card">
            <div class="verification-icon">💬</div>
            <h3>Chat ao Vivo</h3>
            <p>Converse com nosso suporte</p>
            <button class="btn btn-primary">Iniciar Chat</button>
        </div>

        <div class="verification-card">
            <div class="verification-icon">📧</div>
            <h3>Email</h3>
            <p>Envie sua dúvida por email</p>
            <button class="btn btn-secondary">Contatar</button>
        </div>

        <div class="verification-card">
            <div class="verification-icon">📞</div>
            <h3>Telefone</h3>
            <p>Atendimento telefônico</p>
            <button class="btn btn-secondary">Ligar</button>
        </div>
    </div>

    <div style="margin-top: 30px; text-align: center;">
        <button class="btn btn-danger">Desativar Conta</button>
    </div>
</div>
<script>
    function copyToClipboard() {
        const text = document.querySelector('#walletAddress').value;
        navigator.clipboard.writeText(text);

        const copyBtn = document.querySelector('.copy-btn');
        const copyIcon = document.querySelector('#copyIcon');
        copyBtn.classList.add('copied');
        copyIcon.classList.remove('fa-copy');
        copyIcon.classList.add('fa-check');
        setTimeout(() => {
            copyBtn.classList.remove('copied');
            copyIcon.classList.remove('fa-check');
            copyIcon.classList.add('fa-copy');
        }, 2000);
    }
</script>
@endpush