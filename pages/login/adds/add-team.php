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

// Validar que se recibió el nombre del equipo
if (isset($data['nombre_equipo']) && !empty(trim($data['nombre_equipo']))) {
    $nombre_equipo = trim($data['nombre_equipo']);

    // Preparar la consulta para evitar inyección SQL
    $stmt = $conn->prepare("INSERT INTO equipo (nombre_equipo) VALUES (?)");
    if ($stmt) {
        $stmt->bind_param("s", $nombre_equipo);

        // Ejecutar la consulta y enviar respuesta al cliente
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al ejecutar la consulta.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al preparar la consulta.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Nombre del equipo no proporcionado.']);
}

// Cerrar la conexión
$conn->close();
?>