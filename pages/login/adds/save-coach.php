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
    $id_nacionalidad = (int) $_POST['id_nacionalidad'];
    $id_equipo = (int) $_POST['id_equipo'];
    $id_certificado = (int) $_POST['id_certificado'];
    $es_fiba = (int) $_POST['es_fiba'];
    $experiencias = isset($_POST['experiencias']) ? $_POST['experiencias'] : [];

    // Verificar si ya existe un coach con el mismo nombre y apellido
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM coaches WHERE nombre = ? AND apellido = ?");
    $check_stmt->bind_param("ss", $nombre, $apellido);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        $_SESSION['error'] = "Error: Ya existe un coach con el nombre '$nombre' y apellido '$apellido'.";
        header("Location: /pages/login/add-coach.php");
        exit;
    }

    // Formatear el nombre de la imagen y la ruta de destino
    $imagen = $_FILES['imagen'];
    $image_name = strtolower("{$nombre}_{$apellido}.png"); // Formato deseado
    $target_dir = "../../../assets/images/clients/coaches/";

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
    $relative_image_path = "/assets/images/clients/coaches/" . $image_name;

    // Insertar coach en la base de datos
    $stmt = $conn->prepare("
        INSERT INTO coaches (nombre, apellido, nacimiento, id_nacionalidad, id_equipo, imagen, id_certificado, es_fiba) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssissii", $nombre, $apellido, $nacimiento, $id_nacionalidad, $id_equipo, $relative_image_path, $id_certificado, $es_fiba);
    if ($stmt->execute()) {
        $coach_id = $stmt->insert_id; // Obtener el ID del coach recién insertado

        // Insertar experiencias
        foreach ($experiencias as $experiencia) {
            $experiencia = $conn->real_escape_string($experiencia);
            $stmt_exp = $conn->prepare("INSERT INTO experiencias (id_coach, descripcion) VALUES (?, ?)");
            $stmt_exp->bind_param("is", $coach_id, $experiencia);
            $stmt_exp->execute();
        }

        $_SESSION['success'] = "Coach agregado correctamente.";
        header("Location: /pages/login/dashboard.php");
        exit;
    } else {
        $_SESSION['error'] = "Error al guardar los datos del coach.";
        header("Location: /pages/login/add-coach.php");
        exit;
    }
}
?>