<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'conexion.php'; // Conexión a la base de datos

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id)) {
    $id = $data->id;

    // Eliminar movimiento de stock por ID
    $sql = "DELETE FROM stock_movements WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Movimiento eliminado correctamente"]);
    } else {
        echo json_encode(["message" => "Error al eliminar el movimiento"]);
    }
} else {
    echo json_encode(["message" => "ID no proporcionado"]);
}
?>
