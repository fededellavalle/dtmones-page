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

// Validar datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_coach = $_POST['id_coach'];
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $nacimiento = $_POST['nacimiento'];
    $id_equipo = $_POST['id_equipo'];
    $id_nacionalidad = $_POST['id_nacionalidad'];
    $id_certificado = $_POST['id_certificado'];
    $es_fiba = $_POST['es_fiba'];
    $experiencias = $_POST['experiencias'] ?? [];
    $imagen_actual = $_POST['imagen_actual'] ?? null;

    if (empty($nombre) || empty($apellido) || empty($id_equipo) || empty($id_nacionalidad)) {
        $_SESSION['error'] = "Por favor, completa todos los campos obligatorios.";
        header("Location: /pages/login/edit-coach.php?id=$id_coach");
        exit;
    }

    // Verificar duplicados
    $sql_verificar = "SELECT id_coach FROM coaches WHERE nombre = ? AND apellido = ? AND id_coach != ?";
    $stmt_verificar = $conn->prepare($sql_verificar);
    $stmt_verificar->bind_param("ssi", $nombre, $apellido, $id_coach);
    $stmt_verificar->execute();
    $stmt_verificar->store_result();

    if ($stmt_verificar->num_rows > 0) {
        $_SESSION['error'] = "Ya existe un coach con el mismo nombre y apellido.";
        header("Location: /pages/login/edit-coach.php?id=$id_coach");
        exit;
    }
    $stmt_verificar->close();

    // Manejar imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $directorio_destino = '/assets/images/clients/coaches/';
        $nombre_imagen = strtolower($nombre . '_' . $apellido) . '.png';
        $ruta_destino = $directorio_destino . $nombre_imagen;

        // Eliminar la imagen anterior
        if ($imagen_actual && file_exists($_SERVER['DOCUMENT_ROOT'] . $imagen_actual)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $imagen_actual);
        }

        // Subir la nueva imagen
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $ruta_destino)) {
            $imagen_actual = $ruta_destino; // Actualizar imagen
        } else {
            $_SESSION['error'] = "Error al subir la imagen.";
            header("Location: /pages/login/edit-coach.php?id=$id_coach");
            exit;
        }
    }

    // Obtener la imagen actual si no se subió una nueva
    if (empty($imagen_actual)) {
        $sql = "SELECT imagen FROM coaches WHERE id_coach = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_coach);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            $imagen_actual = $row['imagen'];
        }
        $stmt->close();
    }

    // Actualizar datos
    $sql = "UPDATE coaches 
            SET nombre = ?, apellido = ?, nacimiento = ?, id_equipo = ?, id_nacionalidad = ?, id_certificado = ?, es_fiba = ?, imagen = ? 
            WHERE id_coach = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssisi", $nombre, $apellido, $nacimiento, $id_equipo, $id_nacionalidad, $id_certificado, $es_fiba, $imagen_actual, $id_coach);

    if (!$stmt->execute()) {
        $_SESSION['error'] = "Error al actualizar los datos: " . $stmt->error;
        header("Location: /pages/login/edit-coach.php?id=$id_coach");
        exit;
    }

    // Actualizar experiencias
    $conn->query("DELETE FROM experiencias WHERE id_coach = $id_coach");
    $stmtExp = $conn->prepare("INSERT INTO experiencias (id_coach, descripcion) VALUES (?, ?)");
    foreach ($experiencias as $exp) {
        if (!empty($exp)) {
            $stmtExp->bind_param("is", $id_coach, $exp);
            $stmtExp->execute();
        }
    }

    $_SESSION['success'] = "Coach actualizado exitosamente.";
    header("Location: /pages/login/dashboard.php");
    exit;
}
// Si no se recibe una solicitud POST, redirigir al dashboard
header("Location: /pages/login/dashboard.php");
exit;
?>