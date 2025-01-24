<?php
// Configuración de conexión a la base de datos
$host = "localhost:3307";
$user = "root";
$password = "";
$dbname = "clientesdb";

$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Error de conexión: " . $conn->connect_error]));
}

// Consultar publicaciones de Instagram
$sql = "SELECT id_publicacion, link_publicacion FROM publicaciones_instagram
ORDER BY id_publicacion DESC";
$result = $conn->query($sql);

$publicaciones = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $link_original = $row['link_publicacion'];
        $link_embed = $link_original . 'embed';
        $publicaciones[] = [
            'id' => $row['id_publicacion'],
            'link_original' => $link_original,
            'link_embed' => $link_embed
        ];
    }
    echo json_encode(["status" => "success", "data" => $publicaciones]);
} else {
    echo json_encode(["status" => "error", "message" => "No se encontraron publicaciones."]);
}

$conn->close();
?>