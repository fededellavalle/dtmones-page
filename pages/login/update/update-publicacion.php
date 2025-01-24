<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['username'])) {
    header("Location: /pages/login.php");
    exit;
}

header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id_publicacion'], $data['link_publicacion']) || empty($data['id_publicacion']) || empty($data['link_publicacion'])) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

$id = $data['id_publicacion'];
$link = $data['link_publicacion'];

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

// Verificar duplicados (excepto el actual)
$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM publicaciones_instagram WHERE link_publicacion = ? AND id_publicacion != ?");
$stmt->bind_param("si", $link, $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    echo json_encode(['success' => false, 'message' => 'Este link ya existe']);
    exit;
}

// Actualizar publicación
$stmt = $conn->prepare("UPDATE publicaciones_instagram SET link_publicacion = ? WHERE id_publicacion = ?");
$stmt->bind_param("si", $link, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el link']);
}

$conn->close();
