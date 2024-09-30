<?php
// Definir los parámetros de conexión
$host = "localhost"; // Servidor donde está la base de datos
$dbname = "inventory_system"; // Nombre de la base de datos
$username = "root"; // Usuario de MySQL (en XAMPP el usuario por defecto es 'root')
$password = ""; // Contraseña (en XAMPP el password por defecto es vacío)

// Crear la conexión utilizando PDO
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Establecer el modo de error PDO a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conexión exitosa"; // Descomentar para verificar conexión
} catch (PDOException $e) {
    // Mostrar error en caso de fallo de conexión
    echo "Error de conexión: " . $e->getMessage();
}
?>
