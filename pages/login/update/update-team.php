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

// Validar que se recibió el ID del equipo y el nuevo nombre
if (isset($data['id_equipo']) && isset($data['nombre_equipo']) && !empty(trim($data['nombre_equipo']))) {
    $id_equipo = $data['id_equipo'];
    $nombre_equipo = trim($data['nombre_equipo']);

    // Verificar si ya existe un equipo con el mismo nombre, pero diferente ID (para evitar conflictos con el equipo que estamos actualizando)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM equipo WHERE nombre_equipo = ? AND id_equipo != ?");
    if ($stmt) {
        $stmt->bind_param("si", $nombre_equipo, $id_equipo);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();

        // Cerrar el primer statement antes de ejecutar otro
        $stmt->close();

        // Si ya existe un equipo con el mismo nombre, se devuelve un error
        if ($count > 0) {
            echo json_encode(['success' => false, 'error' => 'Ya existe un equipo con ese nombre.']);
        } else {
            // Preparar la consulta para actualizar el equipo
            $stmt = $conn->prepare("UPDATE equipo SET nombre_equipo = ? WHERE id_equipo = ?");
            if ($stmt) {
                $stmt->bind_param("si", $nombre_equipo, $id_equipo);

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
        echo json_encode(['success' => false, 'error' => 'Error al verificar la existencia del equipo.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'ID del equipo o nombre del equipo no proporcionado.']);
}

// Cerrar la conexión
$conn->close();
?>