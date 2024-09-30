<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'conexion.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id) && isset($data->name) && isset($data->price) && isset($data->user_id)) {
    $id = $data->id;
    $name = $data->name;
    $price = $data->price;
    $user_id = $data->user_id; // Registrar quién editó (opcional)

    // Actualizar el nombre y el precio del producto
    $sql = "UPDATE products SET name = :name, price = :price WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':price', $price, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Producto editado correctamente"]);
    } else {
        echo json_encode(["message" => "Error al editar el producto"]);
    }
} else {
    echo json_encode(["message" => "Datos incompletos"]);
}
?>
