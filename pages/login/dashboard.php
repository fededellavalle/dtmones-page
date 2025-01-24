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

// Obtener la lista de clientes
$sql = "
    SELECT id AS id_persona, nombre, apellido, imagen, 'jugador' AS tipo
    FROM clientes
    UNION ALL
    SELECT id_coach AS id_persona, nombre, apellido, imagen, 'coach' AS tipo
    FROM coaches
    ORDER BY nombre ASC, apellido ASC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - DTMones</title>
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
    <link rel="stylesheet" href="/css/header.css">
    <link rel="stylesheet" href="/css/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/styles-dashboard.css">
</head>

<body>
    <header>
        <a href="/index.html">
            <img src="/assets/images/estrellaDTM.png" alt="Logo">
        </a>

        <div class="menu-icon">
            <i class="bi bi-list"></i>
        </div>
    </header>

    <!-- Overlay oscuro -->
    <div class="overlay"></div>

    <!-- Menú lateral -->
    <div class="side-menu">
        <button class="close-menu">&times;</button>
        <nav>
            <a href="/index.html">Home</a>
            <a href="/pages/who-we-are.html">Who we are</a>
            <a href="/pages/clients.html">Clients</a>
            <a href="/pages/legends.html">Legends</a>
            <a href="/pages/media.html">Media</a>
            <a href="/pages/contact-us.html">Contact us</a>
        </nav>
    </div>

    <div class="main-banner">
        <h1>Bienvenido, Gustavo</h1>
    </div>

    <div class="player-section">
        <div class="container">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $_SESSION['success']; ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <!-- Buscador -->
            <div class="row mb-4">
                <div class="col-md-6 mx-auto">
                    <input type="text" id="searchInput" class="form-control"
                        placeholder="Buscar jugadores por nombre o apellido...">
                </div>
            </div>

            <!-- Botón de agregar jugador -->
            <div class="row mb-4">
                <div class="col-md-4 text-center">
                    <a href="/pages/login/add-client.php" class="btn btn-success">Agregar Jugador</a>
                </div>
                <div class="col-md-4 text-center">
                    <a href="/pages/login/add-coach.php" class="btn btn-success">Agregar Coach</a>
                </div>
                <div class="col-md-4 text-center">
                    <a href="/pages/login/options.php" class="btn btn-success">Opciones</a>
                </div>
            </div>

            <div class="row" id="player-list">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="col-md-3 col-sm-6 div-card player-item">
                            <div class="card-player mx-auto"
                                onclick="showPlayerInfo(<?php echo $row['id_persona']; ?>, '<?php echo $row['tipo']; ?>')">

                                <div class="card-image">
                                    <img src="<?php echo htmlspecialchars($row['imagen']); ?>"
                                        alt="<?php echo htmlspecialchars($row['nombre']); ?>">
                                </div>
                                <p class="player-name">
                                    <?php echo htmlspecialchars($row['nombre']); ?>
                                    <?php echo htmlspecialchars($row['apellido']); ?>
                                </p>
                                <p class="player-type">
                                    <?php if ($row['tipo'] == 'jugador'): ?>
                                        Jugador
                                    <?php else: ?>
                                        Entrenador
                                    <?php endif; ?>
                                </p>
                                <div>
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm"
                                        onclick="event.stopPropagation(); <?php echo ($row['tipo'] == 'jugador') ? 'confirmEdit(' . $row['id_persona'] . ');' : 'confirmEditCoach(' . $row['id_persona'] . ');'; ?>">Editar</a>

                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm"
                                        onclick="event.stopPropagation(); <?php echo ($row['tipo'] == 'jugador') ? 'confirmDelete(' . $row['id_persona'] . ');' : 'confirmDeleteCoach(' . $row['id_persona'] . ');'; ?>">Borrar</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center">No hay registros disponibles.</p>
                <?php endif; ?>
            </div>

        </div>
    </div>



    <footer class="footer text-white">
        <div class="container d-flex justify-content-between align-items-center flex-column flex-md-row">
            <div class="footer-logo text-center mb-4 mb-md-0">
                <img src="/assets/images/logo3.png" alt="DTMones Basketball Agency" class="img-fluid mb-2"
                    style="max-width: 150px;" />
                <p class="mb-0">DTMones Basketball Agency</p>
            </div>
            <div class="footer-contact text-center text-md-end">
                <p class="mb-0">Tel: 35534343</p>
                <p class="mb-0">dtmones@gmail.com.ar</p>
                <p class="mb-0">www.dtmones.com.ar</p>
                <hr class="my-2 mx-auto mx-md-0" style="border-top: 1px solid #ffffff; width: 80%;" />
                <div class="social-icons d-flex justify-content-center justify-content-md-end">
                    <a href="https://www.facebook.com/p/DTMones-Basketball-Agency-100066988350727/"
                        class="text-white me-3"><i class="bi bi-facebook"></i></a>
                    <a href="https://www.instagram.com/dtm.ones/" class="text-white me-3"><i
                            class="bi bi-instagram"></i></a>
                    <a href="https://twitter.com/dtm_ones" class="text-white me-3"><i class="bi bi-twitter"></i></a>
                    <a href="https://www.youtube.com/channel/UCSvEghAAbhjs1HpLl68FIvw" class="text-white me-3"><i
                            class="bi bi-youtube"></i></a>
                    <a href="#" class="text-white"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="text-center mt-4 div-copy">
            <p class="mb-0">&copy; 2025 DTMones Basketball Agency. Todos los derechos
                reservados.</p>
        </div>
    </footer>

    <div class="modal fade" id="playerInfoModal" tabindex="-1" aria-labelledby="playerInfoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="playerInfoModalLabel">Información del Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div id="playerDetails">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script src="/js/header.js"></script>
    <script src="/js/script-dashboard.js"></script>
</body>

</html>