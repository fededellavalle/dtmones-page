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
    $id_cliente = $_POST['id_cliente'];
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $nacimiento = $_POST['nacimiento'];
    $id_posicion = $_POST['id_posicion'];
    $id_nacionalidad = $_POST['id_nacionalidad'];
    $id_equipo = $_POST['id_equipo'];
    $id_categoria = $_POST['id_categoria'];
    $link_eurobasket = $_POST['link_eurobasket'] ?? null;
    $imagen_actual = $_POST['imagen_actual'] ?? null;
    $youtube_links = $_POST['youtube_links'] ?? [];

    // Validar campos obligatorios
    if (empty($nombre) || empty($apellido) || empty($id_posicion) || empty($id_nacionalidad)) {
        $_SESSION['error'] = "Por favor, completa todos los campos obligatorios.";
        header("Location: /pages/login/edit-client.php?id=$id_cliente");
        exit;
    }

    // Verificar duplicados (nombre y apellido)
    $sql_verificar = "SELECT id FROM clientes WHERE nombre = ? AND apellido = ? AND id != ?";
    $stmt_verificar = $conn->prepare($sql_verificar);
    $stmt_verificar->bind_param("ssi", $nombre, $apellido, $id_cliente);
    $stmt_verificar->execute();
    $stmt_verificar->store_result();

    if ($stmt_verificar->num_rows > 0) {
        $_SESSION['error'] = "Ya existe un cliente con el mismo nombre y apellido.";
        header("Location: /pages/login/edit-client.php?id=$id_cliente");
        exit;
    }
    $stmt_verificar->close();

    // Obtener el nombre de la categoría para crear el directorio de imágenes
    $sql_categoria = "SELECT nombre_categoria FROM categoria WHERE id_categoria = ?";
    $stmt_categoria = $conn->prepare($sql_categoria);
    $stmt_categoria->bind_param("i", $id_categoria);
    $stmt_categoria->execute();
    $result_categoria = $stmt_categoria->get_result();
    $categoria = $result_categoria->fetch_assoc();

    if ($categoria) {
        $nombre_categoria = strtolower(str_replace(' ', '_', $categoria['nombre_categoria'])); // Convertir a minúsculas y reemplazar espacios
    } else {
        $_SESSION['error'] = "No se encontró la categoría para el cliente.";
        header("Location: /pages/login/edit-client.php?id=$id_cliente");
        exit;
    }

    // Validar y procesar la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $directorio_destino = '/assets/images/clients/' . $nombre_categoria . '/';
        $nombre_imagen = strtolower($nombre . '_' . $apellido) . '.png';
        $ruta_destino = $directorio_destino . $nombre_imagen;

        // Eliminar la imagen anterior si existe
        if ($imagen_actual && file_exists($_SERVER['DOCUMENT_ROOT'] . $imagen_actual)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $imagen_actual);
        }

        // Mover la imagen al directorio de destino
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $ruta_destino)) {
            $imagen_actual = $ruta_destino; // Actualizar $imagen_actual con la nueva imagen
        } else {
            $_SESSION['error'] = "Error al subir la imagen.";
            header("Location: /pages/login/edit-client.php?id=$id_cliente");
            exit;
        }
    }

    if (empty($imagen_actual)) {
        // Consulta segura para obtener la imagen actual
        $sql = "SELECT imagen FROM clientes WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // Si hay una imagen registrada, la conservamos
        if ($row) {
            $imagen_actual = $row['imagen'];
        }

        $stmt->close();
    }

    // Actualizar los datos del cliente
    $sql = "UPDATE clientes 
            SET nombre = ?, apellido = ?, nacimiento = ?, id_posicion = ?, id_nacionalidad = ?, id_equipo = ?, id_categoria = ?, link_eurobasket = ?, imagen = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiiissi", $nombre, $apellido, $nacimiento, $id_posicion, $id_nacionalidad, $id_equipo, $id_categoria, $link_eurobasket, $imagen_actual, $id_cliente);

    if (!$stmt->execute()) {
        $_SESSION['error'] = "Error al actualizar los datos: " . $stmt->error;
        header("Location: /pages/login/edit-client.php?id=$id_cliente");
        exit;
    }

    // Actualizar los videos de YouTube
    $conn->query("DELETE FROM videos WHERE id_cliente = $id_cliente");

    $stmtYtlink = $conn->prepare("INSERT INTO videos (id_cliente, link_video) VALUES (?, ?)");
    foreach ($youtube_links as $link) {
        if (!empty($link)) {
            $stmtYtlink->bind_param("is", $id_cliente, $link);
            $stmtYtlink->execute();
        }
    }

    $_SESSION['success'] = "Cliente actualizado exitosamente.";
    header("Location: /pages/login/dashboard.php");
    exit;
}

// Si no se recibe una solicitud POST, redirigir al dashboard
header("Location: /pages/login/dashboard.php");
exit;
?>