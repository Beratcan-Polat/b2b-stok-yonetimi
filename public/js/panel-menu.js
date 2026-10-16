const sidebar = document.querySelector('.yan-menu');
const menuButton = sidebar?.querySelector('.menu-dugmesi');
const menu = document.getElementById('panel-menu');

if (sidebar && menuButton && menu) {
    sidebar.classList.add('menu-hazir');

    menuButton.addEventListener('click', () => {
        const isOpen = menuButton.getAttribute('aria-expanded') === 'true';

        menuButton.setAttribute('aria-expanded', String(!isOpen));
        menu.classList.toggle('mobil-acik', !isOpen);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && menu.classList.contains('mobil-acik')) {
            menu.classList.remove('mobil-acik');
            menuButton.setAttribute('aria-expanded', 'false');
            menuButton.focus();
        }
    });
}
