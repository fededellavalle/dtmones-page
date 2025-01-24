<?php
// Obtener el ID del coach desde la URL
$idCoach = isset($_GET['id']) ? $_GET['id'] : null;

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

// Verificar si se pasó un ID de coach
if ($idCoach) {
    // Consulta SQL para obtener los detalles del coach
    $sqlCoach = "
    SELECT 
        c.id_coach AS id, 
        c.nombre AS coach_nombre,
        c.apellido AS coach_apellido,
        n.nombre_nacionalidad AS nacionalidad, 
        e.nombre_equipo AS equipo,
        c.imagen,
        c.nacimiento,
        cert.nombre_certificado,
        c.es_fiba
    FROM coaches c
    INNER JOIN nacionalidad n ON c.id_nacionalidad = n.id_nacionalidad
    INNER JOIN equipo e ON c.id_equipo = e.id_equipo
    INNER JOIN certificados cert ON c.id_certificado = cert.id_certificado
    WHERE c.id_coach = '$idCoach'
";

    $resultCoach = $conn->query($sqlCoach);

    // Verificar si se encontró el coach
    if ($resultCoach->num_rows > 0) {
        $coach = $resultCoach->fetch_assoc();

        // Consulta SQL para obtener las experiencias del coach
        $sqlExperiencias = "SELECT descripcion FROM experiencias WHERE id_coach = '$idCoach'";
        $resultExperiencias = $conn->query($sqlExperiencias);

        $experiencias = [];
        if ($resultExperiencias->num_rows > 0) {
            while ($row = $resultExperiencias->fetch_assoc()) {
                $experiencias[] = $row['descripcion'];
            }
        }
    } else {
        echo "Coach no encontrado.";
        exit;
    }
} else {
    echo "ID de coach no especificado.";
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

// Calcular la edad del coach
$edadCoach = calcularEdad($coach['nacimiento']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $coach['coach_nombre'] . " " . $coach['coach_apellido']; ?> - DTMones</title>
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
                <a href="/pages/clients/coaches.html" class="btn btn-danger">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
            <h1 class="client-name"><?php echo $coach['coach_nombre'] . " " . $coach['coach_apellido']; ?></h1>
            <div class="client-description">
                <div class="client-image-container">
                    <img src="<?php echo $coach['imagen']; ?>" alt="<?php echo $coach['coach_nombre']; ?>"
                        class="client-image">
                </div>
                <div class="description">
                    <p><strong>Current Team:</strong> <?php echo $coach['equipo']; ?></p>
                    <p><strong>Country:</strong> <?php echo $coach['nacionalidad']; ?></p>
                    <p><strong>Certification: </strong> <?php echo $coach['nombre_certificado']; ?></p>
                    <p><strong>Date of Birth:</strong> <?php echo $coach['nacimiento']; ?> (<?php echo $edadCoach; ?>
                        years)</p>
                    <p><strong>FIBA Accreditation:</strong> <?php echo $coach['es_fiba'] ? 'Yes' : 'No'; ?></p>
                </div>
            </div>

            <h2 class="mt-3 mb-3">Experiences</h2>
            <div class="experiencias">
                <?php if (!empty($experiencias)): ?>
                    <?php foreach ($experiencias as $experiencia): ?>
                        <div class="experiencia">
                            <p>• <?php echo $experiencia; ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay experiencias disponibles para este coach.</p>
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
        integrity="sha384-llJ6D2pTegksJm1tQQkq5s6F0pyT1o6z5BzmTxmttT4kXtPppXz9NYy2dlRJ0g2V"
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