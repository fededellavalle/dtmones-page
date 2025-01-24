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
                <a href="clients/client-details.php?id=${player.id}" class="card-link">
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

// Cargar jugadores al cargar la página
const pageCategory = document.title.toLowerCase(); // 'imports', 'domestics', 'coaches'
loadPlayers(pageCategory);