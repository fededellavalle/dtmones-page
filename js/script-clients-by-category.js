// Variables globales
let players = []; // Contiene todos los jugadores cargados inicialmente
let filteredPlayers = []; // Contiene los jugadores filtrados actualmente

// Cargar jugadores desde el servidor y almacenarlos localmente
async function loadPlayers(category) {
    try {
        const url = category ? `/php/getClientsByCategory.php?categoria=${category}` : '/getPlayers.php';
        const response = await fetch(url);
        players = await response.json(); // Guardar todos los jugadores en la variable global
        filteredPlayers = players; // Inicialmente, mostrar todos los jugadores
        renderPlayers(filteredPlayers); // Renderizar los jugadores
    } catch (error) {
        console.error('Error al cargar los datos:', error);
    }
}

// Renderizar jugadores en la interfaz
function renderPlayers(playerList) {
    const playerContainer = document.getElementById('player-list');
    playerContainer.innerHTML = ''; // Limpiar el contenedor antes de renderizar

    playerList.forEach(player => {
        const playerCard = `
            <div class="col-md-3 div-card">
                <a href="client-details.php?id=${player.id}" class="card-link">
                    <div class="card-player mx-auto">
                        <div class="card-category">${player.abreviacion_posicion}</div>
                        <div class="card-image">
                            <img src="${player.imagen}" alt="${player.jugador_nombre}">
                            <div class="overlays">
                                <p>${player.jugador_nombre.toUpperCase()} ${player.jugador_apellido.toUpperCase()}</p>
                            </div>
                        </div>
                        <div class="card-details">
                            <p>${player.nacionalidad}<br>Position: ${player.posicion}</p>
                        </div>
                    </div>
                </a>
            </div>
        `;
        playerContainer.innerHTML += playerCard;
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
// Filtrar jugadores por posición localmente
function filterPlayersByPosition(position) {
    if (!position) {
        // Si no se selecciona posición, mostrar todos los jugadores
        filteredPlayers = players;
    } else {
        // Filtrar jugadores por posición
        filteredPlayers = players.filter(player => player.abreviacion_posicion === position);
    }
    renderPlayers(filteredPlayers); // Renderizar los jugadores filtrados
}

// Manejar cambios en los botones de radio
document.querySelectorAll('input[name="position"]').forEach(radio => {
    radio.addEventListener('change', (event) => {
        const selectedPosition = event.target.value;
        filterPlayersByPosition(selectedPosition); // Filtrar localmente
    });
});


// Cargar jugadores al cargar la página
const pageCategory = document.title.toLowerCase(); // 'imports', 'domestics', 'coaches'
loadPlayers(pageCategory);


const toggleButton = document.getElementById('toggleFilters');
    const filterSection = document.getElementById('filterSection');
    const containerMain = document.getElementById('containerMain');

    toggleButton.addEventListener('click', () => {
        const sectionHeight = filterSection.scrollHeight; // Altura real del contenido

        if (filterSection.classList.contains('show')) {
            // Ocultar el filtro
            filterSection.style.opacity = '0';
            filterSection.style.transform = 'translateY(-20px)';
            containerMain.style.height = `58px`;
            setTimeout(() => {
                filterSection.classList.remove('show');
                containerMain.style.height = '58px'; // Altura original (ajústala según tu diseño)
            }, 500); // Duración de la transición
        } else {
            // Mostrar el filtro
            filterSection.style.display = 'block';
            containerMain.style.height = `${containerMain.scrollHeight}px`; // Mantener altura fija antes de expandir
            setTimeout(() => {
                filterSection.classList.add('show');
                filterSection.style.opacity = '1';
                filterSection.style.transform = 'translateY(0)';
                containerMain.style.height = `${sectionHeight + 58}px`; // Altura del contenedor con el filtro visible
            }, 10); // Retraso para activar la transición
        }
    });