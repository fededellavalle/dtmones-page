document.addEventListener('DOMContentLoaded', function () {
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

    // Agregar Certificado
document.getElementById("btnAgregarCertificado").addEventListener("click", function () {
    Swal.fire({
        title: 'Agregar Certificado',
        input: 'text',
        inputLabel: 'Nombre del Certificado',
        inputPlaceholder: 'Ingrese el nombre del certificado',
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
            // AJAX para agregar certificado
            fetch('adds/add-certificado.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nombre_certificado: result.value })
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Éxito!', 'Certificado agregado correctamente.', 'success').then(() => {
                            location.reload(); // Recargar la página para actualizar la tabla
                        });
                    } else {
                        Swal.fire('Error', 'Hubo un problema al agregar el certificado.', 'error');
                    }
                });
        }
    });
});

// Editar Nacionalidad
document.querySelectorAll(".btn-editar-nacionalidad").forEach(button => {
    button.addEventListener("click", function () {
        let id = this.closest('tr').querySelector('td').innerText;
        console.log(id);
        Swal.fire({
            title: 'Editar Nacionalidad',
            input: 'text',
            inputLabel: 'Nombre de la Nacionalidad',
            inputPlaceholder: 'Ingrese el nombre de la nacionalidad',
            inputValue: this.closest('tr').querySelector('td:nth-child(2)').innerText,
            showCancelButton: true,
            confirmButtonText: 'Actualizar',
            cancelButtonText: 'Cancelar',
            preConfirm: (nombre) => {
                if (!nombre) {
                    Swal.showValidationMessage('Debe ingresar un nombre');
                }
                return nombre;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX para actualizar la nacionalidad
                fetch('update/update-nationality.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_nacionalidad: id, nombre_nacionalidad: result.value })
                }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Éxito!', 'Nacionalidad actualizada correctamente.', 'success').then(() => {
                                location.reload(); // Recargar la página para ver los cambios
                            });
                        } else {
                            Swal.fire('Error', 'Hubo un problema al actualizar la nacionalidad.', 'error');
                        }
                    });
            }
        });
    });
});

// Borrar Nacionalidad
document.querySelectorAll(".btn-borrar-nacionalidad").forEach(button => {
    button.addEventListener("click", function () {
        let id = this.closest('tr').querySelector('td').innerText;
        console.log(id);
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás recuperar esta nacionalidad después de eliminarla.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX para eliminar la nacionalidad
                fetch('delete/delete-nationality.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_nacionalidad: id })
                }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Eliminado!', 'La nacionalidad ha sido eliminada.', 'success').then(() => {
                                location.reload(); // Recargar la página para actualizar la tabla
                            });
                        } else {
                            Swal.fire('Error', 'Hubo un problema al eliminar la nacionalidad.', 'error');
                        }
                    });
            }
        });
    });
});

// Editar Equipo
document.querySelectorAll(".btn-editar-equipo").forEach(button => {
    button.addEventListener("click", function () {
        let id = this.closest('tr').querySelector('td').innerText;
        console.log(id);
        Swal.fire({
            title: 'Editar Equipo',
            input: 'text',
            inputLabel: 'Nombre del Equipo',
            inputPlaceholder: 'Ingrese el nombre del equipo',
            inputValue: this.closest('tr').querySelector('td:nth-child(2)').innerText,
            showCancelButton: true,
            confirmButtonText: 'Actualizar',
            cancelButtonText: 'Cancelar',
            preConfirm: (nombre) => {
                if (!nombre) {
                    Swal.showValidationMessage('Debe ingresar un nombre');
                }
                return nombre;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX para actualizar el equipo
                fetch('update/update-team.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_equipo: id, nombre_equipo: result.value })
                }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Éxito!', 'Equipo actualizado correctamente.', 'success').then(() => {
                                location.reload(); // Recargar la página para ver los cambios
                            });
                        } else {
                            Swal.fire('Error', 'Hubo un problema al actualizar el equipo.', 'error');
                        }
                    });
            }
        });
    });
});

// Borrar Equipo
document.querySelectorAll(".btn-borrar-equipo").forEach(button => {
    button.addEventListener("click", function () {
        let id = this.closest('tr').querySelector('td').innerText;
        console.log(id);
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás recuperar este equipo después de eliminarla.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX para eliminar la equipo
                fetch('delete/delete-team.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_equipo: id })
                }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Eliminado!', 'El equipo ha sido eliminado.', 'success').then(() => {
                                location.reload(); // Recargar la página para actualizar la tabla
                            });
                        } else {
                            Swal.fire('Error', 'Hubo un problema al eliminar el equipo.', 'error');
                        }
                    });
            }
        });
    });
});

// Editar Certificado
document.querySelectorAll(".btn-editar-certificado").forEach(button => {
    button.addEventListener("click", function () {
        let id = this.closest('tr').querySelector('td').innerText;
        console.log(id);
        Swal.fire({
            title: 'Editar Certificado',
            input: 'text',
            inputLabel: 'Nombre del Certificado',
            inputPlaceholder: 'Ingrese el nombre del Certificado',
            inputValue: this.closest('tr').querySelector('td:nth-child(2)').innerText,
            showCancelButton: true,
            confirmButtonText: 'Actualizar',
            cancelButtonText: 'Cancelar',
            preConfirm: (nombre) => {
                if (!nombre) {
                    Swal.showValidationMessage('Debe ingresar un nombre');
                }
                return nombre;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX para actualizar la certificado
                fetch('update/update-certificado.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_certificado: id, nombre_certificado: result.value })
                }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Éxito!', 'Certificado actualizado correctamente.', 'success').then(() => {
                                location.reload(); // Recargar la página para ver los cambios
                            });
                        } else {
                            Swal.fire('Error', 'Hubo un problema al actualizar el certificado.', 'error');
                        }
                    });
            }
        });
    });
});

// Borrar Certificado
document.querySelectorAll(".btn-borrar-certificado").forEach(button => {
    button.addEventListener("click", function () {
        let id = this.closest('tr').querySelector('td').innerText;
        console.log(id);
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás recuperar este certificado después de eliminarla.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX para eliminar el certificado
                fetch('delete/delete-certificado.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_certificado: id })
                }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Eliminado!', 'El certificado ha sido eliminado.', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', 'Hubo un problema al eliminar el certificado.', 'error');
                        }
                    });
            }
        });
    });
});


// Agregar Link de Instagram
document.getElementById("btnAgregarPublicacion").addEventListener("click", function () {
    Swal.fire({
        title: 'Agregar publicacion de Instagram',
        input: 'url',
        inputLabel: 'Ingrese un link de una publicacion de Instagram',
        inputPlaceholder: 'Ingrese el link',
        showCancelButton: true,
        confirmButtonText: 'Agregar',
        cancelButtonText: 'Cancelar',
        preConfirm: (nombre) => {
            if (!nombre) {
                Swal.showValidationMessage('Debe ingresar un link');
            }
            return nombre;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // AJAX para agregar certificado
            fetch('adds/add-link-publicacion.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nombre_certificado: result.value })
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Éxito!', 'Publicacion agregada correctamente.', 'success').then(() => {
                            location.reload(); // Recargar la página para actualizar la tabla
                        });
                    } else {
                        Swal.fire('Error', 'Hubo un problema al agregar el link de la publicacion.', 'error');
                    }
                });
        }
    });
});

// Editar publicacion
document.querySelectorAll(".btn-editar-publicacion").forEach(button => {
    button.addEventListener("click", function () {
        let id = this.closest('tr').querySelector('td').innerText;
        console.log(id);
        Swal.fire({
            title: 'Editar Link de la publicacion',
            input: 'url',
            inputLabel: 'Ingrese un link de una publicacion de Instagram',
            inputPlaceholder: 'Ingrese el link',
            inputValue: this.closest('tr').querySelector('td:nth-child(2)').innerText,
            showCancelButton: true,
            confirmButtonText: 'Actualizar',
            cancelButtonText: 'Cancelar',
            preConfirm: (nombre) => {
                if (!nombre) {
                    Swal.showValidationMessage('Debe ingresar un nombre');
                }
                return nombre;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX para actualizar la publicacion
                fetch('update/update-publicacion.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_publicacion: id, link_publicacion: result.value })
                }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Éxito!', 'Link de la publicacion actualizado correctamente.', 'success').then(() => {
                                location.reload(); // Recargar la página para ver los cambios
                            });
                        } else {
                            Swal.fire('Error', 'Hubo un problema al actualizar el link de la publicacion.', 'error');
                        }
                    });
            }
        });
    });
});

// Borrar publicacion
document.querySelectorAll(".btn-borrar-publicacion").forEach(button => {
    button.addEventListener("click", function () {
        let id = this.closest('tr').querySelector('td').innerText;
        console.log(id);
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás recuperar este publicacion después de eliminarla.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX para eliminar el publicacion
                fetch('delete/delete-publicacion.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_publicacion: id })
                }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Eliminado!', 'El link de la publicacion ha sido eliminado.', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', 'Hubo un problema al eliminar el link de la publicacion.', 'error');
                        }
                    });
            }
        });
    });
});
});












