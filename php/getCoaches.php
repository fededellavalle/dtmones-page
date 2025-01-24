<?php
// Conexión a la base de datos
$host = "localhost:3307"; // Cambiar si es necesario
$user = "root"; // Usuario de la base de datos
$password = ""; // Contraseña de la base de datos
$dbname = "clientesdb"; // Cambiar al nombre real de la base de datos

$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Construir la consulta SQL para obtener coaches con nacionalidad, equipo, certificado y experiencias
$sql = "
    SELECT 
        c.id_coach AS id_coach,
        c.nombre AS nombre,
        c.apellido AS apellido,
        n.nombre_nacionalidad AS nacionalidad,
        e.nombre_equipo AS equipo,
        c.nacimiento AS nacimiento,
        c.imagen AS imagen,
        cf.nombre_certificado AS certificado,
        c.es_fiba AS es_fiba,
        GROUP_CONCAT(exp.descripcion SEPARATOR '|') AS experiencias
    FROM coaches c
    LEFT JOIN nacionalidad n ON c.id_nacionalidad = n.id_nacionalidad
    LEFT JOIN equipo e ON c.id_equipo = e.id_equipo
    LEFT JOIN certificados cf ON c.id_certificado = cf.id_certificado
    LEFT JOIN experiencias exp ON c.id_coach = exp.id_coach
    GROUP BY c.id_coach, c.nombre, c.apellido, n.nombre_nacionalidad, e.nombre_equipo, c.nacimiento, c.imagen, cf.nombre_certificado, c.es_fiba
    ORDER BY c.nombre ASC, c.apellido ASC
";

$result = $conn->query($sql);

// Procesar el resultado
$coaches = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Dividir las experiencias en un array si existen
        if (!empty($row['experiencias'])) {
            $row['experiencias'] = explode('|', $row['experiencias']);
        } else {
            $row['experiencias'] = [];
        }
        $coaches[] = $row;
    }
}

// Enviar los datos al frontend en formato JSON
header('Content-Type: application/json');
echo json_encode($coaches);

$conn->close();
?>