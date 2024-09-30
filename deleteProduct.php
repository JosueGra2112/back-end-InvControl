<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'conexion.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id) && isset($data->user_id)) {
    $id = $data->id;
    $user_id = $data->user_id; // Registrar quién eliminó (opcional)

    // Eliminar el producto
    $sql = "DELETE FROM products WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Producto eliminado correctamente"]);
    } else {
        echo json_encode(["message" => "Error al eliminar el producto"]);
    }
} else {
    echo json_encode(["message" => "Datos incompletos"]);
}
?>
