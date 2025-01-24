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

// Validar que se recibió el ID del certificado y el nuevo nombre
if (isset($data['id_certificado']) && isset($data['nombre_certificado']) && !empty(trim($data['nombre_certificado']))) {
    $id_certificado = $data['id_certificado'];
    $nombre_certificado = trim($data['nombre_certificado']);

    // Verificar si ya existe un certificado con el mismo nombre, pero diferente ID (para evitar conflictos con el certificado que estamos actualizando)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM certificados WHERE nombre_certificado = ? AND id_certificado != ?");
    if ($stmt) {
        $stmt->bind_param("si", $nombre_certificado, $id_certificado);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();

        // Cerrar el primer statement antes de ejecutar otro
        $stmt->close();

        // Si ya existe un certificado con el mismo nombre, se devuelve un error
        if ($count > 0) {
            echo json_encode(['success' => false, 'error' => 'Ya existe un certificado con ese nombre.']);
        } else {
            // Preparar la consulta para actualizar el certificado
            $stmt = $conn->prepare("UPDATE certificados SET nombre_certificado = ? WHERE id_certificado = ?");
            if ($stmt) {
                $stmt->bind_param("si", $nombre_certificado, $id_certificado);

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
        echo json_encode(['success' => false, 'error' => 'Error al verificar la existencia del certificado.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'ID del certificado o nombre del certificado no proporcionado.']);
}

// Cerrar la conexión
$conn->close();
?>