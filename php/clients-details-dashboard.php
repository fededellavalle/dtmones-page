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

// Obtener el ID desde la URL
$id = isset($_GET['id']) ? $_GET['id'] : null;
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : null;  // Obtener el tipo de jugador o coach

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

// Verificar si se pasó un ID y tipo
if ($id && $tipo) {
    if ($tipo == 'jugador') {
        // Consulta para obtener detalles del jugador
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
            WHERE c.id = ?
        ";

        $stmtJugador = $conn->prepare($sqlJugador);
        $stmtJugador->bind_param("i", $id);
        $stmtJugador->execute();
        $resultJugador = $stmtJugador->get_result();

        if ($resultJugador->num_rows > 0) {
            $player = $resultJugador->fetch_assoc();

            echo '<img src="' . htmlspecialchars($player['imagen']) . '" class="img-fluid mb-3" alt="Imagen del jugador">';
            echo '<h5>' . htmlspecialchars($player['jugador_nombre']) . ' ' . htmlspecialchars($player['jugador_apellido']) . '</h5>';
            echo '<p>Nacionalidad: ' . htmlspecialchars($player['nacionalidad']) . '</p>';
            echo '<p>Posición: ' . htmlspecialchars($player['posicion']) . '</p>';
            echo '<p>Categoría: ' . htmlspecialchars($player['categoria']) . '</p>';
            echo '<p>Equipo: ' . htmlspecialchars($player['equipo']) . '</p>';
            echo '<p>Fecha de nacimiento: ' . htmlspecialchars($player['nacimiento']) . '</p>';
            echo '<p>Altura: ' . htmlspecialchars($player['altura']) . ' cm</p>';
            echo '<a href="' . htmlspecialchars($player['link_eurobasket']) . '" target="_blank">Perfil en Eurobasket</a>';

            // Obtener videos del jugador
            $sqlVideos = "SELECT link_video FROM videos WHERE id_cliente = ?";
            $stmtVideos = $conn->prepare($sqlVideos);
            $stmtVideos->bind_param("i", $id);
            $stmtVideos->execute();
            $resultVideos = $stmtVideos->get_result();

            if ($resultVideos->num_rows > 0) {
                echo '<h5>Videos:</h5>';
                while ($row = $resultVideos->fetch_assoc()) {
                    $videoEmbedUrl = convertirUrlAEmbed($row['link_video']);
                    if ($videoEmbedUrl) {
                        echo '<iframe width="560" height="315" src="' . htmlspecialchars($videoEmbedUrl) . '" frameborder="0" allowfullscreen class="videos"></iframe>';
                    } else {
                        echo '<p>URL de video no válida: ' . htmlspecialchars($row['link_video']) . '</p>';
                    }
                }
            } else {
                echo '<p>No se encontraron videos del jugador.</p>';
            }

            $stmtVideos->close();
        } else {
            echo "Jugador no encontrado.";
        }

        $stmtJugador->close();
    } elseif ($tipo == 'coach') {
        // Consulta para obtener detalles del coach
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
            WHERE c.id_coach = ?
        ";

        $stmtCoach = $conn->prepare($sqlCoach);
        $stmtCoach->bind_param("i", $id);
        $stmtCoach->execute();
        $resultCoach = $stmtCoach->get_result();

        if ($resultCoach->num_rows > 0) {
            $coach = $resultCoach->fetch_assoc();

            // Consultar experiencias del coach
            $sqlExperiencias = "SELECT descripcion FROM experiencias WHERE id_coach = ?";
            $stmtExperiencias = $conn->prepare($sqlExperiencias);
            $stmtExperiencias->bind_param("i", $id);
            $stmtExperiencias->execute();
            $resultExperiencias = $stmtExperiencias->get_result();

            $experiencias = [];
            if ($resultExperiencias->num_rows > 0) {
                while ($row = $resultExperiencias->fetch_assoc()) {
                    $experiencias[] = $row['descripcion'];
                }
            }

            // Mostrar detalles del coach
            echo '<img src="' . htmlspecialchars($coach['imagen']) . '" class="img-fluid mb-3" alt="Imagen del entrenador">';
            echo '<h5>' . htmlspecialchars($coach['coach_nombre']) . ' ' . htmlspecialchars($coach['coach_apellido']) . '</h5>';
            echo '<p>Nacionalidad: ' . htmlspecialchars($coach['nacionalidad']) . '</p>';
            echo '<p>Equipo: ' . htmlspecialchars($coach['equipo']) . '</p>';
            echo '<p>Fecha de nacimiento: ' . htmlspecialchars($coach['nacimiento']) . '</p>';
            echo '<p>Certificado: ' . htmlspecialchars($coach['nombre_certificado']) . '</p>';
            echo '<p>FIBA: ' . ($coach['es_fiba'] ? 'Sí' : 'No') . '</p>';

            // Mostrar experiencias del coach
            if (count($experiencias) > 0) {
                echo '<h5>Experiencia:</h5>';
                foreach ($experiencias as $experiencia) {
                    echo '<p class="p-experiencies">' . htmlspecialchars($experiencia) . '</p>';
                }
            } else {
                echo '<p>No se encontraron experiencias para este entrenador.</p>';
            }

        } else {
            echo "Entrenador no encontrado.";
        }

        $stmtCoach->close();
    } else {
        echo "Tipo no válido.";
    }
} else {
    echo "ID o tipo no especificado.";
}

$conn->close();

function calcularEdad($fechaNacimiento)
{
    $fechaNacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fechaNacimiento)->y;
    return $edad;
}
?>