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

// Leer los datos enviados desde el cliente
$data = json_decode(file_get_contents("php://input"), true);

// Validar que se recibió el nombre de la nacionalidad
if (isset($data['nombre_nacionalidad']) && !empty(trim($data['nombre_nacionalidad']))) {
    $nombre_nacionalidad = trim($data['nombre_nacionalidad']);

    // Verificar si ya existe una nacionalidad con el mismo nombre
    $stmt = $conn->prepare("SELECT COUNT(*) FROM nacionalidad WHERE nombre_nacionalidad = ?");
    if ($stmt) {
        $stmt->bind_param("s", $nombre_nacionalidad);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();

        // Cerrar la consulta de verificación antes de seguir
        $stmt->close();

        // Si ya existe una nacionalidad con el mismo nombre, se devuelve un error
        if ($count > 0) {
            echo json_encode(['success' => false, 'error' => 'Ya existe una nacionalidad con ese nombre.']);
        } else {
            // Preparar la consulta para insertar la nueva nacionalidad
            $stmt = $conn->prepare("INSERT INTO nacionalidad (nombre_nacionalidad) VALUES (?)");
            if ($stmt) {
                $stmt->bind_param("s", $nombre_nacionalidad);

                // Ejecutar la consulta y enviar respuesta al cliente
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Error al ejecutar la consulta de inserción.']);
                }

                // Cerrar el stmt después de la ejecución
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al preparar la consulta de inserción.']);
            }
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al verificar la existencia de la nacionalidad.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Nombre de la nacionalidad no proporcionado.']);
}

// Cerrar la conexión
$conn->close();
?>