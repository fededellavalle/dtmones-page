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

// Verificar si se proporcionó un ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $cliente_id = (int) $_GET['id'];

    // Obtener la información del cliente antes de eliminar
    $select_client_stmt = $conn->prepare("SELECT nombre, apellido, imagen FROM coaches WHERE id_coach = ?");
    $select_client_stmt->bind_param("i", $cliente_id);
    $select_client_stmt->execute();
    $select_client_stmt->bind_result($nombre, $apellido, $imagen);
    $select_client_stmt->fetch();
    $select_client_stmt->close();

    // Verificar si el cliente existe
    if (empty($nombre) || empty($apellido)) {
        $_SESSION['error'] = "Cliente no encontrado.";
        header("Location: /pages/login/dashboard.php");
        exit;
    }

    // Construir la ruta completa de la imagen
    $image_path = $_SERVER['DOCUMENT_ROOT'] . $imagen;

    // Eliminar las experiencies relacionados al cliente
    $delete_experiencies_stmt = $conn->prepare("DELETE FROM experiencias WHERE id_coach = ?");
    $delete_experiencies_stmt->bind_param("i", $cliente_id);

    if (!$delete_experiencies_stmt->execute()) {
        $_SESSION['error'] = "Error al eliminar las experiencias: " . $conn->error;
        header("Location: /pages/login/dashboard.php");
        exit;
    }

    $delete_experiencies_stmt->close();

    // Eliminar el cliente
    $delete_client_stmt = $conn->prepare("DELETE FROM coaches WHERE id_coach = ?");
    $delete_client_stmt->bind_param("i", $cliente_id);

    if ($delete_client_stmt->execute()) {
        // Eliminar la imagen del servidor
        if (file_exists($image_path)) {
            if (unlink($image_path)) {
                $_SESSION['success'] = "Cliente y su imagen fueron eliminados exitosamente.";
            } else {
                $_SESSION['error'] = "Cliente eliminado, pero no se pudo eliminar la imagen.";
            }
        } else {
            $_SESSION['success'] = "Cliente eliminado exitosamente. La imagen no existe o ya fue eliminada.";
        }
    } else {
        $_SESSION['error'] = "Error al eliminar el cliente: " . $conn->error;
    }

    $delete_client_stmt->close();
} else {
    $_SESSION['error'] = "ID de cliente inválido.";
}

$conn->close();

// Redirigir al dashboard o página principal
header("Location: /pages/login/dashboard.php");
exit;
?>