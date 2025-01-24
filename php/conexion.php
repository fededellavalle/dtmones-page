<?php
// Configuración de la conexión
$host = "localhost:3307";
$user = "root"; // Usuario por defecto de XAMPP
$password = ""; // Sin contraseña por defecto en XAMPP
$dbname = "clientesDB";

// Crear conexión
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
} else {
    echo "Conexión exitosa a la base de datos";
}
?>