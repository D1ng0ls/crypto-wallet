{{-- <div class="profile-card">
    <div class="profile-avatar" id="initials" data-name="{{ auth()->user()->name }}">

    </div>
    <div class="profile-name">{{ auth()->user()->name }}</div>
    <div class="profile-email">{{ auth()->user()->email }}</div>
    <div class="verification-status">
        ✓ Verificado
    </div>
</div> --}}

<div class="profile-nav">
    <div class="nav-item active" onclick="showSection('personal')">
        <div class="nav-icon">👤</div>
        <span>Informações Pessoais</span>
    </div>
    <div class="nav-item" onclick="showSection('security')">
        <div class="nav-icon">🔒</div>
        <span>Segurança</span>
    </div>
    <div class="nav-item" onclick="showSection('activity')">
        <div class="nav-icon">📊</div>
        <span>Atividade</span>
    </div>
    <div class="nav-item" onclick="showSection('support')">
        <div class="nav-icon">💬</div>
        <span>Suporte</span>
    </div>
</div>

<script>
    function showSection(sectionId) {
            // Remove active de todas as seções e nav items
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });

            // Ativa a seção selecionada
            document.getElementById(sectionId).classList.add('active');
            event.target.closest('.nav-item').classList.add('active');
        }
</script>
