<?php
header("Access-Control-Allow-Origin: *"); // Permite solicitudes desde cualquier origen
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // Permite los métodos POST, GET y OPTIONS
header("Access-Control-Allow-Headers: Content-Type"); // Permite el encabezado Content-Type

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include 'conexion.php';

$data = json_decode(file_get_contents("php://input"));

// Verificar si se han enviado todos los datos necesarios
if (isset($data->user_id) && isset($data->can_create_product) && isset($data->can_edit_product) && isset($data->can_delete_product)) {
    $user_id = $data->user_id;
    $can_create_product = $data->can_create_product;
    $can_edit_product = $data->can_edit_product;
    $can_delete_product = $data->can_delete_product;

    // Verificar si el usuario ya tiene un registro en la tabla permissions
    $sql = "SELECT * FROM permissions WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // Actualizar permisos si ya existe un registro
        $sql_update = "UPDATE permissions 
                       SET can_create_product = :can_create_product, 
                           can_edit_product = :can_edit_product, 
                           can_delete_product = :can_delete_product 
                       WHERE user_id = :user_id";

        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bindParam(':can_create_product', $can_create_product, PDO::PARAM_BOOL);
        $stmt_update->bindParam(':can_edit_product', $can_edit_product, PDO::PARAM_BOOL);
        $stmt_update->bindParam(':can_delete_product', $can_delete_product, PDO::PARAM_BOOL);
        $stmt_update->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt_update->execute()) {
            echo json_encode(["message" => "Permisos actualizados correctamente"]);
        } else {
            echo json_encode(["message" => "Error al actualizar permisos"]);
        }
    } else {
        // Crear un nuevo registro de permisos si no existe
        $sql_insert = "INSERT INTO permissions (user_id, can_create_product, can_edit_product, can_delete_product) 
                       VALUES (:user_id, :can_create_product, :can_edit_product, :can_delete_product)";
        
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bindParam(':user_id', $user_id);
        $stmt_insert->bindParam(':can_create_product', $can_create_product, PDO::PARAM_BOOL);
        $stmt_insert->bindParam(':can_edit_product', $can_edit_product, PDO::PARAM_BOOL);
        $stmt_insert->bindParam(':can_delete_product', $can_delete_product, PDO::PARAM_BOOL);

        if ($stmt_insert->execute()) {
            echo json_encode(["message" => "Permisos creados correctamente"]);
        } else {
            echo json_encode(["message" => "Error al crear permisos"]);
        }
    }
} else {
    echo json_encode(["message" => "Datos incompletos"]);
}
?>
