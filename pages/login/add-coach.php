<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['username'])) {
    header("Location: /pages/login.php");
    exit;
}

// Conexión a la base de datos
$host = "localhost:3307";
$user = "root";
$password = "";
$dbname = "clientesdb";

$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener opciones para equipos
$equipos = $conn->query("SELECT id_equipo, nombre_equipo FROM equipo ORDER BY nombre_equipo ASC");

// Obtener opciones para nacionalidades
$nacionalidades = $conn->query("SELECT id_nacionalidad, nombre_nacionalidad FROM nacionalidad ORDER BY nombre_nacionalidad ASC");

$certificados = $conn->query("SELECT id_certificado, nombre_certificado FROM certificados ORDER BY nombre_certificado ASC");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Coach</title>
    <link rel="apple-touch-icon" sizes="57x57" href="/assets/images/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/assets/images/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/assets/images/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/assets/images/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/assets/images/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/assets/images/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/assets/images/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/assets/images/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/assets/images/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/assets/images/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="/assets/images/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="/css/styles-add-coach.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="/css/styles-add-client.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <h1 class="text-center mb-4">Agregar Coach</h1>

        <div class="col-md-3 div-card mx-auto mb-3" id="preview-card">
            <div class="card-player mx-auto">
                <div class="card-image">
                    <img src="/assets/images/image-preview.png" alt="Imagen del Cliente" id="preview-image">
                    <div class="overlays">
                        <p id="preview-name">NOMBRE APELLIDO</p>
                    </div>
                </div>
                <div class="card-details">
                    <p id="preview-details">Nacionalidad<br>Current Team:</p>
                </div>
            </div>
        </div>

        <form action="/pages/login/adds/save-coach.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <!-- Datos personales -->
                <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="apellido" class="form-label">Apellido</label>
                    <input type="text" id="apellido" name="apellido" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nacimiento" class="form-label">Fecha de Nacimiento</label>
                    <input type="date" id="nacimiento" name="nacimiento" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="id_equipo" class="form-label">Equipo</label>
                    <select id="id_equipo" name="id_equipo" class="form-control select2" required>
                        <option value="" selected>Seleccione un equipo</option>
                        <?php while ($equipo = $equipos->fetch_assoc()): ?>
                            <option value="<?php echo $equipo['id_equipo']; ?>">
                                <?php echo $equipo['nombre_equipo']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="button" class="btn btn-primary mt-2" id="btnAgregarEquipo">Agregar Equipo</button>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="id_nacionalidad" class="form-label">Nacionalidad</label>
                    <select id="id_nacionalidad" name="id_nacionalidad" class="form-control select2" required>
                        <option value="" selected>Seleccione una nacionalidad</option>
                        <?php while ($nacionalidad = $nacionalidades->fetch_assoc()): ?>
                            <option value="<?php echo $nacionalidad['id_nacionalidad']; ?>">
                                <?php echo $nacionalidad['nombre_nacionalidad']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="button" class="btn btn-primary mt-2" id="btnAgregarNacionalidad">Agregar
                        Nacionalidad</button>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="imagen" class="form-label">Foto de Perfil</label>
                    <input type="file" id="imagen" name="imagen" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="id_certificado" class="form-label">Certificado</label>
                    <select id="id_certificado" name="id_certificado" class="form-control select2" required>
                        <option value="" selected>Seleccione un certificado</option>
                        <?php while ($certificado = $certificados->fetch_assoc()): ?>
                            <option value="<?php echo $certificado['id_certificado']; ?>">
                                <?php echo $certificado['nombre_certificado']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="button" class="btn btn-primary mt-2" id="btnAgregarCertificado">Agregar
                        Certificado</button>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="es_fiba" class="form-label">Es entrenador FIBA?</label>
                    <select id="es_fiba" name="es_fiba" class="form-control select2" required>
                        <option value="" selected>Seleccione una opcion</option>
                        <option value="1" selected>Si</option>
                        <option value="0" selected>No</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Experiencias</label>
                    <div id="youtubeLinks">
                        <div class="input-group mb-3">
                            <input type="url" name="experiencias[]" class="form-control"
                                placeholder="Ingrese una experiencia del coach">
                            <button type="button" class="btn btn-danger btn-sm removeLinkBtn">Eliminar</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" id="addLinkBtn">Agregar otra
                        experiencia</button>
                </div>
            </div>

            <div class="text-center mt-4">
                <!-- Botón Guardar Jugador con confirmación -->
                <button type="button" id="guardarCoachBtn" class="btn btn-primary">Guardar Coach</button>

                <!-- Botón Cancelar con confirmación -->
                <a href="javascript:void(0);" id="cancelarBtn" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <!-- Select2 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

    <script src="/js/script-add-coach.js"></script>
</body>

</html>