<?php
function convertirUrlAEmbed($url)
{
    $urlParts = parse_url($url);
    parse_str($urlParts['query'], $queryParams);

    if (isset($queryParams['v'])) {
        return "https://www.youtube.com/embed/" . $queryParams['v'];
    }

    return null; // Retorna null si no es una URL válida
}

// Obtener el ID del jugador desde la URL
$idJugador = isset($_GET['id']) ? $_GET['id'] : null;

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

// Verificar si se pasó un ID de jugador
if ($idJugador) {
    // Consulta SQL para obtener los detalles del jugador
    $sqlJugador = "
    SELECT 
        c.id AS id, 
        c.nombre AS jugador_nombre,
        c.apellido AS jugador_apellido,
        n.nombre_nacionalidad AS nacionalidad, 
        p.nombre_posicion AS posicion,
        cat.nombre_categoria AS categoria,
        e.nombre_equipo AS equipo,
        c.imagen,
        c.nacimiento,
        c.altura,
        c.link_eurobasket
    FROM clientes c
    INNER JOIN nacionalidad n ON c.id_nacionalidad = n.id_nacionalidad
    INNER JOIN posicion p ON c.id_posicion = p.id_posicion
    INNER JOIN categoria cat ON c.id_categoria = cat.id_categoria
    INNER JOIN equipo e ON c.id_equipo = e.id_equipo
    WHERE c.id = '$idJugador'
";


    $resultJugador = $conn->query($sqlJugador);

    // Verificar si se encontró el jugador
    if ($resultJugador->num_rows > 0) {
        $player = $resultJugador->fetch_assoc();

        // Consulta SQL para obtener los videos del jugador
        $sqlVideos = "SELECT link_video FROM videos WHERE id_cliente = '$idJugador'";
        $resultVideos = $conn->query($sqlVideos);

        $videos = [];
        if ($resultVideos->num_rows > 0) {
            while ($row = $resultVideos->fetch_assoc()) {
                $videos[] = $row['link_video'];
            }
        }
    } else {
        echo "Jugador no encontrado.";
        exit;
    }
} else {
    echo "ID de jugador no especificado.";
    exit;
}


$conn->close();

function calcularEdad($fechaNacimiento)
{
    $fechaNacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fechaNacimiento)->y;
    return $edad;
}

// Calcular la edad del jugador
$edadJugador = calcularEdad($player['nacimiento']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $player['jugador_nombre'] . " " . $player['jugador_apellido']; ?> - DTMones</title>
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
    <link rel="stylesheet" href="/css/loader.css">
    <link rel="stylesheet" href="/css/styles-clients-details.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
</head>

<body>
    <div id="loader">
        <img src="/assets/images/estrellaDTM.png" alt="Cargando...">
    </div>

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

    <div class="main">
        <div class="client-details">
            <div style="display: flex; align-items: left;" class="div-button">
                <a href="<?php
                if (strtolower($player['categoria']) === 'legends') {
                    echo '/pages/legends.html';
                } else {
                    echo '/pages/clients/' . strtolower($player['categoria']) . '.html';
                }
                ?>" class="btn btn-danger">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
            <h1 class="client-name"><?php echo $player['jugador_nombre'] . " " . $player['jugador_apellido']; ?></h1>
            <div class="client-description">
                <div class="client-image-container">
                    <img src="<?php echo $player['imagen']; ?>" alt="<?php echo $player['jugador_nombre']; ?>"
                        class="client-image">
                </div>
                <div class="description">
                    <p><strong>Position:</strong> <?php echo $player['posicion']; ?></p>
                    <p><strong>Current Team:</strong> <?php echo $player['equipo']; ?></p>
                    <p><strong>Country:</strong> <?php echo $player['nacionalidad']; ?></p>
                    <p><strong>Date of Birth:</strong> <?php echo $player['nacimiento']; ?> (<?php echo $edadJugador; ?>
                        years)</p>
                    <p><strong>Height:</strong> <?php echo $player['altura']; ?> cm</p>
                    <p><strong>Eurobasket Profile:</strong>
                        <a href="<?php echo $player['link_eurobasket']; ?>" target="_blank">View Profile</a>
                    </p>
                </div>

            </div>


            <h2>Videos</h2>
            <div class="videos">
                <?php if (!empty($videos)): ?>
                    <?php foreach ($videos as $video): ?>
                        <?php $embedUrl = convertirUrlAEmbed($video); ?>
                        <?php if ($embedUrl): ?>
                            <div class="video">
                                <iframe width="560" height="315" src="<?php echo $embedUrl; ?>" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                            </div>
                        <?php else: ?>
                            <p>URL de video inválida: <?php echo $video; ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay videos disponibles para este jugador.</p>
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
                    <a href="/pages/login.html" class="text-white"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="text-center mt-4 div-copy">
            <p class="mb-0">&copy; 2025 DTMones Basketball Agency. Todos los derechos
                reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="/js/header.js"></script>
    <script>
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
    </script>
</body>

</html>