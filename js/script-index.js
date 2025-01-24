document.querySelector('.div-copy p').innerHTML = `&copy; ${new Date().getFullYear()} DTMones Basketball Agency. Todos los derechos reservados.`;

document.addEventListener("DOMContentLoaded", function () {
    const url = "/php/getClientes.php";

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const carouselContent = document.getElementById("carousel-content");
            let html = "";
            let activeClass = "active";

            // Agrupar jugadores en filas de 3
            for (let i = 0; i < data.length; i += 3) {
                html += `<div class="carousel-item ${activeClass}">
                            <div class="row justify-content-center">`;

                // Crear las tarjetas para cada jugador
                for (let j = i; j < i + 3 && j < data.length; j++) {
                    if(data[j].tipo === 'Jugador'){
                    html += `
                        <div class="col-md-4">
                            <a href="/pages/clients/client-details.php?id=${data[j].id}" class="card-link">
                                <div class="card-player mx-auto">
                                    <div class="card-image">
                                        <img src="${data[j].imagen}" alt="${data[j].nombre}">
                                        <div class="overlays">
                                            <p>${data[j].nombre.toUpperCase()} ${data[j].apellido.toUpperCase()}</p>
                                        </div>
                                    </div>
                                    <div class="card-details">
                                        <p>${data[j].nacionalidad}<br>Current team: ${data[j].equipo}<br>${data[j].tipo}</p>
                                    </div>
                                </div>
                            </a>
                        </div>`;
                    } else{
                        html += `
                        <div class="col-md-4">
                            <a href="/pages/clients/coach-details.php?id=${data[j].id}" class="card-link">
                                <div class="card-player mx-auto">
                                    <div class="card-image">
                                        <img src="${data[j].imagen}" alt="${data[j].nombre}">
                                        <div class="overlays">
                                            <p>${data[j].nombre.toUpperCase()} ${data[j].apellido.toUpperCase()}</p>
                                        </div>
                                    </div>
                                    <div class="card-details">
                                        <p>${data[j].nacionalidad}<br>Current team: ${data[j].equipo}<br>${data[j].tipo}</p>
                                    </div>
                                </div>
                            </a>
                        </div>`;
                    }
                }

                html += `</div></div>`;
                activeClass = ""; // Solo el primer elemento tiene la clase 'active'
                
            }

            carouselContent.innerHTML = html;
        })
        .catch(error => console.error("Error al cargar jugadores:", error));
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


