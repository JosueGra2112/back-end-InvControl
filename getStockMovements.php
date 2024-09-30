<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'conexion.php';

// Verificar si la solicitud es de tipo OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Consultar los movimientos de inventario, incluyendo el nombre del usuario que realizó el movimiento
$sql = "SELECT sm.id, p.name AS product_name, sm.type, sm.quantity, sm.movement_date, u.username AS user_name
        FROM stock_movements sm 
        LEFT JOIN products p ON sm.product_id = p.id
        LEFT JOIN users u ON sm.user_id = u.id
        ORDER BY sm.movement_date DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$movements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver los movimientos en formato JSON
if ($movements) {
    echo json_encode($movements);
} else {
    echo json_encode([]);
}
?>
