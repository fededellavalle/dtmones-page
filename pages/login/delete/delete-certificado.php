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

// Validar que se recibió el ID del certificado
if (isset($data['id_certificado']) && !empty($data['id_certificado'])) {
    $id_certificado = $data['id_certificado'];

    // Verificar si el certificado está asociada a algún coach
    $stmt = $conn->prepare("SELECT COUNT(*) FROM coaches WHERE id_certificado = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id_certificado);
        $stmt->execute();
        $stmt->bind_result($count_coaches);
        $stmt->fetch();

        // Cerrar la consulta de verificación
        $stmt->close();


        // Si hay coaches asociados, no se puede eliminar el certificado
        if ($count_coaches > 0) {
            echo json_encode(['success' => false, 'error' => 'No se puede eliminar el certificado porque está asociada a coaches.']);
        } else {
            // Preparar la consulta para eliminar el certificado
            $stmt = $conn->prepare("DELETE FROM certificados WHERE id_certificado = ?");
            if ($stmt) {
                $stmt->bind_param("i", $id_certificado);

                // Ejecutar la consulta de eliminación y enviar respuesta al cliente
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Error al ejecutar la consulta de eliminación.']);
                }

                // Cerrar el stmt después de la ejecución
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al preparar la consulta de eliminación.']);
            }
        }

    } else {
        echo json_encode(['success' => false, 'error' => 'Error al verificar la existencia de coaches asociados al certificado.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'ID del certificado no proporcionado.']);
}

// Cerrar la conexión
$conn->close();
?>