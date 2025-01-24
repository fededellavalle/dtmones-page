document.querySelector('.div-copy p').innerHTML = `&copy; ${new Date().getFullYear()} DTMones Basketball Agency. Todos los derechos reservados.`;

// Selecciona todas las tarjetas
const cards = document.querySelectorAll('.category-card');

// Asigna un evento de clic a cada tarjeta
cards.forEach(card => {
    card.addEventListener('click', () => {
        // Obtén el nombre de la clase adicional (imports, domestics, coaches)
        const pageName = [...card.classList].find(cls => cls !== 'category-card');
        
        // Redirige a la página correspondiente
        if (pageName) {
            window.location.href = `/pages/clients/${pageName}.html`; // Ejemplo: imports.html
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const loader = document.getElementById("loader");

    // Espera unos milisegundos para mayor efecto visual
    setTimeout(() => {
        loader.classList.add("hidden"); // Activa la clase que oculta el loader
    }, 1000); // Opcional: ajusta el tiempo si quieres que permanezca más tiempo visible

    // Remueve completamente el loader después de la transición
    loader.addEventListener("transitionend", () => {
        loader.style.display = "none"; // Elimina el loader del flujo del DOM
    });
});
