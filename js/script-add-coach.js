document.addEventListener('DOMContentLoaded', function () {
    $('.select2').select2({
        placeholder: "Seleccione una opción", // Texto de marcador
        allowClear: true // Habilitar opción para limpiar selección
    });
});

document.addEventListener('DOMContentLoaded', function () {
    // Referencias a los elementos del formulario y la previsualización
    const nombreInput = document.getElementById('nombre');
    const apellidoInput = document.getElementById('apellido');
    const nacionalidadSelect = document.getElementById('id_nacionalidad');
    const equipoSelect = document.getElementById('id_equipo');

    const imageInput = document.getElementById('imagen');
    const previewCard = document.getElementById('preview-card');
    const previewName = document.getElementById('preview-name');
    const previewDetails = document.getElementById('preview-details');
    const previewImage = document.getElementById('preview-image');

    // Función para actualizar la previsualización
    function updatePreview() {
        const nombre = nombreInput.value || 'NOMBRE';
        const apellido = apellidoInput.value || 'APELLIDO';
        const nacionalidad = nacionalidadSelect.selectedOptions[0]?.text || 'Nacionalidad';
        const equipo = equipoSelect.selectedOptions[0]?.text || 'Sin equipo asignado';
        previewName.textContent = `${nombre.toUpperCase()} ${apellido.toUpperCase()}`;
        previewDetails.innerHTML = `${nacionalidad}<br>${equipo}`;

        // Mostrar la tarjeta si hay contenido
        previewCard.style.display = 'block';
    }

    // Listener para actualizar imagen
    imageInput.addEventListener('change', function () {
        const file = imageInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImage.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            previewImage.src = '/assets/images/image-preview.png';
        }
        updatePreview();
    });

    // Listeners para campos de texto y select
    nombreInput.addEventListener('input', updatePreview);
    apellidoInput.addEventListener('input', updatePreview);
    nacionalidadSelect.addEventListener('change', updatePreview);
    equipoSelect.addEventListener('change', updatePreview);

    updatePreview();

    const guardarJugadorBtn = document.getElementById('guardarCoachBtn');
    guardarJugadorBtn.addEventListener('click', function (event) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se guardará la información del coach.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Simula el envío del formulario al presionar "Sí, guardar"
                guardarJugadorBtn.closest('form').submit();
            }
        });
    });

    // Botón Cancelar
    const cancelarBtn = document.getElementById('cancelarBtn');
    cancelarBtn.addEventListener('click', function (event) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se perderán los cambios realizados.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'Volver'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirige al dashboard si se confirma
                window.location.href = 'dashboard.php';
            }
        });
    });


    // Agregar Nacionalidad
    document.getElementById("btnAgregarNacionalidad").addEventListener("click", function () {
        Swal.fire({
            title: 'Agregar Nacionalidad',
            input: 'text',
            inputLabel: 'Nombre de la Nacionalidad',
            inputPlaceholder: 'Ingrese el nombre de la nacionalidad',
            showCancelButton: true,
            confirmButtonText: 'Agregar',
            cancelButtonText: 'Cancelar',
            preConfirm: (nombre) => {
                if (!nombre) {
                    Swal.showValidationMessage('Debe ingresar un nombre');
                }
                return nombre;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX para agregar nacionalidad
                fetch('adds/add-nationality.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nombre_nacionalidad: result.value })
                }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Éxito!', 'Nacionalidad agregada correctamente.', 'success').then(() => {
                                location.reload(); // Recargar la página para actualizar el select
                            });
                        } else {
                            Swal.fire('Error', 'Hubo un problema al agregar la nacionalidad.', 'error');
                        }
                    });
            }
        });
    });

    // Agregar Equipo
    document.getElementById("btnAgregarEquipo").addEventListener("click", function () {
        Swal.fire({
            title: 'Agregar Equipo',
            input: 'text',
            inputLabel: 'Nombre del Equipo',
            inputPlaceholder: 'Ingrese el nombre del equipo',
            showCancelButton: true,
            confirmButtonText: 'Agregar',
            cancelButtonText: 'Cancelar',
            preConfirm: (nombre) => {
                if (!nombre) {
                    Swal.showValidationMessage('Debe ingresar un nombre');
                }
                return nombre;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX para agregar equipo
                fetch('adds/add-team.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nombre_equipo: result.value })
                }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Éxito!', 'Equipo agregado correctamente.', 'success').then(() => {
                                location.reload(); // Recargar la página para actualizar el select
                            });
                        } else {
                            Swal.fire('Error', 'Hubo un problema al agregar el equipo.', 'error');
                        }
                    });
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    // Agregar nueva experiencia
    document.getElementById('addLinkBtn').addEventListener('click', function () {
        const container = document.getElementById('youtubeLinks');
        const inputGroup = document.createElement('div');
        inputGroup.className = 'input-group mb-3';
        inputGroup.innerHTML = `
    <input type="text" name="experiencias[]" class="form-control"
                                placeholder="Ingrese una experiencia del coach">
                            <button type="button" class="btn btn-danger btn-sm removeLinkBtn">Eliminar</button>
`;
        container.appendChild(inputGroup);

        // Agregar evento de eliminación al nuevo botón
        inputGroup.querySelector('.removeLinkBtn').addEventListener('click', handleRemoveLink);
    });

    // Manejar eliminación de links con confirmación
    document.querySelectorAll('.removeLinkBtn').forEach(button => {
        button.addEventListener('click', handleRemoveLink);
    });

    function handleRemoveLink(event) {
        const linkGroup = event.target.parentElement;

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se eliminará esta experiencia.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                linkGroup.remove();
                Swal.fire(
                    'Eliminado',
                    'El link ha sido eliminado.',
                    'success'
                );
            }
        });
    }
});