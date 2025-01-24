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

// Procesar datos del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validar y limpiar datos
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $nacimiento = $conn->real_escape_string($_POST['nacimiento']);
    $id_posicion = (int) $_POST['id_posicion'];
    $id_nacionalidad = (int) $_POST['id_nacionalidad'];
    $altura = (float) $_POST['altura'];
    $id_equipo = (int) $_POST['id_equipo'];
    $link_eurobasket = $conn->real_escape_string($_POST['link_eurobasket']);
    $id_categoria = (int) $_POST['id_categoria'];
    $youtube_links = isset($_POST['youtube_links']) ? $_POST['youtube_links'] : [];

    // Verificar si ya existe un cliente con el mismo nombre y apellido
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM clientes WHERE nombre = ? AND apellido = ?");
    $check_stmt->bind_param("ss", $nombre, $apellido);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        $_SESSION['error'] = "Error: Ya existe un cliente con el nombre '$nombre' y apellido '$apellido'.";
        header("Location: /pages/login/add-client.php");
        exit;
    }

    // Obtener el nombre de la categoría
    $categoria_stmt = $conn->prepare("SELECT nombre_categoria FROM categoria WHERE id_categoria = ?");
    $categoria_stmt->bind_param("i", $id_categoria);
    $categoria_stmt->execute();
    $categoria_stmt->bind_result($nombre_categoria);
    $categoria_stmt->fetch();
    $categoria_stmt->close();

    if (empty($nombre_categoria)) {
        die("Error: La categoría seleccionada no existe.");
    }

    // Formatear el nombre de la imagen y la ruta de destino
    $imagen = $_FILES['imagen'];
    $image_name = strtolower("{$nombre}_{$apellido}.png"); // Formato deseado
    $target_dir = "../../../assets/images/clients/" . strtolower($nombre_categoria) . "/";

    // Crear la carpeta si no existe
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $image_file = $target_dir . $image_name;
    $image_type = strtolower(pathinfo($imagen["name"], PATHINFO_EXTENSION));

    // Validar que el archivo sea PNG
    if ($image_type !== 'png') {
        die("Error: Solo se permiten imágenes en formato PNG.");
    }

    // Mover el archivo con el nuevo nombre
    if (!move_uploaded_file($imagen['tmp_name'], $image_file)) {
        die("Error al subir la imagen.");
    }

    // Guardar la ruta relativa de la imagen
    $relative_image_path = "/assets/images/clients/" . strtolower($nombre_categoria) . "/" . $image_name;

    // Insertar cliente en la base de datos
    $stmt = $conn->prepare("
        INSERT INTO clientes 
        (nombre, apellido, nacimiento, id_posicion, id_nacionalidad, altura, id_equipo, imagen, link_eurobasket, id_categoria) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssiidsssi",
        $nombre,
        $apellido,
        $nacimiento,
        $id_posicion,
        $id_nacionalidad,
        $altura,
        $id_equipo,
        $relative_image_path,
        $link_eurobasket,
        $id_categoria
    );

    if ($stmt->execute()) {
        $cliente_id = $stmt->insert_id;

        // Guardar links de YouTube
        if (!empty($youtube_links)) {
            $youtube_stmt = $conn->prepare("
                INSERT INTO videos (id_cliente, link_video) VALUES (?, ?)
            ");
            foreach ($youtube_links as $link) {
                $link = $conn->real_escape_string($link);
                $youtube_stmt->bind_param("is", $cliente_id, $link);
                $youtube_stmt->execute();
            }
            $youtube_stmt->close();
        }

        $_SESSION['success'] = "Cliente agregado exitosamente.";
        header("Location: /pages/login/dashboard.php");
    } else {
        $_SESSION['error'] = "Error al guardar el cliente: " . $conn->error;
        header("Location: /pages/login/add-client.php");
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>