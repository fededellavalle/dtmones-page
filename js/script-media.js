document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById("iframe-container");

    // Función para cargar publicaciones
    function cargarPublicaciones() {
        fetch("/php/getPublicaciones.php")
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    const publicaciones = data.data;
                    publicaciones.forEach(publicacion => {
                        // Crear iframe para cada publicación
                        const iframe = document.createElement("iframe");
                        iframe.width = "320";
                        iframe.height = "440";
                        iframe.src = publicacion.link_embed;
                        iframe.frameBorder = "0";

                        // Agregar el iframe al contenedor
                        container.appendChild(iframe);
                    });
                } else {
                    container.innerHTML = `<p>${data.message}</p>`;
                }
            })
            .catch(error => {
                console.error("Error al cargar las publicaciones:", error);
                container.innerHTML = "<p>Error al cargar las publicaciones.</p>";
            });
    }

    // Llamar a la función para cargar las publicaciones
    cargarPublicaciones();

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
