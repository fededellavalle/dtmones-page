<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['username'])) {
    header("Location: /pages/login.php");
    exit;
}

header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['nombre_certificado']) || empty($data['nombre_certificado'])) {
    echo json_encode(['success' => false, 'message' => 'Link inválido']);
    exit;
}

$link = $data['nombre_certificado'];

// Conexión a la base de datos
$host = "localhost:3307";
$user = "root";
$password = "";
$dbname = "clientesdb";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

// Verificar el número de publicaciones existentes
$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM publicaciones_instagram");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] >= 9) {
    echo json_encode(['success' => false, 'message' => 'Solo puedes tener hasta 9 publicaciones']);
    exit;
}

// Verificar duplicados
$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM publicaciones_instagram WHERE link_publicacion = ?");
$stmt->bind_param("s", $link);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    echo json_encode(['success' => false, 'message' => 'Este link ya existe']);
    exit;
}

// Insertar nueva publicación
$stmt = $conn->prepare("INSERT INTO publicaciones_instagram (link_publicacion) VALUES (?)");
$stmt->bind_param("s", $link);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al insertar el link']);
}

$conn->close();
