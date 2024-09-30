<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include 'conexion.php';

$data = json_decode(file_get_contents("php://input"));

// Verificar si los datos necesarios están presentes
if (isset($data->name) && isset($data->price) && isset($data->stock) && isset($data->user_id)) {
    $name = $data->name;
    $price = $data->price;
    $stock = $data->stock;
    $user_id = $data->user_id;  // Agregar el ID del usuario

    try {
        // Iniciar la transacción
        $conn->beginTransaction();

        // Insertar el producto en la tabla 'products'
        $sql = "INSERT INTO products (name, price, stock) VALUES (:name, :price, :stock)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock);

        if ($stmt->execute()) {
            // Obtener el ID del producto recién creado
            $product_id = $conn->lastInsertId();

            // Insertar movimiento de inventario (entrada)
            $sqlMovement = "INSERT INTO stock_movements (product_id, type, quantity, movement_date, user_id) 
                            VALUES (:product_id, 'entry', :quantity, NOW(), :user_id)";
            $stmtMovement = $conn->prepare($sqlMovement);
            $stmtMovement->bindParam(':product_id', $product_id);
            $stmtMovement->bindParam(':quantity', $stock);
            $stmtMovement->bindParam(':user_id', $user_id);  // Guardar el ID del usuario

            if ($stmtMovement->execute()) {
                $conn->commit();
                echo json_encode(["message" => "Producto y movimiento registrados correctamente."]);
            } else {
                $conn->rollBack();
                echo json_encode(["message" => "Error al registrar el movimiento de inventario."]);
            }
        } else {
            echo json_encode(["message" => "Error al registrar el producto."]);
        }
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(["message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["message" => "Datos incompletos."]);
}
?>
