// Vanilla JavaScript for Stok Lestari Shell

document.addEventListener('DOMContentLoaded', () => {
    // Password toggle
    document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
        const targetId = btn.getAttribute('data-toggle-password');
        const input = document.getElementById(targetId);
        const textEl = btn.querySelector('.password-toggle-text');

        if (!input) return;

        btn.addEventListener('click', () => {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
            btn.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            if (textEl) {
                textEl.textContent = isPassword ? 'Sembunyikan' : 'Tampilkan';
            }
        });
    });

    // Mobile Navigation Drawer
    const openBtn = document.getElementById('open-mobile-nav');
    const closeBtn = document.getElementById('close-mobile-nav');
    const drawer = document.getElementById('mobile-nav');
    const backdrop = document.getElementById('mobile-nav-backdrop');

    function openNav() {
        if (!drawer) return;
        drawer.classList.remove('hidden');
        drawer.setAttribute('aria-hidden', 'false');
        if (openBtn) openBtn.setAttribute('aria-expanded', 'true');
        document.body.classList.add('overflow-hidden');
    }

    function closeNav() {
        if (!drawer) return;
        drawer.classList.add('hidden');
        drawer.setAttribute('aria-hidden', 'true');
        if (openBtn) openBtn.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('overflow-hidden');
    }

    if (openBtn) openBtn.addEventListener('click', openNav);
    if (closeBtn) closeBtn.addEventListener('click', closeNav);
    if (backdrop) backdrop.addEventListener('click', closeNav);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawer && !drawer.classList.contains('hidden')) {
            closeNav();
        }
    });
});
