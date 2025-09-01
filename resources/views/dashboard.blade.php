@extends('components.templates.dashboard')

@php
    $totalValue = 0;

    function getFearAndGreedStatus(int $index)
    {
        if ($index <= 25) {
            return 'Medo Extremo';
        } elseif ($index <= 45) {
            return 'Medo';
        } elseif ($index <= 54) {
            return 'Neutro';
        } elseif ($index <= 74) {
            return 'Ganância';
        } else { // 75 a 100
            return 'Ganância Extrema';
        }
    }
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
            <button class="action-btn secondary" id="enviarCrypto">📤 Enviar</button>
            <button class="action-btn secondary" id="sacarCrypto">📥 Sacar</button>
        </div>
    </div>

    <div class="moody-index">
        <h3>Índice de Medo e Ganância</h3>
        <div class="moody-index-chart">
            <svg width="144" height="78" viewBox="0 0 144 78">
                <path d="M 13 67.99999999999999 A 59 59 0 0 1 20.699799159192082 38.85742987153037" stroke="#EA3943" stroke-width="6" stroke-linecap="round" fill="none"></path>
                <path d="M 25.25491104204376 32.001435329825206 A 59 59 0 0 1 49.136580399325936 13.610074056278464" stroke="#EA8C00" stroke-width="6" stroke-linecap="round" fill="none"></path>
                <path d="M 56.928700281788366 10.957420072336895 A 59 59 0 0 1 87.07129971821165 10.957420072336895" stroke="#F3D42F" stroke-width="6" stroke-linecap="round" fill="none"></path>
                <path d="M 94.86341960067408 13.61007405627847 A 59 59 0 0 1 118.74508895795626 32.00143532982522" stroke="#93D900" stroke-width="6" stroke-linecap="round" fill="none"></path>
                <path d="M 123.30020084080792 38.85742987153038 A 59 59 0 0 1 131 68" stroke="#16C784" stroke-width="6" stroke-linecap="round" fill="none"></path>
                <path d="M 13 67.99999999999999 A 59 59 0 0 1 20.699799159192082 38.85742987153037" stroke="none" stroke-width="6" stroke-linecap="round" fill="none"></path>
                <path d="M 25.25491104204376 32.001435329825206 A 59 59 0 0 1 49.136580399325936 13.610074056278464" stroke="none" stroke-width="6" stroke-linecap="round" fill="none"></path>
                <circle cx="52.014462705527805" cy="12.4880346317007" r="5" fill="white"></circle>
            </svg>
            <div class="moody-index-value">
                <span class="moody-index-number">{{ $fearAndGreedIndex }}</span>
                <span class="moody-index-text">{{ getFearAndGreedStatus($fearAndGreedIndex) }}</span>
            </div>
        </div>
    </div>
    <div class="market-trends">
        <h3>Criptos sugeridas</h3>
        @forelse ($recommendedCoins as $coin)
            <div class="trend-item">
                <span>{{$coin->name}} - {{$coin->risk}}</span>
                <span class="crypto-change positive">{{$coin->price}} $</span>
            </div>
        @empty
            <div class="trend-item" style="color: #999;">Nenhuma moeda foi encontrada para seu perfil...</div>
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

<div class="hidden" id="modalTransfer">
    <div class="modal-content">
        <span class="close" id="closeModalTransfer">&times;</span>
        <form method="POST" action="{{route('crypto.send')}}">
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
            <div>
                <label for="account">Conta de Destino:</label>
                <input class="form-input" type="text" name="account" id="account">
            </div>
            <button class="action-btn" type="submit">Transferir</button>
        </form>
    </div>
</div>

<div class="hidden" id="modalSaque">
    <div class="modal-content">
        <span class="close" id="closeModalSaque">&times;</span>
        <form method="POST" action="{{route('crypto.withdraw')}}">
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
            <button class="action-btn" type="submit">Sacar</button>
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

    document.addEventListener('DOMContentLoaded', function() {
        const actionButtons = document.querySelectorAll('#enviarCrypto');
        actionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const modal = document.getElementById('modalTransfer');
                modal.classList.add('modal');
                modal.classList.remove('hidden');
            });
        });

        const closeModalTransfer = document.getElementById('closeModalTransfer');
        closeModalTransfer.addEventListener('click', function() {
            const modalTransfer = document.getElementById('modalTransfer');
            modalTransfer.classList.add('hidden');
            modalTransfer.classList.remove('modal');
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const actionButtons = document.querySelectorAll('#sacarCrypto');
        actionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const modal = document.getElementById('modalSaque');
                modal.classList.add('modal');
                modal.classList.remove('hidden');
            });
        });

        const closeModalTransfer = document.getElementById('closeModalSaque');
        closeModalTransfer.addEventListener('click', function() {
            const modalTransfer = document.getElementById('modalSaque');
            modalTransfer.classList.add('hidden');
            modalTransfer.classList.remove('modal');
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
    function getCirclePosition(value) {
        const cx = 72;
        const cy = 68;
        const r = 59;

        const angle = 180 - (value * 180 / 100);
        const rad = angle * Math.PI / 180;

        const x = cx + r * Math.cos(rad);
        const y = cy - r * Math.sin(rad);

        const circle = document.querySelector('.moody-index-chart circle');
        circle.setAttribute('cx', x);
        circle.setAttribute('cy', y);
    }

    getCirclePosition({{ $fearAndGreedIndex }});
});
</script>
@endpush
