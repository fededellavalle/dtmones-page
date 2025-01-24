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

// Validar que se recibió el ID del equipo
if (isset($data['id_equipo']) && !empty($data['id_equipo'])) {
    $id_equipo = $data['id_equipo'];

    // Verificar si el equipo está asociada a algún coach
    $stmt = $conn->prepare("SELECT COUNT(*) FROM coaches WHERE id_equipo = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id_equipo);
        $stmt->execute();
        $stmt->bind_result($count_coaches);
        $stmt->fetch();

        // Cerrar la consulta de verificación
        $stmt->close();

        // Verificar si el equipo está asociada a algún cliente
        $stmt = $conn->prepare("SELECT COUNT(*) FROM clientes WHERE id_equipo = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id_equipo);
            $stmt->execute();
            $stmt->bind_result($count_clientes);
            $stmt->fetch();

            // Cerrar la consulta de verificación de clientes
            $stmt->close();

            // Si hay coaches o clientes asociados, no se puede eliminar el equipo
            if ($count_coaches > 0 || $count_clientes > 0) {
                echo json_encode(['success' => false, 'error' => 'No se puede eliminar el equipo porque está asociada a coaches o clientes.']);
            } else {
                // Preparar la consulta para eliminar la equipo
                $stmt = $conn->prepare("DELETE FROM equipo WHERE id_equipo = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $id_equipo);

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
            echo json_encode(['success' => false, 'error' => 'Error al verificar la existencia de clientes asociados al equipo.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al verificar la existencia de coaches asociados al equipo.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'ID del equipo no proporcionado.']);
}

// Cerrar la conexión
$conn->close();
?>