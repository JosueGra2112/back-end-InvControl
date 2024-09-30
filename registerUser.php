<?php
// Permitir el acceso desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permitir los métodos específicos POST, GET y OPTIONS
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
// Permitir el encabezado Content-Type
header("Access-Control-Allow-Headers: Content-Type");

// Incluir el archivo de conexión
include 'conexion.php';

// Manejar solicitud OPTIONS (preflight request)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Devuelve una respuesta exitosa para las solicitudes OPTIONS
    exit;
}

// Recibir los datos del registro en formato JSON
$data = json_decode(file_get_contents("php://input"));

// Validar que los campos están completos
if (isset($data->username) && isset($data->password)) {
    // Verificar si el usuario ya existe en la base de datos
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $data->username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // El usuario ya existe, devolver un mensaje de error
        echo json_encode(["message" => "El usuario ya existe"]);
    } else {
        // Encriptar la contraseña
        $hashed_password = md5($data->password);

        // Insertar el nuevo usuario en la base de datos con rol de "Empleado"
        $sql = "INSERT INTO users (username, password, role_id) 
                VALUES (:username, :password, (SELECT id FROM roles WHERE role_name = 'Empleado'))";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $data->username);
        $stmt->bindParam(':password', $hashed_password);

        if ($stmt->execute()) {
            // Registro exitoso
            echo json_encode(["message" => "Usuario registrado con éxito"]);
        } else {
            // Error en la inserción del usuario
            echo json_encode(["message" => "Error al registrar usuario"]);
        }
    }
} else {
    // Faltan datos en la solicitud
    echo json_encode(["message" => "Datos incompletos"]);
}
?>
