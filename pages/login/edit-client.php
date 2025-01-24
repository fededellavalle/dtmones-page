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

// Obtener el ID del cliente desde la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de cliente no proporcionado.");
}

$id_cliente = $_GET['id'];

// Obtener datos del cliente
$query = "SELECT * FROM clientes WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Cliente no encontrado.");
}

$cliente = $result->fetch_assoc();

// Consulta SQL para obtener los videos del jugador
$sqlVideos = "SELECT link_video FROM videos WHERE id_cliente = '$id_cliente'";
$resultVideos = $conn->query($sqlVideos);

$videos = [];
if ($resultVideos->num_rows > 0) {
    while ($row = $resultVideos->fetch_assoc()) {
        $videos[] = $row['link_video'];
    }
}

// Obtener opciones de joins
$posiciones = $conn->query("SELECT id_posicion, nombre_posicion, abreviacion_posicion FROM posicion");
$nacionalidades = $conn->query("SELECT id_nacionalidad, nombre_nacionalidad FROM nacionalidad ORDER BY nombre_nacionalidad ASC");
$equipos = $conn->query("SELECT id_equipo, nombre_equipo FROM equipo ORDER BY nombre_equipo ASC");
$categorias = $conn->query("SELECT id_categoria, nombre_categoria FROM categoria ORDER BY nombre_categoria ASC");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
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
    <link rel="stylesheet" href="/css/styles-add-player.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="/css/styles-edit-client.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <h1 class="text-center mb-4">Editar Cliente</h1>

        <div class="col-md-3 div-card mx-auto mb-3" id="preview-card">
            <div class="card-player mx-auto">
                <div class="card-category" id="preview-position">POS</div>
                <div class="card-image">
                    <img src="<?php echo $cliente['imagen']; ?>" alt="Imagen del Cliente" id="preview-image">
                    <div class="overlays">
                        <p id="preview-name">NOMBRE APELLIDO</p>
                    </div>
                </div>
                <div class="card-details">
                    <p id="preview-details">Nacionalidad<br>Position: POS</p>
                </div>
            </div>
        </div>

        <form action="/pages/login/update/update-client.php" method="POST" enctype="multipart/form-data" id="form">
            <input type="hidden" name="id_cliente" value="<?php echo $id_cliente; ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" id="nombre" name="nombre" class="form-control"
                        value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="apellido" class="form-label">Apellido</label>
                    <input type="text" id="apellido" name="apellido" class="form-control"
                        value="<?php echo htmlspecialchars($cliente['apellido']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nacimiento" class="form-label">Fecha de Nacimiento</label>
                    <input type="date" id="nacimiento" name="nacimiento" class="form-control"
                        value="<?php echo $cliente['nacimiento']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="id_posicion" class="form-label">Posición</label>
                    <select id="id_posicion" name="id_posicion" class="form-control select2" required>
                        <option value="" disabled>Seleccione una posición</option>
                        <?php while ($posicion = $posiciones->fetch_assoc()): ?>
                            <option value="<?php echo $posicion['id_posicion']; ?>" <?php echo ($cliente['id_posicion'] == $posicion['id_posicion']) ? 'selected' : ''; ?>>
                                <?php echo $posicion['nombre_posicion']; ?>
                                (<?php echo $posicion['abreviacion_posicion']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="id_nacionalidad" class="form-label">Nacionalidad</label>
                    <select id="id_nacionalidad" name="id_nacionalidad" class="form-control select2" required>
                        <option value="" disabled>Seleccione una nacionalidad</option>
                        <?php while ($nacionalidad = $nacionalidades->fetch_assoc()): ?>
                            <option value="<?php echo $nacionalidad['id_nacionalidad']; ?>" <?php echo ($cliente['id_nacionalidad'] == $nacionalidad['id_nacionalidad']) ? 'selected' : ''; ?>>
                                <?php echo $nacionalidad['nombre_nacionalidad']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="id_equipo" class="form-label">Equipo</label>
                    <select id="id_equipo" name="id_equipo" class="form-control select2" required>
                        <option value="" disabled>Seleccione un equipo</option>
                        <?php while ($equipo = $equipos->fetch_assoc()): ?>
                            <option value="<?php echo $equipo['id_equipo']; ?>" <?php echo ($cliente['id_equipo'] == $equipo['id_equipo']) ? 'selected' : ''; ?>>
                                <?php echo $equipo['nombre_equipo']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="imagen" class="form-label">Foto de Perfil</label>
                    <input type="file" id="imagen" name="imagen" class="form-control">
                    <small class="text-muted">Deje este campo vacío para mantener la imagen actual.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="link_eurobasket" class="form-label">Link a Eurobasket</label>
                    <input type="url" id="link_eurobasket" name="link_eurobasket" class="form-control"
                        value="<?php echo $cliente['link_eurobasket']; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="id_categoria" class="form-label">Categoría</label>
                    <select id="id_categoria" name="id_categoria" class="form-control select2" required>
                        <option value="" disabled>Seleccione una categoría</option>
                        <?php while ($categoria = $categorias->fetch_assoc()): ?>
                            <option value="<?php echo $categoria['id_categoria']; ?>" <?php echo ($cliente['id_categoria'] == $categoria['id_categoria']) ? 'selected' : ''; ?>>
                                <?php echo $categoria['nombre_categoria']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">Links de YouTube</label>
                <div id="youtubeLinks">
                    <?php if (!empty($videos)): ?>
                        <?php foreach ($videos as $video): ?>
                            <div class="input-group mb-3">
                                <input type="url" name="youtube_links[]" class="form-control"
                                    value="<?php echo htmlspecialchars($video); ?>" placeholder="Ingrese un link de YouTube">
                                <button type="button" class="btn btn-danger btn-sm removeLinkBtn">Eliminar</button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="input-group mb-3">
                            <input type="url" name="youtube_links[]" class="form-control"
                                placeholder="Ingrese un link de YouTube">
                            <button type="button" class="btn btn-danger btn-sm removeLinkBtn">Eliminar</button>
                        </div>
                    <?php endif; ?>

                </div>
                <button type="button" class="btn btn-secondary btn-sm" id="addLinkBtn">Agregar otro link</button>
            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Actualizar Cliente</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <script src="/js/script-edit-client.js"></script>
</body>

</html>