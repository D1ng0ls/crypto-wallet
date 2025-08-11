document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-name]').forEach(el => {
        const name = el.getAttribute('data-name');
        const words = name.split(' ');
        let initials = '';
    
        for (let i = 0; i < words.length; i++) {
            initials += words[i].charAt(0).toUpperCase();
        }
    
        el.textContent = initials;
    });
});