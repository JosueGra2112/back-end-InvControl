<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'conexion.php';

$sql = "SELECT u.id, u.username, r.role_name, p.can_create_product, p.can_edit_product, p.can_delete_product
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id
        LEFT JOIN permissions p ON u.id = p.user_id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($users);
?>
