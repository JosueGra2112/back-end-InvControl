<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'conexion.php'; // Incluye el archivo de conexión a la base de datos

// Consulta para obtener todos los productos
$sql = "SELECT * FROM products";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    // Obtener los productos como un arreglo asociativo
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Enviar los productos como JSON
    echo json_encode($products);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
