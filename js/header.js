
    const menuIcon = document.querySelector('.menu-icon');
    const sideMenu = document.querySelector('.side-menu');
    const overlay = document.querySelector('.overlay');
    const closeMenuButton = document.querySelector('.close-menu');

    // Función para abrir el menú
    const openMenu = () => {
        sideMenu.classList.add('open');
        overlay.classList.add('active');
    };

    // Función para cerrar el menú
    const closeMenu = () => {
        sideMenu.classList.remove('open');
        overlay.classList.remove('active');
    };

    // Evento para abrir el menú al hacer clic en el ícono
    menuIcon.addEventListener('click', openMenu);

    // Evento para cerrar el menú al hacer clic en el overlay o el botón de cerrar
    overlay.addEventListener('click', closeMenu);
    closeMenuButton.addEventListener('click', closeMenu);

    // Cerrar el menú al hacer clic en un enlace
    sideMenu.addEventListener('click', (e) => {
        if (e.target.tagName === 'A') {
            closeMenu();
        }
    });



