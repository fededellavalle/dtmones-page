<?php
// Conexión a la base de datos
$host = "localhost:3307"; // Cambiar si es necesario
$user = "root"; // Usuario de la base de datos
$password = ""; // Contraseña de la base de datos
$dbname = "clientesdb"; // Nombre de la base de datos

$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si se pasó una categoría como parámetro en la URL
$categoria = isset($_GET['categoria']) ? $conn->real_escape_string($_GET['categoria']) : null;

// Construir la consulta SQL
$sql = "
    SELECT 
        c.id AS id, 
        c.nombre AS jugador_nombre,
        c.apellido AS jugador_apellido,
        n.nombre_nacionalidad AS nacionalidad, 
        p.nombre_posicion AS posicion,
        p.abreviacion_posicion AS abreviacion_posicion,
        cat.nombre_categoria AS categoria,
        e.nombre_equipo AS equipo,
        c.imagen
    FROM clientes c
    INNER JOIN nacionalidad n ON c.id_nacionalidad = n.id_nacionalidad
    INNER JOIN posicion p ON c.id_posicion = p.id_posicion
    INNER JOIN categoria cat ON c.id_categoria = cat.id_categoria
    INNER JOIN equipo e ON c.id_equipo = e.id_equipo    
";

// Agregar filtro por categoría si se pasó como parámetro
if ($categoria) {
    $sql .= " WHERE cat.nombre_categoria = '$categoria'
    ORDER BY c.nombre ASC, c.apellido ASC";
}

$result = $conn->query($sql);

// Procesar el resultado
$jugadores = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jugadores[] = $row;
    }
}

// Enviar los datos al frontend en formato JSON
header('Content-Type: application/json');
echo json_encode($jugadores);

$conn->close();
?>