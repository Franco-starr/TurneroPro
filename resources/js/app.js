const menuToggle = document.getElementById('menu-toggle');
const mobileMenu = document.getElementById('mobile-menu');

if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', () => {
        const isCollapsed = mobileMenu.classList.toggle('hidden');

        menuToggle.setAttribute('aria-expanded', String(isCollapsed ? false : true));
    });

    mobileMenu.addEventListener('click', (event) => {
        if (event.target.closest('a, button, form')) {
            mobileMenu.classList.add('hidden');
            menuToggle.setAttribute('aria-expanded', 'false');
        }
    });
}