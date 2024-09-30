<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'conexion.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id) && isset($data->newStock) && isset($data->user_id)) {
    $id = $data->id;
    $newStock = (int)$data->newStock;  // Asegurarse de que sea un entero
    $user_id = $data->user_id;
    $soldQuantity = $data->soldQuantity;  // Cantidad vendida

    // Iniciar transacción
    $conn->beginTransaction();

    try {
        // Actualizar el stock en la tabla 'products'
        $sql = "UPDATE products SET stock = :newStock WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':newStock', $newStock, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Insertar el movimiento en la tabla 'stock_movements'
        $movementSql = "INSERT INTO stock_movements (product_id, type, quantity, movement_date, user_id) 
                        VALUES (:product_id, 'exit', :quantity, NOW(), :user_id)";
        $movementStmt = $conn->prepare($movementSql);
        $movementStmt->bindParam(':product_id', $id, PDO::PARAM_INT);
        $movementStmt->bindParam(':quantity', $soldQuantity, PDO::PARAM_INT); // Cantidad vendida
        $movementStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $movementStmt->execute();

        // Confirmar la transacción
        $conn->commit();

        echo json_encode(["message" => "Venta realizada correctamente, stock actualizado"]);
    } catch (Exception $e) {
        // Revertir la transacción si ocurre un error
        $conn->rollBack();
        echo json_encode(["message" => "Error al realizar la venta: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["message" => "Datos incompletos"]);
}
?>
