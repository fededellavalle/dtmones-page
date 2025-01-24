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

// Validar que se recibió el ID de la nacionalidad y el nuevo nombre
if (isset($data['id_nacionalidad']) && isset($data['nombre_nacionalidad']) && !empty(trim($data['nombre_nacionalidad']))) {
    $id_nacionalidad = $data['id_nacionalidad'];
    $nombre_nacionalidad = trim($data['nombre_nacionalidad']);

    // Verificar si ya existe una nacionalidad con el mismo nombre, pero diferente ID (para evitar conflictos con la nacionalidad que estamos actualizando)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM nacionalidad WHERE nombre_nacionalidad = ? AND id_nacionalidad != ?");
    if ($stmt) {
        $stmt->bind_param("si", $nombre_nacionalidad, $id_nacionalidad);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();

        // Cerrar el primer statement antes de ejecutar otro
        $stmt->close();

        // Si ya existe una nacionalidad con el mismo nombre, se devuelve un error
        if ($count > 0) {
            echo json_encode(['success' => false, 'error' => 'Ya existe una nacionalidad con ese nombre.']);
        } else {
            // Preparar la consulta para actualizar la nacionalidad
            $stmt = $conn->prepare("UPDATE nacionalidad SET nombre_nacionalidad = ? WHERE id_nacionalidad = ?");
            if ($stmt) {
                $stmt->bind_param("si", $nombre_nacionalidad, $id_nacionalidad);

                // Ejecutar la consulta y enviar respuesta al cliente
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Error al ejecutar la consulta de actualización.']);
                }

                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al preparar la consulta de actualización.']);
            }
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al verificar la existencia de la nacionalidad.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'ID de la nacionalidad o nombre de la nacionalidad no proporcionado.']);
}

// Cerrar la conexión
$conn->close();
?>