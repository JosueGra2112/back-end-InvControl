<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'conexion.php'; // Conexión a la base de datos

// Eliminar todos los movimientos de inventario
$sql = "DELETE FROM stock_movements";
$stmt = $conn->prepare($sql);

if ($stmt->execute()) {
    echo json_encode(["message" => "Inventario limpiado correctamente"]);
} else {
    echo json_encode(["message" => "Error al limpiar el inventario"]);
}
?>
