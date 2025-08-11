@extends('components.templates.dashboard')

@php
    $totalValue = 0;
@endphp

@foreach ($user->wallets as $wallet)
    @php
        $totalValue += $wallet->balance * $wallet->coin->price;
    @endphp
@endforeach

@push('content')
<div class="wallet-section">
    <div class="balance-card">
        <div class="balance-title">Saldo Total Convertido</div>
        <div class="balance-amount">{{ number_format($totalValue, 2, ',', '.') }} $</div>
    </div>

    <div class="crypto-list">
        @forelse ($user->wallets as $wallet)
            <div class="crypto-item">
                <div class="crypto-info">
                    <div class="crypto-icon {{$wallet->coin->symbol}}">
                        <img src="{{asset('images/coins/'.$wallet->coin->image)}}" alt="{{$wallet->coin->name}}" style="width: 100%; height: 100%">
                    </div>
                    <div class="crypto-details">
                        <h3>{{$wallet->coin->name}}</h3>
                        <p>{{$wallet->balance}} {{$wallet->coin->symbol}}</p>
                    </div>
                </div>
                <div class="crypto-value">
                    <div class="crypto-price">{{$wallet->coin->price * $wallet->balance}} $</div>
                </div>
            </div>
        @empty
            <div style="color: #999; text-align: center;">
                <p>Sua carteira está vazia...</p>
                <p>Adicione sua primeira moeda para começar.</p>
                <button class="action-btn" style="margin-top: 20px;" id="comprarCrypto">Comprar Crypto</button>
            </div>
        @endforelse
    </div>
</div>

<div class="sidebar">
    <div class="quick-actions">
        <h3>Ações Rápidas</h3>
        <div class="action-buttons">
            <button class="action-btn" id="comprarCrypto">🔄 Comprar Crypto</button>
            <button class="action-btn secondary">📤 Enviar</button>
            <button class="action-btn secondary">📥 Sacar</button>
        </div>
    </div>

    <div class="market-trends">
        <h3>Tendências do Mercado</h3>
        @forelse ($coins as $coin)
            <div class="trend-item">
                <span>{{$coin->name}}</span>
                <span class="crypto-change positive">{{$coin->price}} $</span>
            </div>
        @empty
            <div class="trend-item" style="color: #999;">Nenhuma moeda encontrada...</div>
        @endforelse
    </div>
</div>

<div class="hidden" id="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <form method="POST" action="{{route('crypto.buy')}}">
            @csrf
            <div>
                <label for="coin">Moeda:</label>
                <select class="form-input" name="coin" id="coin">
                    @foreach ($coins as $coin)
                        <option value="{{$coin->id}}">{{$coin->name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="amount">Quantidade:</label>
                <input class="form-input" type="number" name="amount" id="amount">
            </div>
            <button class="action-btn" type="submit">Comprar</button>
        </form>
    </div>
</div>

<script>
    function numberFormat(number) {
        return number.toFixed(2).replace('.', ',');
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const actionButtons = document.querySelectorAll('#comprarCrypto');
        actionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const modal = document.getElementById('modal');
                modal.classList.add('modal');
                modal.classList.remove('hidden');
            });
        });

        const closeModal = document.getElementById('closeModal');
        closeModal.addEventListener('click', function() {
            const modal = document.getElementById('modal');
            modal.classList.add('hidden');
            modal.classList.remove('modal');
        });
    });
</script>
@endpush
