
    document.getElementById('searchInput').addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        const players = document.querySelectorAll('.player-item');

        players.forEach(player => {
            const name = player.querySelector('.player-name').textContent.toLowerCase();
            if (name.includes(filter)) {
                player.style.display = 'block';
            } else {
                player.style.display = 'none';
            }
        });
    });

// Función para confirmar la edición
function confirmEdit(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Vas a editar este cliente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, editar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirigir a la página de edición
            window.location.href = `edit-client.php?id=${id}`;
        }
    });
}


function confirmEditCoach(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Vas a editar este cliente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, editar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirigir a la página de edición
            window.location.href = `edit-coach.php?id=${id}`;
        }
    });
}

// Función para confirmar la eliminación
function confirmDelete(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, borrar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirigir a la página de eliminación
            window.location.href = `delete/delete-client.php?id=${id}`;
        }
    });
}

function confirmDeleteCoach(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, borrar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirigir a la página de eliminación
            window.location.href = `delete/delete-coach.php?id=${id}`;
        }
    });
}

function showPlayerInfo(playerId, tipo) {
    console.log(tipo);
    // Realiza una solicitud AJAX para obtener la información del jugador o coach
    $.ajax({
        url: '/php/clients-details-dashboard.php', // Archivo PHP que devolverá los detalles del jugador/entrenador
        method: 'GET',
        data: { id: playerId, tipo: tipo }, // Enviar tanto el ID como el tipo (player/coach)
        success: function (data) {
            // Rellenar el modal con los datos
            $('#playerDetails').html(data);
            // Mostrar el modal
            $('#playerInfoModal').modal('show');
        },
        error: function () {
            alert('Error al obtener la información.');
        }
    });
}

