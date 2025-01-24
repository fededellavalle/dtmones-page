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

// Consultar datos de cada tabla
function getTableData($conn, $tableName)
{
    $sql = "SELECT * FROM $tableName";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// Obtener datos de las tablas
$nacionalidades = getTableData($conn, 'nacionalidad');
$equipos = getTableData($conn, 'equipo');
$certificados = getTableData($conn, 'certificados');
$publicaciones = getTableData($conn, 'publicaciones_instagram'); // Nueva tabla
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opciones</title>
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container my-5">
        <h1 class="text-center mb-4">Opciones</h1>
        <a href="/pages/login/dashboard.php" class="btn btn-primary">Volver a Dashboard</a>

        <!-- Tabla de Nacionalidades -->
        <h2 class="mt-4">Nacionalidades</h2>
        <button class="btn btn-success btn-sm mt-1 mb-1" id="btnAgregarNacionalidad">Agregar Nacionalidad</button>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre Nacionalidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($nacionalidades as $nacionalidad): ?>
                    <tr>
                        <td><?= $nacionalidad['id_nacionalidad'] ?></td>
                        <td><?= htmlspecialchars($nacionalidad['nombre_nacionalidad']) ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-editar-nacionalidad">Editar</button>
                            <button class="btn btn-danger btn-sm btn-borrar-nacionalidad">Borrar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Tabla de Equipos -->
        <h2 class="mt-4">Equipos</h2>
        <button class="btn btn-success btn-sm mt-1 mb-1" id="btnAgregarEquipo">Agregar Equipo</button>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre Equipo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($equipos as $equipo): ?>
                    <tr>
                        <td><?= $equipo['id_equipo'] ?></td>
                        <td><?= htmlspecialchars($equipo['nombre_equipo']) ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-editar-equipo">Editar</button>
                            <button class="btn btn-danger btn-sm btn-borrar-equipo">Borrar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Tabla de Certificados -->
        <h2 class="mt-4">Certificados</h2>
        <button class="btn btn-success btn-sm mt-1 mb-1" id="btnAgregarCertificado">Agregar Certificado</button>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre Certificado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($certificados as $certificado): ?>
                    <tr>
                        <td><?= $certificado['id_certificado'] ?></td>
                        <td><?= htmlspecialchars($certificado['nombre_certificado']) ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-editar-certificado">Editar</button>
                            <button class="btn btn-danger btn-sm btn-borrar-certificado">Borrar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Tabla de Publicaciones de Instagram -->
        <h2 class="mt-4">Publicaciones de Instagram</h2>
        <button class="btn btn-success btn-sm mt-1 mb-1" id="btnAgregarPublicacion">Agregar Publicación</button>
        <br>
        <small class="text-muted">Maximo 9 link de publicaciones.</small>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Link Publicación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($publicaciones as $publicacion): ?>
                    <tr>
                        <td><?= $publicacion['id_publicacion'] ?></td>
                        <td><?= htmlspecialchars($publicacion['link_publicacion']) ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-editar-publicacion">Editar</button>
                            <button class="btn btn-danger btn-sm btn-borrar-publicacion">Borrar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="/js/script-options.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>

</body>

</html>