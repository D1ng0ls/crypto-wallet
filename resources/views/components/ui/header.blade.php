

<header>
    <div class="logo"><a href="{{ route('dashboard') }}">🚀 CryptoWallet</a></div>
    <div class="user-profile-header">
        <span>{{ auth()->user()->name }}</span>
        <div class="profile-avatar-header" id="initials" data-name="{{ auth()->user()->name }}"></div>

        <div class="hidden" id="popupProfile">
            <ul>
                <li><a href="{{ route('profile') }}" id="profile" >Perfil <i class="fa-solid fa-user"></i></a></li>
                <li><a href="{{ route('logout') }}" id="logout" >Sair <i class="fa-solid fa-right-from-bracket"></i></a></li>
            </ul>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const popupProfile = document.getElementById('popupProfile');
        const profileAvatar = document.querySelector('.user-profile-header');

        profileAvatar.addEventListener('click', function() {
            popupProfile.classList.remove('hidden');
            popupProfile.classList.add('popup-profile');
        });

        document.addEventListener('click', function(event) {
            if (!popupProfile.contains(event.target) && !profileAvatar.contains(event.target)) {
                popupProfile.classList.add('hidden');
                popupProfile.classList.remove('popup-profile');
            }
        });
    });
</script>

@vite('resources/js/getInitials.js')