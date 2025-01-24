// Variables globales
let coaches = [];

// Cargar coaches desde el servidor
async function loadCoaches() {
    try {
        const response = await fetch('/php/getCoaches.php');
        coaches = await response.json();
        renderCoaches(coaches);
    } catch (error) {
        console.error('Error al cargar los datos:', error);
    }
}

// Renderizar coaches en el DOM
function renderCoaches(coaches) {
    const coachList = document.getElementById('player-list');
    coachList.innerHTML = '';

    coaches.forEach(coach => {
        const card = `
        <div class="col-md-3 div-card">
                <a href="coach-details.php?id=${coach.id_coach}" class="card-link">
                    <div class="card-player mx-auto">
                        <div class="card-image">
                            <img src="${coach.imagen}" alt="${coach.nombre} ${coach.apellido}">
                            <div class="overlays">
                                <p>${coach.nombre.toUpperCase()} ${coach.apellido.toUpperCase()}</p>
                            </div>
                        </div>
                        <div class="card-details">
                            <p>${coach.nacionalidad}<br>Current Team: ${coach.equipo}</p>
                        </div>
                    </div>
                </a>
            </div>`;
        coachList.innerHTML += card;
    });

    
}
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

// Inicializar la página
loadCoaches();
