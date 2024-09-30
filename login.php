<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'conexion.php';

$data = json_decode(file_get_contents("php://input"));

// Validar si se enviaron usuario y contraseña
if (isset($data->username) && isset($data->password)) {
    $username = $data->username;
    $password = md5($data->password); // Asegúrate de que la contraseña esté encriptada en MD5

    // Consultar si el usuario existe y si la contraseña es correcta
    $sql = "SELECT u.id, u.username, r.role_name, p.can_create_product, p.can_edit_product, p.can_delete_product 
            FROM users u 
            LEFT JOIN roles r ON u.role_id = r.id 
            LEFT JOIN permissions p ON u.id = p.user_id
            WHERE u.username = :username AND u.password = :password";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si es empleado, verificar si tiene permisos concedidos
        if ($user['role_name'] === 'Empleado' && !$user['can_create_product'] && !$user['can_edit_product'] && !$user['can_delete_product']) {
            echo json_encode(["message" => "Acceso denegado. El administrador aún no te ha concedido permisos."]);
        } else {
            echo json_encode(["message" => "Acceso concedido", "user" => $user]);
        }
    } else {
        echo json_encode(["message" => "Usuario o contraseña incorrectos"]);
    }
} else {
    echo json_encode(["message" => "Datos incompletos"]);
}
?>
