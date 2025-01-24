<?php
// Conexión a la base de datos
$host = "localhost:3307";
$user = "root"; // Usuario por defecto de XAMPP
$password = ""; // Sin contraseña por defecto en XAMPP
$dbname = "clientesdb";

$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consultar jugadores con sus relaciones (JOINs) y aplicar filtros si es necesario
$sql = "
    SELECT
        j.id,
        j.nombre AS nombre,
        j.apellido AS apellido,
        n.nombre_nacionalidad AS nacionalidad, 
        e.nombre_equipo AS equipo,
        j.imagen,
        'Jugador' AS tipo
    FROM clientes j
    INNER JOIN nacionalidad n ON j.id_nacionalidad = n.id_nacionalidad
    INNER JOIN equipo e ON j.id_equipo = e.id_equipo
    
    UNION ALL
    
    SELECT 
        c.id_coach AS id,
        c.nombre AS nombre,
        c.apellido AS apellido,
        n.nombre_nacionalidad AS nacionalidad, 
        e.nombre_equipo AS equipo,
        c.imagen,
        'Coach' AS tipo
    FROM coaches c
    INNER JOIN nacionalidad n ON c.id_nacionalidad = n.id_nacionalidad
    INNER JOIN equipo e ON c.id_equipo = e.id_equipo
    
    ORDER BY RAND()
    LIMIT 15
";




$result = $conn->query($sql);

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